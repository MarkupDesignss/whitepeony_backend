<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Helpers\Utilities;
use App\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\PaypalService;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedMail;
use Illuminate\Support\Str;
use App\Services\StripeService;
use Stripe\Stripe;
use Stripe\Token;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Cache; 
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function orders()
    {
        try {
            $userId = Auth::user()->id;

            // ✅ Fetch orders with product, variants & reviews + reviewer (customer)
            $orders = Order::with([
                'items.product.variants',
                'items.product.reviews.customer'
            ])
            ->where('customer_id', $userId)
            ->get();

            $ordersTransformed = $orders->map(function ($order) {

                $order->items = $order->items->map(function ($item) {

                    $product = $item->product;

                    if ($product) {

                        // ✅ Decode category IDs
                        $categoryIds = json_decode($product->category_id, true) ?: [];

                        // ✅ Fetch category names
                        $categoryNames = DB::table('categories')
                            ->whereIn('id', $categoryIds)
                            ->pluck('name')
                            ->toArray();

                        // ✅ Rating Summary
                        $averageRating = round($product->reviews->avg('rating') ?? 0, 1);
                        $reviewCount   = $product->reviews->count();

                        // ✅ Add formatted product data
                        $item->product = [
                            'id' => $product->id,
                            'name' => $product->name,
                            'description' => $product->description,
                            'product_type' => $product->product_type,
                            'front_image' => $product->front_image,
                            'back_image' => $product->back_image,
                            'side_image' => $product->side_image,
                            'category_id' => $categoryIds,
                            'categories' => $categoryNames,
                            'average_rating' => $averageRating,
                            'review_count' => $reviewCount,
                            'variants' => $product->variants,
                            'reviews' => $product->reviews // ✅ Reviews Included
                        ];

                    } else {
                        $item->product = null;
                    }

                    return $item;
                });

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Orders fetched successfully',
                'orders'  => $ordersTransformed
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function showShiping()
    {
        $shippings = ShippingMethod::all();
         return response()->json([
                'success' => true,
                'message' => 'Shipping methods fetched successfully',
                'error'   => $shippings
            ], 200);
    }
    
    /**
         * Convert an amount from a given currency to CZK using CurrencyAPI.
    */
    private function convertToCzk(float $amount, string $fromCurrency): float
    {
        $fromCurrency = strtolower($fromCurrency);
        if ($fromCurrency === 'czk') {
            return round($amount, 2);
        }

        $rate = $this->getExchangeRateToCzk($fromCurrency);
        return round($amount * $rate, 2);
    }

    /**
     * Get exchange rate (1 EUR/USD = ? CZK) from CurrencyAPI with caching.
     */
    private function getExchangeRateToCzk(string $fromCurrency): float
    {
        $cacheKey = "currencyapi_rate_{$fromCurrency}_to_czk";
        return Cache::remember($cacheKey, 3600, function () use ($fromCurrency) {
            $apiKey = env('CURRENCY_API_KEY', 'cur_live_CPIorlc0TVwjhAEzqPEiSA2wh86miRJFw4jNnfOC');
            $response = Http::get("https://api.currencyapi.com/v3/latest", [
                'apikey' => $apiKey,
                'base_currency' => 'USD',
                'currencies' => 'CZK,' . strtoupper($fromCurrency),
            ]);

            if ($response->failed()) {
                Log::error('CurrencyAPI request failed', ['status' => $response->status()]);
                throw new \Exception('Currency conversion service unavailable');
            }

            $data = $response->json();
            $rates = $data['data'] ?? [];

            $czkRate = $rates['CZK']['value'] ?? null;
            if (!$czkRate) {
                throw new \Exception('CZK rate not found');
            }

            // If converting from USD, rate is directly CZK per USD
            if (strtolower($fromCurrency) === 'usd') {
                return $czkRate;
            }

            // For EUR (or other currencies), get rate vs USD
            $targetRate = $rates[strtoupper($fromCurrency)]['value'] ?? null;
            if (!$targetRate) {
                throw new \Exception("Rate for {$fromCurrency} not found");
            }

            // CZK per EUR = (CZK/USD) / (EUR/USD)
            return $czkRate / $targetRate;
        });
    }

    public function placeOrder(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('Place Order Request', $request->all());

            // Convert currency to lowercase for validation
            $request->merge([
                'currency' => strtolower($request->currency)
            ]);

            $request->validate([
                'cart_id'     => 'required|exists:carts,id',
                'address_id'  => 'nullable|exists:addresses,id',
                'shipping_id' => 'required|exists:shipping_methods,id',
                'promo_code'  => 'nullable|string|exists:promocodes,coupon_code',
                'currency'    => 'required|string|in:czk,eur,usd',
                'grand_total' => 'required|numeric|min:0',
            ]);

            $user = Auth::user();
            if (!$user) {
                Log::warning('Unauthorized order attempt');
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            if ($user->status === 'blocked') {
                Log::warning('Blocked user order attempt', ['user_id' => $user->id]);
                return response()->json(['success' => false, 'message' => 'Your account is blocked.'], 403);
            }

            $customerId = $user->id;

            // ---------- Address ----------
            $address = DB::table('addresses')
                ->where('customer_id', $customerId)
                ->when($request->address_id, fn($q) => $q->where('id', $request->address_id))
                ->first();

            if (!$address) {
                return response()->json(['success' => false, 'message' => 'Please add/select address'], 400);
            }

            // ---------- Shipping ----------
            $shipping = DB::table('shipping_methods')
                ->where('id', $request->shipping_id)
                ->where('is_active', 1)
                ->first();

            if (!$shipping) {
                return response()->json(['success' => false, 'message' => 'Invalid shipping method'], 400);
            }

            // ---------- Cart ----------
            $cart = DB::table('carts')
                ->where('id', $request->cart_id)
                ->where('customer_id', $customerId)
                ->first();

            if (!$cart) {
                return response()->json(['success' => false, 'message' => 'Cart not found'], 404);
            }

            $cartItems = DB::table('cart_items')
                ->where('cart_id', $cart->id)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
            }

            // ---------- Stock Check & Update ----------
            foreach ($cartItems as $item) {
                $variant = DB::table('product_variants')
                    ->where('id', $item->variant_id)
                    ->lockForUpdate()
                    ->first();

                if (!$variant || $variant->stock < $item->quantity) {
                    DB::rollBack();
                    Log::error('Stock insufficient', ['variant_id' => $item->variant_id]);
                    return response()->json(['success' => false, 'message' => 'Insufficient stock for product'], 400);
                }

                DB::table('product_variants')
                    ->where('id', $variant->id)
                    ->update(['stock' => $variant->stock - $item->quantity]);
            }

            // ---------- Totals (only for promo code calculation, not for validation) ----------
            $productsTotal = $cartItems->sum('total_price');
            $totalAmount   = $productsTotal + $shipping->cost;
            $discount      = 0;
            $finalTotalCzk = $totalAmount; // server-side total in CZK (used only for promo)

            // ---------- Promo Code ----------
            if ($request->filled('promo_code')) {
                $promo = DB::table('promocodes')
                    ->where('coupon_code', $request->promo_code)
                    ->first();

                if (!$promo ||
                    $promo->customer_type !== $user->type ||
                    ($promo->end_date && now()->gt($promo->end_date)) ||
                    ($promo->usage_limit && $promo->used_count >= $promo->usage_limit)
                ) {
                    DB::rollBack();
                    Log::error('Invalid or exhausted Promo Code', ['code' => $request->promo_code]);
                    return response()->json(['success' => false, 'message' => 'Invalid or expired promo code'], 400);
                }

                $discount = $promo->discount_type === 'percentage'
                    ? ($promo->discount_value / 100) * $totalAmount
                    : $promo->discount_value;

                $discount = min($discount, $promo->max_discount ?? $totalAmount, $totalAmount);
                $finalTotalCzk = $totalAmount - $discount;

                DB::table('promocodes')->where('id', $promo->id)->increment('used_count');
            }

            // ---------- Currency Conversion (YOUR EXACT REQUIREMENT) ----------
            $frontendCurrency = strtolower($request->currency);
            $frontendAmount   = (float) $request->grand_total;

            // Convert to CZK only if needed
            $finalTotalToStore = $this->convertToCzk($frontendAmount, $frontendCurrency);

            Log::info('Currency conversion', [
                'original_amount' => $frontendAmount,
                'original_currency' => $frontendCurrency,
                'converted_to_czk' => $finalTotalToStore
            ]);

            // ---------- Stripe PaymentIntent (charge in original currency) ----------
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) round($frontendAmount * 100),
                'currency' => $frontendCurrency,
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'customer_id' => $customerId
                ]
            ]);

            // ---------- Create Order ----------
            $trackingNumber = 'ORD-' . strtoupper(Str::random(10));
            $orderId = DB::table('orders')->insertGetId([
                'customer_id'     => $customerId,
                'cart_id'         => $cart->id,
                'address_id'      => $address->id,
                'shipping_id'     => $shipping->id,
                'discount_amount' => $discount,
                'currency'        => $frontendCurrency,
                'total_amount'    => $totalAmount, // server total in CZK (optional)
                'final_total'     => $finalTotalToStore, // STORED IN CZK (converted if needed)
                'status'          => 'pending',
                'payment_status'  => 'pending',
                'tracking_number' => $trackingNumber,
                'stripe_order_id' => $paymentIntent->id,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // Update PaymentIntent metadata with real order_id
            $paymentIntent->metadata['order_id'] = $orderId;
            $paymentIntent->save();

            // ---------- Order Items ----------
            foreach ($cartItems as $item) {
                DB::table('order_items')->insert([
                    'order_id'   => $orderId,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'unit_price' => $item->price_per_qty,
                    'subtotal'   => $item->total_price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment initiated',
                'order_id' => $orderId,
                'tracking_number' => $trackingNumber,
                'stripe_order_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount_to_pay' => $frontendAmount,
                'currency' => $frontendCurrency
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Order Error', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }


   public function stripeWebhook(Request $request)
    {
        Log::info('Stripe Webhook Received', [
            'payload' => $request->getContent()
        ]);
    
        $event = json_decode($request->getContent());
    
        if (!isset($event->type)) {
    
            Log::warning('Stripe Webhook Invalid Event');
    
            return response()->json(['status' => 'invalid'], 400);
        }
    
        Log::info('Stripe Event Type', [
            'type' => $event->type
        ]);
    
        if ($event->type === 'payment_intent.succeeded') {
    
            $intent = $event->data->object;
    
            Log::info('PaymentIntent Success Received', [
                'intent_id' => $intent->id,
                'amount' => $intent->amount,
                'currency' => $intent->currency
            ]);
    
            $order = DB::table('orders')
                ->where('stripe_order_id', $intent->id)
                ->first();
    
            if (!$order) {
    
                Log::error('Order Not Found For Stripe Intent', [
                    'intent_id' => $intent->id
                ]);
    
                return response()->json(['status' => 'order_not_found']);
            }
    
            Log::info('Order Found', [
                'order_id' => $order->id,
                'payment_status' => $order->payment_status
            ]);
    
            if ($order->payment_status == 'paid') {
    
                Log::warning('Payment Already Processed', [
                    'order_id' => $order->id
                ]);
    
                return response()->json(['status' => 'already_processed']);
            }
    
            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'payment_status' => 'paid',
                    'status' => 'placed',
                    'updated_at' => now(),
                ]);
    
            Log::info('Order Payment Updated', [
                'order_id' => $order->id
            ]);
    
            /* ---------- Delete Cart ---------- */
    
            DB::table('cart_items')
                ->where('cart_id', $order->cart_id)
                ->delete();
    
            DB::table('carts')
                ->where('id', $order->cart_id)
                ->delete();
    
            Log::info('Cart Cleared', [
                'cart_id' => $order->cart_id
            ]);
    
            /* ---------- Notification ---------- */
    
            $customer = DB::table('customers')
                ->where('id', $order->customer_id)
                ->first();
    
            if ($customer && $customer->fcm_token) {
    
                Log::info('Sending Push Notification', [
                    'customer_id' => $customer->id
                ]);
    
                Utilities::sendPush(
                    $customer->fcm_token,
                    'Order Placed Successfully',
                    'Your order has been placed.',
                    [
                        'order_id' => $order->id,
                        'type' => 'order'
                    ]
                );
    
                DB::table('notifications')->insert([
                    'customer_id' => $customer->id,
                    'type' => 'order',
                    'title' => 'Order Placed Successfully',
                    'message' => 'Your order has been placed.',
                    'is_read' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
    
                Log::info('Notification Stored In DB', [
                    'customer_id' => $customer->id
                ]);
            } else {
    
                Log::warning('Customer Or FCM Token Missing', [
                    'customer_id' => $order->customer_id
                ]);
            }
    
            return response()->json([
                'status' => 'payment_success',
                'order_id' => $order->id,
                'payment_intent_id' => $intent->id,
                'amount' => number_format($intent->amount / 100, 2),
            ]);
        }
    
        Log::info('Stripe Event Ignored', [
            'event_type' => $event->type
        ]);
    
        return response()->json(['status' => 'ignored']);
    }

    public function stripeStatus(Request $request)
    {
        $sessionId = $request->query('session_id');
        $dbOrderId = $request->query('order_id');
        if (!$sessionId || !$dbOrderId) {
            return redirect('/api/payment-failed?reason=missing_reference');
        }
        $stripe = new StripeService();
        $result = $stripe->executePayment($sessionId);
        if (!$result['success']) {
            return redirect(
                '/api/payment-failed?reason=' .
                urlencode($result['message']) .
                '&order_id=' . $dbOrderId
            );
        }
        $order = DB::table('orders')->where('id', $dbOrderId)->first();
        if (!$order) {
            return redirect('/api/payment-failed?reason=order_not_found&order_id=' . $dbOrderId);
        }
        // Mark order as paid
        DB::table('orders')->where('id', $order->id)->update([
            'status'                => 'placed',
            'payment_status'        => 'paid',
            'stripe_order_id' => $result['data']['transaction_id'],
            'updated_at'            => now(),
        ]);
        // Clear cart
        DB::table('cart_items')->where('cart_id', $order->cart_id)->delete();
        DB::table('carts')->where('id', $order->cart_id)->delete();
        // Email
        $user = Auth::user();
        if ($user && $user->email) {
            try {
                Mail::to($user->email)->send(new OrderPlacedMail($order));
            } catch (\Exception $e) {
                Log::error('Order confirmation mail failed: ' . $e->getMessage());
            }
        }
        return redirect(
            '/api/payment-success?order_id=' . $order->id .
            '&tracking=' . $order->tracking_number .
            '&total_amount=' . $order->final_total .
            '&payment_method=Stripe'
        );
    }
    //PAYMENT SUCCESS VIEW
    public function success(Request $request)
    {
        return view('payment-success', [
            'order_id'  => $request->query('order_id'),
            'tracking'  => $request->query('tracking'),
            'amount'    => $request->query('total_amount'),
            'payment_method' => $request->query('payment_method'),
        ]);
    }

    //PAYMENT FAILED VIEW
    public function failed(Request $request)
    {
        return view('payment-fail', [
            'order_id' => $request->query('order_id'),
            'reason'   => $request->query('reason') ?? 'Payment failed or cancelled.',
        ]);
    } 

}