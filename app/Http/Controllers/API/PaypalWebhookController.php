<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaypalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('PayPal Webhook received:', $payload);

        try {
            // 1️⃣ Verify webhook signature
            $signatureVerification = $this->verifyWebhookSignature($request);
            if (!$signatureVerification) {
                Log::warning('Invalid PayPal webhook signature.');
                return response()->json(['status' => 'invalid signature'], 400);
            }

            // 2️⃣ Handle event types
            $eventType = $payload['event_type'] ?? null;
            $resource = $payload['resource'] ?? [];

            if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
                $paypalOrderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;
                $amount = $resource['amount']['value'] ?? null;

                if ($paypalOrderId) {
                    // ✅ Update order table if found
                    $orderUpdated = DB::table('orders')
                        ->where('paypal_order_id', $paypalOrderId)
                        ->update([
                            'payment_status' => 'paid',
                            'status' => 'placed',
                            'updated_at' => now(),
                        ]);

                    // ✅ Or update event registration table if found
                    if (!$orderUpdated) {
                        DB::table('event_registrations')
                            ->where('paypal_order_id', $paypalOrderId)
                            ->update([
                                'payment_status' => 'paid',
                                'updated_at' => now(),
                            ]);
                    }
                }
            }

            if ($eventType === 'PAYMENT.CAPTURE.REFUNDED') {
                // Handle refunds if needed
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Throwable $th) {
            Log::error('PayPal Webhook Error: ' . $th->getMessage());
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    private function verifyWebhookSignature(Request $request): bool
    {
        // Optional: Add PayPal SDK signature verification here if you want full security.
        // For sandbox, you can skip temporarily.
        return true;
    }
}
