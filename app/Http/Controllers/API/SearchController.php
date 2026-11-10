<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\ProductReview;
use App\Models\UserSearch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    private function mapProductData($products, $cartProductIds, $wishlistProductIds)
    {
        return $products->map(function ($product) use ($cartProductIds, $wishlistProductIds) {

            $product->category_id = json_decode($product->category_id, true) ?: [];

            $product->is_cart = in_array($product->id, $cartProductIds);
            $product->is_wishlist = in_array($product->id, $wishlistProductIds);

            $product->average_rating = $product->average_rating;
            $product->review_count = $product->review_count;

            return $product;
        });
    }

    public function globalSearch(Request $request)
    {
        try {
            $keyword = $request->input('q');

            if (!$keyword) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search keyword is required'
                ], 400);
            }

            $user = Auth::guard('sanctum')->user();

            if ($user) {
                UserSearch::create([
                    'customer_id' => $user->id,
                    'keyword' => $keyword
                ]);
            }

            $products = Product::with(['variants', 'reviews.customer'])
                ->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
                })
                ->get();

            $cartProductIds = $wishlistProductIds = [];

            if ($user) {
                $cartProductIds = DB::table('carts')
                    ->where('customer_id', $user->id)
                    ->where('status', 'pending')
                    ->first()?->cartItems->pluck('product_id')->toArray() ?? [];

                $wishlistProductIds = DB::table('wishlists')
                    ->where('customer_id', $user->id)
                    ->pluck('product_id')
                    ->toArray();
            }

            $products = $this->mapProductData($products, $cartProductIds, $wishlistProductIds);

            return response()->json([
                'success' => true,
                'message' => 'Search results fetched successfully',
                'data' => ['products' => $products->values()]
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to perform search',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function recommendedProductsForUser(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                $products = Product::with(['variants', 'reviews.customer'])->latest()->take(20)->get();
            } else {
                $keywords = UserSearch::where('customer_id', $user->id)
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->pluck('keyword')
                    ->toArray();

                if (empty($keywords)) {
                    $products = Product::with(['variants', 'reviews'])->latest()->take(20)->get();
                } else {
                    $categoryIds = Product::where(function ($q) use ($keywords) {
                            foreach ($keywords as $keyword) {
                                $q->orWhere('name', 'like', "%{$keyword}%")
                                ->orWhere('description', 'like', "%{$keyword}%");
                            }
                        })
                        ->get()
                        ->flatMap(fn($p) => json_decode($p->category_id, true))
                        ->unique()
                        ->toArray();

                    $products = Product::with(['variants', 'reviews'])->get()->filter(function($product) use ($categoryIds) {
                        return count(array_intersect(json_decode($product->category_id, true), $categoryIds)) > 0;
                    });
                }
            }

            $cartProductIds = $wishlistProductIds = [];

            if ($user) {
                $cartProductIds = DB::table('cart_items')
                    ->where('cart_id', function($q) use ($user) {
                        $q->from('carts')->select('id')->where('customer_id', $user->id)->where('status', 'pending')->limit(1);
                    })
                    ->pluck('product_id')->toArray();

                $wishlistProductIds = DB::table('wishlists')
                    ->where('customer_id', $user->id)
                    ->pluck('product_id')
                    ->toArray();
            }

            $products = $this->mapProductData($products, $cartProductIds, $wishlistProductIds);

            return response()->json([
                'success' => true,
                'message' => $user ? 'Recommended products based on your searches' : 'Showing normal products',
                'data' => $products->values()
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to get products',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    // public function filterProducts(Request $request)
    // {
    //     try {
    //         $query = Product::with(['variants', 'reviews.customer'])
    //             ->leftJoin('product_variants', 'product_variants.product_id', '=', 'products.id');

    //         if ($request->has('category_id')) {
    //             $query->whereJsonContains('products.category_id', $request->category_id);
    //         }

    //         if ($request->has('min_price')) {
    //             $query->where('product_variants.price', '>=', $request->min_price);
    //         }

    //         if ($request->has('max_price')) {
    //             $query->where('product_variants.price', '<=', $request->max_price);
    //         }

    //         // ✅ Rating Filter (Final & Correct)
    //         if ($request->has('rating')) {
    //             $rating = (int)$request->rating;

    //             $query->whereIn('products.id', function ($subQuery) use ($rating) {
    //                 $subQuery->select('product_id')
    //                     ->from('product_reviews')
    //                     ->groupBy('product_id')
    //                     ->havingRaw('AVG(rating) >= ?', [$rating]);
    //             });
    //         }

    //         $products = $query->select('products.*')->distinct()->get();

    //         if ($products->isEmpty()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No products found for given filters'
    //             ], 200);
    //         }

    //         $products->map(function ($product) {

    //             $avgRating = ProductReview::where('product_id', $product->id)->avg('rating');
    //             $product->average_rating = $avgRating ? round($avgRating, 1) : null;

    //             $product->front_image = $product->front_image ? asset('storage/' . $product->front_image) : null;
    //             $product->back_image  = $product->back_image ? asset('storage/' . $product->back_image) : null;
    //             $product->side_image  = $product->side_image ? asset('storage/' . $product->side_image) : null;

    //             $product->reviews = $product->reviews->map(function ($review) {
    //                 return [
    //                     'id' => $review->id,
    //                     'rating' => $review->rating,
    //                     'review' => $review->review,
    //                     'customer_id' => $review->customer_id,
    //                     'customer_name' => $review->customer ? $review->customer->name : null,
    //                     'created_at' => $review->created_at->format('Y-m-d'),
    //                 ];
    //             });

    //             return $product;
    //         });

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Filtered products fetched successfully',
    //             'data' => $products
    //         ], 200);

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error filtering products',
    //             'error'   => $th->getMessage(),
    //         ], 500);
    //     }
    // }
    
    public function filterProducts(Request $request)
    {
        try {
            $query = Product::with([
                'variants' => function($q) use ($request) {
                    if ($request->has('min_price')) {
                        $q->where('price', '>=', $request->min_price);
                    }
                    if ($request->has('max_price')) {
                        $q->where('price', '<=', $request->max_price);
                    }
                },
                'reviews.customer'
            ])
                ->leftJoin('product_variants', 'product_variants.product_id', '=', 'products.id');

            if ($request->has('category_id')) {
                $query->whereJsonContains('products.category_id', $request->category_id);
            }

            if ($request->has('min_price')) {
                $query->where('product_variants.price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('product_variants.price', '<=', $request->max_price);
            }

            // ✅ Rating Filter (Final & Correct)
            if ($request->has('rating')) {
                $rating = (int)$request->rating;

                $query->whereIn('products.id', function ($subQuery) use ($rating) {
                    $subQuery->select('product_id')
                        ->from('product_reviews')
                        ->groupBy('product_id')
                        ->havingRaw('AVG(rating) >= ?', [$rating]);
                });
            }

            $products = $query->select('products.*')->distinct()->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No products found for given filters'
                ], 200);
            }

            $products = $products->map(function ($product) use ($request) {

                $avgRating = ProductReview::where('product_id', $product->id)->avg('rating');
                $product->average_rating = $avgRating ? round($avgRating, 1) : null;

                $product->front_image = $product->front_image ? asset('storage/' . $product->front_image) : null;
                $product->back_image  = $product->back_image ? asset('storage/' . $product->back_image) : null;
                $product->side_image  = $product->side_image ? asset('storage/' . $product->side_image) : null;

                // Sort variants by price
                $product->variants = $product->variants->sortBy('price')->values();

                $product->reviews = $product->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'review' => $review->review,
                        'customer_id' => $review->customer_id,
                        'customer_name' => $review->customer ? $review->customer->name : null,
                        'created_at' => $review->created_at->format('Y-m-d'),
                    ];
                });

                return $product;
            });              

            return response()->json([
                'success' => true,
                'message' => 'Filtered products fetched successfully',
                'data' => $products
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error filtering products',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

   
//     public function sortProducts(Request $request)
//     {
//     try {
//         $sortBy     = $request->input('sort_by', 'price_asc');
//         $categoryId = $request->input('category_id');

//         /* -------------------------
//          | BASE PRODUCT QUERY
//          -------------------------*/
//         $query = Product::query()
//             ->select(
//                 'products.id',
//                 'products.name',
//                 'products.description',
//                 'products.category_id',
//                 'products.front_image',
//                 'products.back_image',
//                 'products.side_image',
//                 'products.product_type',
//                 'products.brewing_info',
//                 'products.created_at',
//                 'products.updated_at'
//             )
//             ->leftJoin('product_variants', 'product_variants.product_id', '=', 'products.id')
//             ->leftJoin('big_sales', function ($join) {
//                 $join->on('big_sales.product_id', '=', 'products.id')
//                      ->where('big_sales.status', 1);
//             })
//             ->selectRaw('
//                 MIN(
//                     CASE
//                         WHEN big_sales.percentage IS NOT NULL
//                         THEN product_variants.price - (product_variants.price * big_sales.percentage / 100)
//                         ELSE product_variants.price
//                     END
//                 ) as final_price
//             ')
//             ->groupBy(
//                 'products.id',
//                 'products.name',
//                 'products.description',
//                 'products.category_id',
//                 'products.front_image',
//                 'products.back_image',
//                 'products.side_image',
//                 'products.product_type',
//                 'products.brewing_info',
//                 'products.created_at',
//                 'products.updated_at'
//             );

//         /* -------------------------
//          | CATEGORY FILTER
//          -------------------------*/
//         if (!empty($categoryId)) {
//             $query->whereJsonContains('products.category_id', (string)$categoryId);
//         }

//         /* -------------------------
//          | SORTING
//          -------------------------*/
//         if ($sortBy === 'price_desc') {
//             $query->orderBy('final_price', 'desc');
//         } else {
//             $query->orderBy('final_price', 'asc');
//         }

//         $products = $query->get();

//         if ($products->isEmpty()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'No products found'
//             ], 404);
//         }

//         /* -------------------------
//          | LOAD VARIANTS + APPLY DISCOUNT
//          -------------------------*/
//         $products->map(function ($product) {

//             $bigSale = \App\Models\BigSale::where('product_id', $product->id)
//                         ->where('status', 1)
//                         ->first();

//             $percentage = $bigSale->percentage ?? 0;

//             $product->variants = \App\Models\ProductVariant::where('product_id', $product->id)
//                 ->get()
//                 ->map(function ($variant) use ($percentage) {

//                     $actualPrice = $variant->price;

//                     if ($percentage > 0) {
//                         $actualPrice = $variant->price - ($variant->price * $percentage / 100);
//                     }

//                     return [
//                         'id'           => $variant->id,
//                         'product_id'   => $variant->product_id,
//                         'unit'         => $variant->unit,
//                         'price'        => (float)$variant->price,
//                         'percentage'   => (int)$percentage,
//                         'actual_price' => round($actualPrice, 2),
//                         'stock'        => $variant->stock,
//                     ];
//                 });

//             return $product;
//         });

//         return response()->json([
//             'success' => true,
//             'message' => 'Products sorted successfully',
//             'data'    => $products
//         ], 200);

//     } catch (\Throwable $th) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Unable to sort products',
//             'error'   => $th->getMessage(),
//         ], 500);
//     }
// }

public function sortProducts(Request $request)
{
    try {

        $user = Auth::guard('sanctum')->user();

        $sortBy     = $request->input('sort_by', 'price_asc');
        $categoryId = $request->input('category_id');

        /* -------------------------
         | BASE PRODUCT QUERY
        -------------------------*/
        $query = Product::query()
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.category_id',
                'products.front_image',
                'products.back_image',
                'products.side_image',
                'products.product_type',
                'products.brewing_info',
                'products.created_at',
                'products.updated_at'
            )
            ->leftJoin('product_variants', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('big_sales', function ($join) {
                $join->on('big_sales.product_id', '=', 'products.id')
                     ->where('big_sales.status', 1);
            })
            ->selectRaw('
                MIN(
                    CASE
                        WHEN big_sales.percentage IS NOT NULL
                        THEN product_variants.price - (product_variants.price * big_sales.percentage / 100)
                        ELSE product_variants.price
                    END
                ) as final_price
            ')
            ->groupBy(
                'products.id',
                'products.name',
                'products.description',
                'products.category_id',
                'products.front_image',
                'products.back_image',
                'products.side_image',
                'products.product_type',
                'products.brewing_info',
                'products.created_at',
                'products.updated_at'
            );

        /* -------------------------
         | GUEST USER → ONLY B2C
        -------------------------*/
        if (!$user) {
            $query->where('products.product_type', 'b2c');
        }

        /* -------------------------
         | CATEGORY FILTER
        -------------------------*/
        if (!empty($categoryId)) {
            $query->whereJsonContains('products.category_id', (string)$categoryId);
        }

        /* -------------------------
         | SORTING
        -------------------------*/
        if ($sortBy === 'price_desc') {
            $query->orderBy('final_price', 'desc');
        } else {
            $query->orderBy('final_price', 'asc');
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No products found'
            ], 404);
        }

        /* -------------------------
         | LOAD VARIANTS + APPLY DISCOUNT
        -------------------------*/
        $products->map(function ($product) {

            $bigSale = \App\Models\BigSale::where('product_id', $product->id)
                        ->where('status', 1)
                        ->first();

            $percentage = $bigSale->percentage ?? 0;

            $product->variants = \App\Models\ProductVariant::where('product_id', $product->id)
                ->get()
                ->map(function ($variant) use ($percentage) {

                    $actualPrice = $variant->price;

                    if ($percentage > 0) {
                        $actualPrice = $variant->price - ($variant->price * $percentage / 100);
                    }

                    return [
                        'id'           => $variant->id,
                        'product_id'   => $variant->product_id,
                        'unit'         => $variant->unit,
                        'price'        => (float)$variant->price,
                        'percentage'   => (int)$percentage,
                        'actual_price' => round($actualPrice, 2),
                        'stock'        => $variant->stock,
                    ];
                });

            return $product;
        });

        return response()->json([
            'success' => true,
            'message' => 'Products sorted successfully',
            'data'    => $products
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'Unable to sort products',
            'error'   => $th->getMessage(),
        ], 500);
    }
}


}