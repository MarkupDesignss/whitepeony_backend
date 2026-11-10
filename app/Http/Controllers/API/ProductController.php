<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    private function mapProductData($products, $cartProductIds, $wishlistProductIds)
    {
        return $products->map(function ($product) use ($cartProductIds, $wishlistProductIds) {

            $product->category_id = json_decode($product->category_id, true) ?: [];

            $product->is_cart     = in_array($product->id, $cartProductIds);
            $product->is_wishlist = in_array($product->id, $wishlistProductIds);

            $product->average_rating = round($product->reviews->avg('rating') ?? 0, 1);
            $product->review_count   = $product->reviews->count();

            // BIG SALE DISCOUNT ENGINE
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

            return $product;
        });
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $deviceToken = $request->header('device_token') ?? $request->input('device_token');

            // Fetch products + variants + reviews
            $products = Product::with(['variants', 'reviews.customer'])->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No products found',
                ], 404);
            }

            $cartProductIds = [];
            $wishlistProductIds = [];

            // ✅ Logged user cart & wishlist
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

            // ✅ Apply flags + rating data
            $products = $this->mapProductData($products, $cartProductIds, $wishlistProductIds);

            return response()->json([
                'status' => true,
                'message' => 'Products fetched successfully',
                'data' => $products,
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Can't get the products: {$th->getMessage()}",
            ], 500);
        }
    }
        
    // ==================== PRODUCT DETAIL ====================
    public function show(Request $request, $id)
    {
        try {

            $user = Auth::guard('sanctum')->user();
            $deviceToken = $request->header('device_token') ?? $request->device_token;

            $product = Product::with(['variants','reviews.customer','bigSale'])->find($id);
            if (!$product) return response()->json(['status'=>false,'message'=>'Product not found'],404);

            $cartProductIds = $wishlistProductIds = [];

            if ($user) {
                $cart = DB::table('carts')->where('customer_id',$user->id)->where('status','pending')->first();
                if ($cart) {
                    $cartProductIds = DB::table('cart_items')->where('cart_id',$cart->id)->pluck('product_id')->toArray();
                }
                $wishlistProductIds = DB::table('wishlists')->where('customer_id',$user->id)->pluck('product_id')->toArray();
            } 
            elseif ($deviceToken) {
                $cart = DB::table('carts')->where('device_token',$deviceToken)->where('status','pending')->first();
                if ($cart) {
                    $cartProductIds = DB::table('cart_items')->where('cart_id',$cart->id)->pluck('product_id')->toArray();
                }
                $wishlistProductIds = DB::table('wishlists')->where('device_token',$deviceToken)->pluck('product_id')->toArray();
            }

            $product = $this->mapProductData(collect([$product]), $cartProductIds, $wishlistProductIds)->first();

            return response()->json(['status'=>true,'message'=>'Product fetched successfully','data'=>$product]);

        } catch (\Throwable $e) {
            return response()->json(['status'=>false,'message'=>$e->getMessage()],500);
        }
    }
    
    public function mostSellingProducts()
    {
        try {

            $products = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select(
                    'products.id',
                    DB::raw('COUNT(order_items.product_id) as sold_count')
                )
                ->groupBy('products.id')
                ->orderByDesc('sold_count')
                ->limit(20)
                ->pluck('products.id'); // get only product IDs

            // Fetch details + reviews
            $data = Product::with(['variants', 'reviews.customer'])
                ->whereIn('id', $products)
                ->get()
                ->map(function ($product) {
                    $product->average_rating = round($product->reviews->avg('rating') ?? 0, 1);
                    $product->review_count   = $product->reviews->count();
                    return $product;
                });

            return response()->json([
                'success' => true,
                'message' => 'Most selling products fetched successfully',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch most selling products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function search(Request $request)
{
    try {
        $user = Auth::guard('sanctum')->user();
        $deviceToken = $request->header('device_token') ?? $request->input('device_token');
        $keyword = trim($request->input('search'));

        $query = Product::with(['variants', 'reviews.customer', 'bigSale'])
            ->when(!$user, function ($q) {
                $q->where('product_type', 'b2c');
            });

        // ONLY starts-with SEARCH
        if ($keyword) {
            $query->where('name', 'LIKE', $keyword . '%');
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No products found'
            ], 404);
        }

        $cartProductIds = [];
        $wishlistProductIds = [];

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

        $products = $this->mapProductData($products, $cartProductIds, $wishlistProductIds);

        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'data' => $products
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    // public function search(Request $request)
    // {
    //     try {
    //         $user = Auth::guard('sanctum')->user();
    //         $deviceToken = $request->header('device_token') ?? $request->input('device_token');
    //         $keyword = trim($request->input('search'));

    //         $query = Product::with(['variants', 'reviews.customer', 'bigSale']);

    //         // ✅ ONLY starts-with SEARCH
    //         if ($keyword) {
    //             $query->where('name', 'LIKE', $keyword . '%');
    //         }

    //         $products = $query->get();

    //         if ($products->isEmpty()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'No products found'
    //             ], 404);
    //         }

    //         $cartProductIds = [];
    //         $wishlistProductIds = [];

    //         if ($user) {
    //             $cart = DB::table('carts')
    //                 ->where('customer_id', $user->id)
    //                 ->where('status', 'pending')
    //                 ->first();

    //             if ($cart) {
    //                 $cartProductIds = DB::table('cart_items')
    //                     ->where('cart_id', $cart->id)
    //                     ->pluck('product_id')
    //                     ->toArray();
    //             }

    //             $wishlistProductIds = DB::table('wishlists')
    //                 ->where('customer_id', $user->id)
    //                 ->pluck('product_id')
    //                 ->toArray();
    //         }
    //         elseif ($deviceToken) {
    //             $cart = DB::table('carts')
    //                 ->where('device_token', $deviceToken)
    //                 ->where('status', 'pending')
    //                 ->first();

    //             if ($cart) {
    //                 $cartProductIds = DB::table('cart_items')
    //                     ->where('cart_id', $cart->id)
    //                     ->pluck('product_id')
    //                     ->toArray();
    //             }

    //             $wishlistProductIds = DB::table('wishlists')
    //                 ->where('device_token', $deviceToken)
    //                 ->pluck('product_id')
    //                 ->toArray();
    //         }

    //         $products = $this->mapProductData($products, $cartProductIds, $wishlistProductIds);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Products fetched successfully',
    //             'data' => $products
    //         ]);

    //     } catch (\Throwable $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

}