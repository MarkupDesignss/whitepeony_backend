<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function addAddress(Request $request)
    {
        try {
            $customerId = Auth::user()->id;

            $request->validate([
                'address_type'   => 'nullable|in:home,office,hotel,other',
                'full_address'  => 'nullable|string|max:255',
                'email      '  => 'nullable|string',
                'name       '  => 'nullable|string',
                'city'          => 'nullable|string|max:100',
                'postal_code'    => 'required|string|max:20',
                'phone'          => 'nullable|string|max:50',
            ]);
            $addressId = DB::table('addresses')->insertGetId([
                'customer_id'   => $customerId,
                'address_type'  => $request->address_type ?? 'home',
                'full_address' => $request->full_address,
                'postal_code'   => $request->postal_code,
                'phone'         => $request->phone,
                // 'name'         => $request->name  ?? null,
                'city'          => $request->city,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            return response()->json([
                'success'    => true,
                'message'    => 'Address added successfully',
                'address_id' => $addressId
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add address',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function updateAddress(Request $request, $addressId)
    {
        try {
            $customerId = Auth::user()->id;

            // Validate request data
            $request->validate([
                'address_type'   => 'nullable|in:home,office,hotel,other',
                'full_address'   => 'nullable|string|max:255',
                'postal_code'    => 'required|string|max:20',
                // 'name       '  => 'nullable|string',
                'city'          => 'nullable|string|max:100',
                'city'          => 'nullable|string|max:100',
                'phone'          => 'nullable|string|max:50',
            ]);

            // Check if the address belongs to the authenticated user
            $address = DB::table('addresses')
                ->where('id', $addressId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found'
                ], 404);
            }

            // Update address
            DB::table('addresses')
                ->where('id', $addressId)
                ->update([
                    'address_type'  => $request->address_type ?? $address->address_type,
                    'full_address'  => $request->full_address ?? $address->full_address,
                    'postal_code'   => $request->postal_code,
                    'city'          => $request->city ?? $address->city,
                    'name'         => $request->name  ?? null,
                    'phone'         => $request->phone ?? $address->phone,
                    'updated_at'    => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update address',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function getAddresses()
    {
        try {
            $customerId = Auth::user()->id;

            $addresses = DB::table('addresses')
                ->where('customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($addresses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No addresses found'
                ], 200);
            }

            return response()->json([
                'success'   => true,
                'addresses' => $addresses
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch addresses',
                'error'   => $th->getMessage()
            ], 500);
        }
    }
    
    public function deleteAddress(Request $request, $id)
    {
        try {
            $customerId = Auth::user()->id;

            // Find the address
            $address = DB::table('addresses')
                ->where('id', $id)
                ->where('customer_id', $customerId)
                ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found'
                ], 404);
            }

            // Delete the address
            DB::table('addresses')
                ->where('id', $id)
                ->where('customer_id', $customerId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete address',
                'error'   => $th->getMessage()
            ], 500);
        }
    }
}