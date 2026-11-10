<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function index($productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $reviews = $product->reviews()->with('customer')->get();

            $result = $reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'review' => $review->review,
                    'customer' => [
                        'id' => $review->customer->id ?? null,
                        'name' => $review->customer->name ?? null,
                        'email' => $review->customer->email ?? null
                    ],
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at
                ];
            });

            return response()->json([
                'status' => true,
                'data' => $result
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to get reviews.',
                'data' => $th->getMessage()
            ]);
        }
    }

    public function store(Request $request, $productId)
    {
    try {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $customerId = Auth::user()->id;

        // ✅ Insert OR Update (1 user = 1 review per product)
        $review = ProductReview::updateOrCreate(
            [
                'product_id'  => $product->id,
                'customer_id' => $customerId,
            ],
            [
                'rating' => $request->rating,
                'review' => $request->review,
            ]
        );

        return response()->json([
            'status'  => true,
            'message' => 'Review saved successfully',
            'data'    => $review
        ]);

    } catch (\Throwable $th) {
        return response()->json([
            'status'  => false,
            'message' => 'Unable to submit review.',
            'data'    => $th->getMessage()
        ], 500);
    }
}

}