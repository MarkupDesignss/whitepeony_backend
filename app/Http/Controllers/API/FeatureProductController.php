<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FeatureProduct;
use Illuminate\Support\Facades\Auth;

class FeatureProductController extends Controller
{
    
//     public function index()
// {
//     try {

//         $featured = FeatureProduct::with([
//             'product' => function ($q) {
//                 $q->withMin('variants', 'price')
//                   ->withMax('variants', 'price');
//             }
//         ])
//         ->where('status', 'active')
//         ->latest()
//         ->get();

//         // price range format
//         $featured->map(function ($item) {
//             if ($item->product) {
//                 $item->product->price_range = [
//                     'min' => $item->product->variants_min_price,
//                     'max' => $item->product->variants_max_price,
//                 ];
//             }
//             return $item;
//         });

//         $data = [
//             'b2b' => $featured->where('product_type', 'b2b')->values(),
//             'b2c' => $featured->where('product_type', 'b2c')->values(),
//         ];

//         return response()->json([
//             'status'  => true,
//             'message' => 'Featured Products (B2B & B2C)',
//             'data'    => $data
//         ], 200);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => false,
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }


public function index()
{
    try {

        $user = Auth::guard('sanctum')->user();

        $featured = FeatureProduct::with([
            'product' => function ($q) {
                $q->withMin('variants', 'price')
                  ->withMax('variants', 'price');
            }
        ])
        ->where('status', 'active')
        ->latest()
        ->get();

        // price range format
        $featured->map(function ($item) {
            if ($item->product) {
                $item->product->price_range = [
                    'min' => $item->product->variants_min_price,
                    'max' => $item->product->variants_max_price,
                ];
            }
            return $item;
        });

        // ✅ Guest user → only B2C
        if (!$user) {
            $data = [
                'b2c' => $featured->where('product.product_type', 'b2c')->values(),
            ];
        } else {
            // ✅ Logged user → B2B + B2C
            $data = [
                'b2b' => $featured->where('product.product_type', 'b2b')->values(),
                'b2c' => $featured->where('product.product_type', 'b2c')->values(),
            ];
        }

        return response()->json([
            'status'  => true,
            'message' => 'Featured Products',
            'data'    => $data
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

}
