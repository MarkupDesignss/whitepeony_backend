<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Promocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromocodeController extends Controller
{    
    public function getPromo()
    {
        try {
            $user = Auth::user();

            $promos = Promocode::all();

            if ($promos->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No promo codes found'
                ], 404);
            }

            $now = now();

            $validPromos = $promos->filter(function ($promo) use ($user, $now) {

                // ✅ Promo must be active & date valid
                if (!($promo->usage_limit > 0 && $promo->end_date >= $now)) {
                    return false;
                }

                // ✅ Promo must match customer type
                if ($promo->customer_type !== $user->type) {
                    return false;
                }

                // ✅ Promo must match user's tier
                if ($promo->tier_id != $user->tier_id) {
                    return false;
                }

                return true;

            })->values();

            // ✅ Add is_valid field based on end_date comparison
            $validPromos = $validPromos->map(function ($promo) use ($now) {
                $promo->is_valid = $promo->end_date >= $now;
                return $promo;
            });

            if ($validPromos->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No valid promo codes available for your tier'
                ], 400);
            }

            return response()->json([
                'status' => true,
                'message' => 'Valid promo codes fetched successfully',
                'data' => $validPromos
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

}