<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BigSale;
use Illuminate\Support\Facades\Auth;


class BigSaleController extends Controller
{
    // public function index()
    // {
    //     try {

    //         $bigSales = BigSale::with('product')
    //             ->where('status', 'active') 
    //             ->latest()
    //             ->get();

    //         $data = [
    //             'b2b' => $bigSales->where('product_type','b2b')->values(),
    //             'b2c' => $bigSales->where('product_type','b2c')->values(),
    //         ];

    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Big Sale Products (B2B & B2C)',
    //             'data'    => $data
    //         ], 200);

    //     } catch (\Exception $e) {

    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Something went wrong',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    
    public function index()
{
    try {

        $user = Auth::guard('sanctum')->user();

        $bigSales = BigSale::with('product')
            ->where('status', 'active')
            ->latest()
            ->get();

        // Guest user → only B2C
        if (!$user) {

            $data = [
                'b2c' => $bigSales->filter(function ($sale) {
                    return optional($sale->product)->product_type === 'b2c';
                })->values(),
            ];

        } else {

            // Logged user → B2B + B2C
            $data = [
                'b2b' => $bigSales->filter(function ($sale) {
                    return optional($sale->product)->product_type === 'b2b';
                })->values(),

                'b2c' => $bigSales->filter(function ($sale) {
                    return optional($sale->product)->product_type === 'b2c';
                })->values(),
            ];
        }

        return response()->json([
            'status'  => true,
            'message' => 'Big Sale Products',
            'data'    => $data
        ], 200);

    } catch (\Exception $e) {

        return response()->json([
            'status'  => false,
            'message' => 'Something went wrong',
            'error'   => $e->getMessage()
        ], 500);
    }
}
}
