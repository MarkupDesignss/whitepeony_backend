<?php
namespace App\Services;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }
    /**
     * Create Stripe Checkout Session
     */
    public function createPayment(
        float $amount,
        string $currency = 'USD',
        string $description = 'Payment through Stripe',
        array $returnData = []
    ) {
        try {
            $type = $returnData['type'] ?? 'order';
            $successUrl = $type === 'event'
                ? route('api.stripe.event.status', $returnData)
                : route('api.stripe.status', $returnData);
            $cancelUrl = $successUrl;
            // Ensure we append session id with correct query separator
            $separator = strpos($successUrl, '?') !== false ? '&' : '?';
            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($currency),
                        'unit_amount' => (int) round($amount * 100), // Stripe works in cents
                        'product_data' => [
                            'name' => $description,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'success_url' => $successUrl . $separator . 'session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
            ]);
            return $session->url;
        } catch (\Exception $e) {
            Log::error('Stripe Create Payment Error: ' . $e->getMessage());
            return null;
        }
    }
    /**
     * Verify Payment after redirect
     */
    public function executePayment(string $sessionId)
    {
        try {
            $session = Session::retrieve($sessionId);
            $paymentIntent = PaymentIntent::retrieve($session->payment_intent);
            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'data' => [
                        'transaction_id' => $paymentIntent->id,
                        'status' => $paymentIntent->status,
                        'amount' => $paymentIntent->amount_received / 100,
                        'currency' => strtoupper($paymentIntent->currency),
                    ]
                ];
            }
            return ['success' => false, 'message' => 'Payment not successful'];
        } catch (\Exception $e) {
            Log::error('Stripe Execute Payment Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Payment verification failed'];
        }
    }
}