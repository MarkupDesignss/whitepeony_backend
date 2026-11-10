<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function getProfile()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'user' => $user->load('address','tier'),
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch profile.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    public function completeProfile(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user && $user->status === 'blocked') {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to add items to the cart. Please ensure your account is not blocked.',
                ], 403);
            }

            $request->validate([
                'name'          => 'required|string|max:255',
                'phone'         => 'required|string|max:20|unique:customers,phone,' . $user->id,
                'email'          =>'nullable|email',  
                'business_name' => 'nullable|string|max:255',
                'vat_id'        => 'nullable|string|max:50',   
                'status'        => 'nullable|in:active,inactive',
                'address'       => 'nullable|string|max:255',
                'zip_code'      => 'nullable|string|max:20',
                'profile_image' => 'nullable'
            ]);
            // dd($request->all());
            if ($request->hasFile('profile_image')) {
                $filePath = $request->file('profile_image')->store('customer', 'public');
                $user->profile_image = $filePath;
            }

            // ✅ Update fields
            $user->name          = $request->name;
            $user->email          = $request->email;
            $user->phone         = $request->phone;
            $user->business_name = $request->business_name ?? null;
            $user->vat_id        = $request->vat_id ?? null;
            $user->status        = $request->status ?? "active";

            // ✅ Set Default Tier ID
           // $user->tier_id       = 1;

            $user->save();

            // ✅ Update or create address
            // $user->address()->updateOrCreate(
            //     ['customer_id' => $user->id],
            //     [
            //         'full_address' => $request->address,
            //         'postal_code'  => $request->zip_code,
            //         'phone'        => $request->phone,
            //     ]
            // );

            return response()->json([
                'success' => true,
                'message' => 'Profile completed successfully.',
                'user'    => $user->load('tier'),
                'base_url' => url('/') . '/storage/'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to update profile.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

}