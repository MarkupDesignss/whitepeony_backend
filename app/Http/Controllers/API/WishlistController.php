<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    
    public function addToWishlist(Request $request)
    {
        try {
            $request->validate([
                'product_id' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) {
                        $product = DB::table('products')->where('id', $value)->first();
                        if (!$product) {
                            $fail('The selected product does not exist.');
                        }
                    },
                ],
            ]);

            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please login to add items to wishlist.',
                ], 401);
            }

            if ($user->status === 'blocked') {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to add items to the wishlist. Your account is blocked.',
                ], 403);
            }

            $customerId = $user->id;
            $productId = $request->input('product_id');

            // Check if product already exists in wishlist for this user
            $exists = DB::table('wishlists')
                ->where('customer_id', $customerId)
                ->where('product_id', $productId)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already in your wishlist.',
                ], 400);
            }

            // Add to wishlist
            DB::table('wishlists')->insert([
                'customer_id' => $customerId,
                'product_id'  => $productId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // ðŸš« Removed global product update â€” no is_wishlist field in products table

            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist successfully.',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to wishlist.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function removeFromWishlist($productId)
    {
        try {
            $user = Auth::user();

            if ($user && $user->status === 'blocked') {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to remove items from the wishlist. Your account is blocked.',
                ], 403);
            }

            $customerId = $user->id;

            // Find and delete the wishlist entry
            $deleted = DB::table('wishlists')
                ->where('customer_id', $customerId)
                ->where('product_id', $productId)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found in wishlist.'
                ], 200);
            }

            // Unmark product as wishlist = false
            DB::table('products')
                ->where('id', $productId)
                ->update(['is_wishlist' => 'false']);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist successfully.'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product from wishlist.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }    
    
    // public function fetchWishlist(Request $request)
    // {
    //     try {
    //         $user = Auth::user();
    //         $deviceToken = $request->header('device_token') ?? $request->input('device_token');

    //         if (!$user && !$deviceToken) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unauthorized. Either login or use device token.',
    //             ], 401);
    //         }

    //         if ($user && $user->status === 'blocked') {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Your account is blocked.',
    //             ], 403);
    //         }

    //         $wishlist = Wishlist::with(['product.variants', 'product.reviews.customer'])
    //             ->when($user, fn($q) => $q->where('customer_id', $user->id))
    //             ->when(!$user, fn($q) => $q->where('device_token', $deviceToken))
    //             ->get();

    //         $wishlist->transform(function ($item) {
    //             $product = $item->product;

    //             $product->average_rating = round($product->reviews->avg('rating') ?? 0, 1);
    //             $product->rating_count = $product->reviews->count();
    //             $product->latest_reviews = $product->reviews->sortByDesc('created_at')->take(5)->values();

    //             return [
    //                 'wishlist_id'    => $item->id,
    //                 'product_id'     => $product->id,
    //                 'name'           => $product->name,
    //                 'description'    => $product->description,
    //                 'front_image'    => $product->front_image,
    //                 'back_image'     => $product->back_image,
    //                 'side_image'     => $product->side_image,
    //                 'average_rating' => $product->average_rating,
    //                 'rating_count'   => $product->rating_count,
    //                 'latest_reviews' => $product->latest_reviews,
    //                 'variants'       => $product->variants->values(),
    //             ];
    //         });

    //         return response()->json([
    //             'success' => true,
    //             'count'   => $wishlist->count(),
    //             'items'   => $wishlist,
    //         ]);

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error fetching wishlist.',
    //             'error'   => $th->getMessage(),
    //         ], 500);
    //     }
    // }
    
    public function fetchWishlist(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $deviceToken = $request->header('device_token') ?? $request->input('device_token');

            if (!$user && !$deviceToken) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized. Either login or provide device token.'
                ], 401);
            }

            // ✅ Fetch wishlist with product, variants, reviews, and big sale
            $wishlist = Wishlist::with([
                    'product.variants',
                    'product.reviews.customer',
                    'product.bigSale'
                ])
                ->when($user, fn($q) => $q->where('customer_id', $user->id))
                ->when(!$user, fn($q) => $q->where('device_token', $deviceToken))
                ->get();

            $products = $wishlist->map(function ($item) {

                $product = $item->product;

                if (!$product) return null; // safety check

                // ⭐ Calculate average rating and review count
                $product->average_rating = round($product->reviews->avg('rating') ?? 0, 1);
                $product->review_count   = $product->reviews->count();

                // ⭐ Apply BigSale for variants
                $percentage = optional($product->bigSale)->percentage ?? 0;

                foreach ($product->variants as $variant) {
                    $mrp = (float) $variant->price;

                    if ($percentage > 0) {
                        $discount = ($mrp * $percentage) / 100;
                        $variant->actual_price = round($mrp - $discount, 2);
                        $variant->percentage   = $percentage;
                    } else {
                        $variant->actual_price = $mrp;
                        $variant->percentage   = 0;
                    }
                }

                // ⭐ Flags for wishlist & cart
                $product->is_wishlist = true;
                $product->is_cart     = false;

                return $product;
            })->filter(); // remove null products

            return response()->json([
                'status'  => true,
                'message' => 'Wishlist fetched successfully',
                'data'    => $products->values()
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Error fetching wishlist.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

}