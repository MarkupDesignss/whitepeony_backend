<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
     public function index()
    {
        try {
            $categories = Category::orderBy('position', 'asc')->get();
    
            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No categories found',
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Categories fetched successfully',
                'categories' => $categories
            ], 200);
    
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to get categories',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    // public function productsByCategory($categoryId)
    // {
    //     try {
    //         $user = Auth::guard('sanctum')->user();

    //         // Fetch products of category
    //         $products = Product::with('variants')
    //             ->whereJsonContains('category_id', $categoryId)
    //             ->get()
    //             ->map(function ($product) {
    //                 $product->category_id = json_decode($product->category_id, true) ?: [];
    //                 return $product;
    //             });

    //         // Default arrays
    //         $cartProductIds = $wishlistProductIds = [];

    //         // If user is logged in → Fetch cart & wishlist
    //         if ($user) {

    //             // ✅ Get pending user cart
    //             $cart = DB::table('carts')
    //                 ->where('customer_id', $user->id)
    //                 ->where('status', 'pending')
    //                 ->first();

    //             // ✅ Get product IDs in user cart
    //             if ($cart) {
    //                 $cartProductIds = DB::table('cart_items')
    //                     ->where('cart_id', $cart->id)
    //                     ->pluck('product_id')
    //                     ->toArray();
    //             }

    //             // ✅ Get product IDs in wishlist
    //             $wishlistProductIds = DB::table('wishlists')
    //                 ->where('customer_id', $user->id)
    //                 ->pluck('product_id')
    //                 ->toArray();
    //         }

    //         // ✅ Append is_cart & is_wishlist flags
    //         $products->map(function ($product) use ($cartProductIds, $wishlistProductIds) {
    //             $product->is_cart = in_array($product->id, $cartProductIds);
    //             $product->is_wishlist = in_array($product->id, $wishlistProductIds);
    //             return $product;
    //         });

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Products fetched successfully',
    //             'data' => $products->values()
    //         ], 200);

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch products by category',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function productsByCategory($categoryId, Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $deviceToken = $request->header('device_token') ?? $request->input('device_token');

            // Fetch products with variants, reviews, and big sale
            // $products = Product::with(['variants', 'reviews.customer', 'bigSale'])
            //     ->whereJsonContains('category_id', $categoryId)
            //     ->get();
            
            $products = Product::with(['variants', 'reviews.customer', 'bigSale'])
    ->whereJsonContains('category_id', $categoryId)
    ->when(!$user, function ($query) {
        $query->where('product_type', 'b2c');
    })
    ->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No products found for this category',
                ], 404);
            }

            // Default arrays
            $cartProductIds = $wishlistProductIds = [];

            // Logged user cart & wishlist
            if ($user) {
                $cart = DB::table('carts')
                    ->where('customer_id', $user->id)
                    ->where('status', 'pending')
                    ->first();

                if ($cart) {
                    $cartProductIds = DB::table('cart_items')
                        ->where('cart_id', $cart->id)
                        ->pluck('product_id')
                        ->toArray();
                }

                $wishlistProductIds = DB::table('wishlists')
                    ->where('customer_id', $user->id)
                    ->pluck('product_id')
                    ->toArray();
            }
            // ✅ Guest user cart & wishlist
            elseif ($deviceToken) {
                $cart = DB::table('carts')
                    ->where('device_token', $deviceToken)
                    ->where('status', 'pending')
                    ->first();

                if ($cart) {
                    $cartProductIds = DB::table('cart_items')
                        ->where('cart_id', $cart->id)
                        ->pluck('product_id')
                        ->toArray();
                }

                $wishlistProductIds = DB::table('wishlists')
                    ->where('device_token', $deviceToken)
                    ->pluck('product_id')
                    ->toArray();
            }

            // Map product data
            $products = $products->map(function ($product) use ($cartProductIds, $wishlistProductIds) {

            $product->category_id = json_decode($product->category_id, true) ?: [];
        
            $product->is_cart = in_array($product->id, $cartProductIds);
            $product->is_wishlist = in_array($product->id, $wishlistProductIds);
        
            $product->average_rating = round($product->reviews->avg('rating') ?? 0, 1);
            $product->review_count   = $product->reviews->count();
        
            $percentage = optional($product->bigSale)->percentage ?? 0;
        
            $totalStock = 0;
        
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
        
                $totalStock += (int) $variant->stock; // sum of variant stocks
            }
        
            $product->stock_quantity = $totalStock;
        
            return $product;
        });

            return response()->json([
                'status' => true,
                'message' => 'Products fetched successfully',
                'data' => $products->values()
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch products by category',
                'error' => $th->getMessage()
            ], 500);
        }
    }

}