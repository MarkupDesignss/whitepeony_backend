<?php

namespace App\Services;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use Illuminate\Support\Facades\Log;

class PaypalService
{
    private PayPalHttpClient $client;

    public function __construct()
    {
        $paypalConfig = config('paypal');
        $mode = $paypalConfig['mode'] ?? 'sandbox';

        $environment = $mode === 'live'
            ? new ProductionEnvironment($paypalConfig['client_id'], $paypalConfig['secret'])
            : new SandboxEnvironment($paypalConfig['client_id'], $paypalConfig['secret']);

        $this->client = new PayPalHttpClient($environment);
    }

    // Create a new PayPal order
    // public function createPayment(float $amount, string $currency = 'USD', string $description = 'Payment through PayPal', array $returnData = [])
    // {
    //     $request = new OrdersCreateRequest();
    //     $request->prefer('return=representation');
    //     $request->body = [
    //         "intent" => "CAPTURE",
    //         "purchase_units" => [[
    //             "amount" => [
    //                 "value" => number_format($amount, 2, '.', ''),
    //                 "currency_code" => $currency
    //             ],
    //             "description" => $description
    //         ]],
    //         "application_context" => [
    //             "cancel_url" => route('api.paypal.status', $returnData),
    //             "return_url" => route('api.paypal.status', $returnData)
    //         ]
    //         ];

    //     try {
    //         $response = $this->client->execute($request);

    //         foreach ($response->result->links as $link) {
    //             if ($link->rel === 'approve') {
    //                 return $link->href;
    //             }
    //         }

    //         Log::error('PayPal Create Payment: No approval URL found.');
    //         return null;

    //     } catch (\Exception $ex) {
    //         Log::error('PayPal Create Payment Error: ' . $ex->getMessage());
    //         if (method_exists($ex, 'getData')) {
    //             Log::error(json_encode($ex->getData(), JSON_PRETTY_PRINT));
    //         }
    //         return null;
    //     }
    // }

    public function createPayment(float $amount, string $currency = 'USD', string $description = 'Payment through PayPal', array $returnData = [])
{
    $type = $returnData['type'] ?? 'order'; // default = order

    // Different routes for order vs event
    $statusRoute = $type === 'event'
        ? route('api.paypal.event.status', $returnData)
        : route('api.paypal.status', $returnData);

    $request = new OrdersCreateRequest();
    $request->prefer('return=representation');
    $request->body = [
        "intent" => "CAPTURE",
        "purchase_units" => [[
            "amount" => [
                "value" => number_format($amount, 2, '.', ''),
                "currency_code" => $currency
            ],
            "description" => $description
        ]],
        "application_context" => [
            "cancel_url" => $statusRoute,
            "return_url" => $statusRoute
        ]
    ];

    try {
        $response = $this->client->execute($request);

        foreach ($response->result->links as $link) {
            if ($link->rel === 'approve') {
                return $link->href;
            }
        }

        Log::error('PayPal Create Payment: No approval URL found.');
        return null;

    } catch (\Exception $ex) {
        Log::error('PayPal Create Payment Error: ' . $ex->getMessage());
        if (method_exists($ex, 'getData')) {
            Log::error(json_encode($ex->getData(), JSON_PRETTY_PRINT));
        }
        return null;
    }
}


    // Capture PayPal order after user approves it
    public function executePayment(string $orderId)
    {
        $request = new OrdersCaptureRequest($orderId);
        $request->prefer('return=representation');

        try {
            $response = $this->client->execute($request);

            if (in_array($response->statusCode, [200, 201])) {
                return [
                    'success' => true,
                    'data' => [
                        'order_id' => $orderId,
                        'status' => $response->result->status,
                        'amount' => $response->result->purchase_units[0]->payments->captures[0]->amount->value ?? null,
                        'currency' => $response->result->purchase_units[0]->payments->captures[0]->amount->currency_code ?? null,
                    ]
                ];
            }

            return ['success' => false, 'message' => 'Payment not approved'];
        } catch (\Exception $ex) {
            Log::error('PayPal Execute Payment Error: ' . $ex->getMessage());
            return ['success' => false, 'message' => 'Payment execution failed'];
        }
    }
}