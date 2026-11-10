<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class BannerController extends Controller
{
    public function index()
    {
        try {

            $banners = Banner::with('product')
                ->where('status','active')
                ->orderBy('id','desc')
                ->get()
                ->groupBy('product_type');

            if ($banners->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No banners available'
                ],404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Banner list grouped by product type',
                'base_url' => url('/storage/'),
                'data' => [
                    'b2b' => $banners->get('b2b', []),
                    'b2c' => $banners->get('b2c', []),
                ]
            ],200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ],500);
        }
    }

}