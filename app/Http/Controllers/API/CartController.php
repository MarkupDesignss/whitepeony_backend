<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Str;
use App\Models\Cart;

class CartController extends Controller
{
    // User based add to cart
    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'variant_id' => 'nullable|integer|exists:product_variants,id',
                'quantity'   => 'nullable|integer|min:1',
            ]);

            $User = Auth::user();
            if (!$User) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Please login.',
                ], 401);
            }

            if ($User->status === 'blocked') {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to add items to the cart. Please ensure your account is not blocked.',
                ], 403);
            }

            $customerId = $User->id;
            $tierId = $User->tier_id;

            // Get or create pending cart
            $cart = DB::table('carts')
                ->where('customer_id', $customerId)
                ->where('status', 'pending')
                ->first();

            if (!$cart) {
                $cartId = DB::table('carts')->insertGetId([
                    'customer_id' => $customerId,
                    'status'      => 'pending',
                    'total_amount'=> 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            } else {
                $cartId = $cart->id;
            }

            $productId = $request->product_id;
            $variantId = $request->variant_id;
            $quantity  = $request->quantity ?? 1;

            // Fetch variant (if provided)
            $variant = null;
            if ($variantId) {
                $variant = DB::table('product_variants')->where('id', $variantId)->first();
                if (!$variant) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid variant selected.',
                    ], 400);
                }
            }

            // Determine unit price
            if ($variant) {
                $unitPrice = $variant->price;
                $variantUnit = $variant->unit;
            } else {
                $pricingQuery = DB::table('product_pricing')->where('product_id', $productId);
                if ($tierId) {
                    $pricingQuery->where('tier_id', $tierId);
                }
                $pricing = $pricingQuery->first();
                $unitPrice = $pricing ? $pricing->price : DB::table('products')->where('id', $productId)->value('price');
                $variantUnit = null;
            }

            $totalPrice = $unitPrice * $quantity;

            // Check if item with same product and variant exists in this user's cart
            $cartItem = DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if ($cartItem) {
                $newQty = $cartItem->quantity + $quantity;
                DB::table('cart_items')
                    ->where('id', $cartItem->id)
                    ->update([
                        'quantity'      => $newQty,
                        'price_per_qty' => $unitPrice,
                        'total_price'   => $unitPrice * $newQty,
                        'updated_at'    => now(),
                    ]);
            } else {
                DB::table('cart_items')->insert([
                    'cart_id'       => $cartId,
                    'product_id'    => $productId,
                    'variant_id'    => $variantId,
                    'variant_unit'  => $variantUnit,
                    'quantity'      => $quantity,
                    'price_per_qty' => $unitPrice,
                    'total_price'   => $totalPrice,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            // Update total cart amount
            $cartTotal = DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->sum('total_price');

            DB::table('carts')
                ->where('id', $cartId)
                ->update([
                    'total_amount' => $cartTotal,
                    'updated_at'   => now(),
                ]);

            // ðŸš« Removed the global is_cart update on products table

            return response()->json([
                'success'  => true,
                'message'  => 'Product added to cart successfully.',
                'cart_id'  => $cartId,
                'item'     => [
                    'product_id'   => $productId,
                    'variant_id'   => $variantId,
                    'variant_unit' => $variantUnit,
                    'quantity'     => $quantity,
                    'unit_price'   => $unitPrice,
                    'total_price'  => $totalPrice,
                ],
                'cart_total' => $cartTotal
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function updateCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'variant_id' => 'nullable|integer|exists:product_variants,id',
                'quantity'   => 'required|integer|min:0',
            ]);

            $User = Auth::user();
            if ($User && $User->status === 'blocked') {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to update the cart. Please ensure your account is not blocked.',
                ], 403);
            }

            $customerId = $User->id;
            $productId  = $request->product_id;
            $variantId  = $request->variant_id;
            $quantity   = $request->quantity;

            // âœ… Fetch user's active cart
            $cart = DB::table('carts')
                ->where('customer_id', $customerId)
                ->where('status', 'pending')
                ->first();

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found.',
                ], 200);
            }

            // âœ… Fetch the cart item
            $cartItem = DB::table('cart_items')
                ->where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();
            // dd($cartItem);
            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found in cart.',
                ], 200);
            }

            // âœ… Check stock depending on variant presence
            if ($variantId) {
                $variant = DB::table('product_variants')->where('product_id', $productId)
                            ->first();

                if (!$variant) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Variant not found.',
                    ], 200);
                }
                if ($variant->stock < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Requested quantity ($quantity) exceeds available stock for this variant ($variant->stock).",
                    ], 400);
                }
            } else {
                $product = DB::table('products')->where('id', $productId)->first();
                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found.',
                    ], 200);
                }
                

                if ($product->stock_quantity < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Requested quantity ($quantity) exceeds available stock ($product->stock_quantity).",
                    ], 400);
                }
            }

            // âœ… If quantity = 0 â†’ remove item from cart
            if ($quantity == 0) {
                DB::table('cart_items')->where('id', $cartItem->id)->delete();
            } else {
                // âœ… Use existing unit price from cart_items
                $unitPrice  = $cartItem->price_per_qty;
                $totalPrice = $unitPrice * $quantity;

                DB::table('cart_items')
                    ->where('id', $cartItem->id)
                    ->update([
                        'quantity'    => $quantity,
                        'total_price' => $totalPrice,
                        'updated_at'  => now(),
                    ]);
            }

            // âœ… Recalculate total cart amount
            $cartTotal = DB::table('cart_items')
                ->where('cart_id', $cart->id)
                ->sum('total_price');

            DB::table('carts')
                ->where('id', $cart->id)
                ->update([
                    'total_amount' => $cartTotal,
                    'updated_at'   => now(),
                ]);

            return response()->json([
                'success'      => true,
                'message'      => 'Cart updated successfully.',
                'cart_id'      => $cart->id,
                'total_amount' => $cartTotal,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    // public function deleteCartItem(Request $request, $productId)
    // {
    //     try {
    //         $customerId = Auth::user()->id;
    //         $User = Auth::user();
    //         if ($User && $User->status === 'blocked') {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'You do not have permission to add items to the cart. Please ensure your account is not blocked.',
    //             ], 403);
    //         }

    //         // Fetch cart
    //         $cart = DB::table('carts')
    //             ->where('customer_id', $customerId)
    //             ->first();

    //         if (!$cart) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Cart not found'
    //             ], 200);
    //         }

    //         // Delete cart item
    //         $deleted = DB::table('cart_items')
    //             ->where('cart_id', $cart->id)
    //             ->where('product_id', $productId)
    //             ->delete();

    //         if (!$deleted) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Product not found in cart'
    //             ], 200);
    //         }

    //         // Recalculate cart total_amount
    //         $cartTotal = DB::table('cart_items')
    //             ->where('cart_id', $cart->id)
    //             ->sum('total_price');

    //         DB::table('carts')
    //             ->where('id', $cart->id)
    //             ->update([
    //                 'total_amount' => $cartTotal,
    //                 'updated_at' => now()
    //             ]);

    //         // Mark product as not in cart
    //         DB::table('products')
    //             ->where('id', $productId)
    //             ->update(['is_cart' => 'false']);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Product removed from cart successfully',
    //             'cart_total' => $cartTotal
    //         ]);

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to delete cart item',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function deleteCartItem(Request $request, $productId)
{
    $user = Auth::user();
    $cart = DB::table('carts')
        ->where('customer_id', $user->id)
        ->where('status', 'pending')
        ->first();
    // dd($cart);
        
    if (!$cart) {
        return response()->json(['success' => false, 'message' => 'Cart not found']);
    }

    // ✅ DELETE ONLY SELECTED VARIANT
    $deleted = DB::table('cart_items')
        ->where('cart_id', $cart->id)
        ->where('product_id', $productId)
        ->where('variant_id', $request->variant_id) // ⭐ THIS LINE FIXES EVERYTHING
        ->delete();
  

    if (!$deleted) {
        return response()->json(['success' => false, 'message' => 'Item not found']);
    }

    // ✅ Recalculate cart
    $cartTotal = DB::table('cart_items')
        ->where('cart_id', $cart->id)
        ->sum('total_price');

    // ✅ If no items left → delete cart
    if ($cartTotal == 0) {
        DB::table('cart_items')->where('cart_id', $cart->id)->delete();
        DB::table('carts')->where('id', $cart->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed, cart deleted',
            'cart_deleted' => true
        ]);
    }

    DB::table('carts')->where('id', $cart->id)->update([
        'total_amount' => $cartTotal,
        'updated_at' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Variant removed successfully',
        'cart_total' => $cartTotal
    ]);
}

public function fetchCart(Request $request)
{
    try {
        $user = Auth::user();
        $deviceToken = $request->header('device_token') ?? $request->input('device_token');

        if (!$user && !$deviceToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Either login or send device token.',
            ], 401);
        }

        if ($user && $user->status === 'blocked') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is blocked.',
            ], 403);
        }

        // ⭐ VAT Percentage
        $vatPercentage = $user->vat_percentage ?? 0;
            // dd($user);
        $cart = Cart::with([
                'items.product.bigSale',
                'items.product.reviews.customer',
                'items.variant'
            ])
            ->when($user, fn ($q) => $q->where('customer_id', $user->id))
            ->when(!$user, fn ($q) => $q->where('device_token', $deviceToken))
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => true,
                'message' => 'Cart is empty',
                'vat_percentage' => $vatPercentage,
                'cart' => [
                    'id' => null,
                    'items' => [],
                    'total_amount' => 0
                ]
            ]);
        }

        $cartTotal = 0;

        $cart->items = $cart->items->transform(function ($item) use (&$cartTotal) {

            $product = $item->product;

            // ⭐ Product deleted / missing safety
            if (!$product) {
                return null;
            }

            // ⭐ BIG SALE LOGIC
            $percentage = optional($product->bigSale)->percentage ?? 0;
            $mrp = (float) $item->price_per_qty;

            if ($percentage > 0) {
                $discount = ($mrp * $percentage) / 100;
                $actualPrice = round($mrp - $discount, 2);
            } else {
                $actualPrice = $mrp;
            }

            $itemTotal = $actualPrice * $item->quantity;
            $cartTotal += $itemTotal;

            // ⭐ Rating
            $averageRating = round($product->reviews->avg('rating') ?? 0, 1);
            $ratingCount  = $product->reviews->count();

            return [
                'cart_item_id'   => $item->id,
                'quantity'       => $item->quantity,
                'mrp'            => $mrp,
                'actual_price'   => $actualPrice,
                'percentage'     => $percentage,
                'total_price'    => round($itemTotal, 2),

                'product_id'     => $product->id,
                'name'           => $product->name,
                'description'    => $product->description,
                'front_image'    => $product->front_image,
                'back_image'     => $product->back_image,
                'side_image'     => $product->side_image,
                'average_rating' => $averageRating,
                'rating_count'   => $ratingCount,

                'variant_id'     => $item->variant?->id,
                'unit'           => $item->variant?->unit,
            ];
        })->filter()->values(); // ⭐ null items remove

        DB::table('carts')
            ->where('id', $cart->id)
            ->update([
                'total_amount' => round($cartTotal, 2),
                'updated_at'   => now(),
            ]);

        return response()->json([
            'success' => true,
            'vat_percentage' => $vatPercentage,
            'cart' => [
                'id'           => $cart->id,
                'items'        => $cart->items,
                'total_amount' => round($cartTotal, 2),
            ],
        ]);

    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching cart.',
            'error'   => $th->getMessage(),
        ], 500);
    }
}
    
//     public function fetchCart(Request $request)
//     {
//     try {
//         $user = Auth::user();
//         $deviceToken = $request->header('device_token') ?? $request->input('device_token');

//         if (!$user && !$deviceToken) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Unauthorized. Either login or send device token.',
//             ], 401);
//         }

//         if ($user && $user->status === 'blocked') {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Your account is blocked.',
//             ], 403);
//         }

//         $cart = Cart::with([
//                 'items.product.bigSale',
//                 'items.product.reviews.customer',
//                 'items.variant'
//             ])
//             ->when($user, fn ($q) => $q->where('customer_id', $user->id))
//             ->when(!$user, fn ($q) => $q->where('device_token', $deviceToken))
//             ->first();

//         if (!$cart) {
//             return response()->json([
//                 'success' => true,
//                 'message' => 'Cart is empty',
//                 'cart' => [
//                     'id' => null,
//                     'items' => [],
//                     'total_amount' => 0
//                 ]
//             ]);
//         }

//         $cartTotal = 0;

//         $cart->items->transform(function ($item) use (&$cartTotal) {

//             $product = $item->product;

//             // ⭐ BIG SALE LOGIC
//             $percentage = optional($product->bigSale)->percentage ?? 0;
//             $mrp = (float) $item->price_per_qty;

//             if ($percentage > 0) {
//                 $discount = ($mrp * $percentage) / 100;
//                 $actualPrice = round($mrp - $discount, 2);
//             } else {
//                 $actualPrice = $mrp;
//             }

//             $itemTotal = $actualPrice * $item->quantity;
//             $cartTotal += $itemTotal;

//             // ⭐ Rating
//             $averageRating = round($product->reviews->avg('rating') ?? 0, 1);
//             $ratingCount  = $product->reviews->count();

//             return [
//                 // Cart Item
//                 'cart_item_id'   => $item->id,
//                 'quantity'       => $item->quantity,
//                 'mrp'            => $mrp,
//                 'actual_price'   => $actualPrice,
//                 'percentage'     => $percentage,
//                 'total_price'    => round($itemTotal, 2),

//                 // Product
//                 'product_id'     => $product->id,
//                 'name'           => $product->name,
//                 'description'    => $product->description,
//                 'front_image'    => $product->front_image,
//                 'back_image'     => $product->back_image,
//                 'side_image'     => $product->side_image,
//                 'average_rating' => $averageRating,
//                 'rating_count'   => $ratingCount,

//                 // Variant
//                 'variant_id'     => $item->variant?->id,
//                 'unit'           => $item->variant?->unit,
//             ];
//         });

//         // ⭐ Update cart total with BigSale prices
//         DB::table('carts')
//             ->where('id', $cart->id)
//             ->update([
//                 'total_amount' => round($cartTotal, 2),
//                 'updated_at'   => now(),
//             ]);

//         return response()->json([
//             'success' => true,
//             'cart' => [
//                 'id'           => $cart->id,
//                 'items'        => $cart->items,
//                 'total_amount' => round($cartTotal, 2),
//             ],
//         ]);

//     } catch (\Throwable $th) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Error fetching cart.',
//             'error'   => $th->getMessage(),
//         ], 500);
//     }
// }


    public function applyPromoCode(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|integer|exists:carts,id',
            'promo_code' => 'required|string',
        ]);

        $customer = Auth::user();

        // ✅ Get promo
        $promo = DB::table('promocodes')
            ->where('coupon_code', $request->promo_code)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired promo code'], 400);
        }

        // ✅ Check if user already used this promo
        $alreadyUsed = DB::table('promo_usages')
            ->where('customer_id', $customer->id)
            ->where('promo_id', $promo->id)
            ->exists();

        if ($alreadyUsed) {
            return response()->json(['success' => false, 'message' => 'You have already used this promo code.'], 400);
        }

        // ✅ Customer Type check
        if ($promo->customer_type && $promo->customer_type != ($customer->type ?? null)) {
            return response()->json(['success'=>false,'message'=>'Promo code not applicable for your customer type'], 400);
        }

        // ✅ Tier check only if customer is B2B
        if (($customer->type ?? null) === 'b2b') {
            if ($promo->tier_id && $promo->tier_id != $customer->tier_id) {
                return response()->json(['success'=>false,'message'=>'Promo code not applicable for your tier'], 400);
            }
        }

        // ✅ Get Cart Total
        $totalAmount = Cart::where('id', $request->cart_id)->value('total_amount');

        // ✅ Calculate Discount
        if ($promo->discount_type === 'percentage') {
            $discountAmount = ($totalAmount * $promo->discount_value) / 100;
            if ($promo->max_discount && $discountAmount > $promo->max_discount) {
                $discountAmount = $promo->max_discount;
            }
        } else {
            $discountAmount = min($promo->discount_value, $promo->max_discount ?? $promo->discount_value);
        }

        $finalAmount = $totalAmount - $discountAmount;

        // ✅ Update Cart
        DB::table('carts')
            ->where('id', $request->cart_id)
            ->update([
                'discount_amount' => $discountAmount,
                'total_amount' => $finalAmount,
                'updated_at' => now(),
            ]);

        // ✅ Record Promo Usage
        DB::table('promo_usages')->insert([
            'customer_id' => $customer->id,
            'promo_id' => $promo->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promo code applied successfully.',
            'promo_id' => $promo->id,
            'promo_code' => $promo->coupon_code,
            'discount_amount' => $discountAmount,
            'total_amount' => $finalAmount,
        ]);
    }

}