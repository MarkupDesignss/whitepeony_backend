<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Str;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client;

class AuthController extends Controller
{

    public function requestRegistrationOtp(Request $request)
    {
        try {
            $request->validate([
                'customer_type' => 'required|in:hotel,restaurant,cafe',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'required|unique:customers,phone',
                'name' => 'required|string',
                'business_name' => 'required|string',
                'password' => 'nullable|string|min:6',
                'vat_id' => 'required|integer',

                // Address fields
                'address.address_type' => 'required|in:home,office,hotel,other',
                'address.full_address' => 'required|string|max:255',
                'address.postal_code' => 'required|string|max:20',
                'address.phone' => 'nullable|string|max:50',
            ]);
            
            // Generate OTP
            $otp = rand(100000, 999999);
           

            // Create customer
            $user = Customer::create([
                'customer_type' => $request->customer_type,
                'email' => $request->email,
                'phone' => $request->phone,
                'name' => $request->name,
                'business_name' => $request->business_name,
                'password' => Hash::make($request->password),
                'vat_id' => $request->vat_id,
                'otp' => $otp,
                'otp_created_at' => Carbon::now(),
            ]);

            // Save address temporarily in cache for 10 minutes
            Cache::put("registration_address_{$user->id}", $request->address, now()->addMinutes(10));

            // Send OTP via email
            Mail::to($user->email)->send(new OtpMail($otp));

            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your email. Please verify to complete registration.',
                'email' => $user->email,
                'otp'   => $otp
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong, unable to send OTP: {$th}",
            ]);
        }
    }

    public function verifyRegistrationOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|digits:6',
            ]);

            $user = Customer::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            if ($user->otp !== $request->otp) {
                return response()->json(['message' => 'Invalid OTP.'], 401);
            }
            
            $isReviewAccount = ($request->email === 'review@whitepeonytea.com');

            if (!$isReviewAccount && $user->otp_created_at && Carbon::parse($user->otp_created_at)->diffInMinutes(now()) > 5) {
                return response()->json(['message' => 'OTP expired.'], 401);
            }

            // Begin transaction
            DB::beginTransaction();

            // OTP correct → mark verified
            $user->email_verified_at = Carbon::now();
            $user->otp = null;
            $user->otp_created_at = null;
            $user->save();

            // Retrieve address from cache
            $addressData = Cache::get("registration_address_{$user->id}");

            if (!$addressData) {
                // Rollback if address not found
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Address data not found. Registration aborted.'
                ]);
            }

             DB::table('addresses')->insert([
                    'customer_id' => $user->id,
                    'address_type' => $addressData['address_type'],
                    'full_address' => $addressData['full_address'],
                    'postal_code' => $addressData['postal_code'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            // Remove from cache
            Cache::forget("registration_address_{$user->id}");

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Registration successful as {$user->customer_type}.",
                'user' => $user
            ]);
        } catch (\Throwable $th) {
            // Rollback on any error
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Unable to register: {$th}",
            ]);
        }
    }

    // public function requestLoginOtp(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'email_or_phone' => 'required'
    //         ]);

    //         $input = $request->email_or_phone;

    //         $user = Customer::where('email', $input)
    //                         ->orWhere('phone', $input)
    //                         ->first();

    //         // Check if user is blocked
    //         if ($user && $user->status === 'blocked') {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Your account is blocked. You cannot request OTP.'
    //             ], 403);
    //         }

    //         // Create new user if not found
    //         if (!$user) {
    //             $user = new Customer();
    //             if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
    //                 $user->email = $input;
    //             } else {
    //                 $user->phone = $input;
    //             }
    //             $user->customer_type = 'hotel';
    //             $user->save();
    //         }

    //         // Generate OTP
    //         $otp = rand(100000, 999999);
    //         $user->otp = $otp;
    //         $user->otp_created_at = now();
    //         $user->save();

    //         // Send OTP via email if it's an email login
    //         if ($user->email && filter_var($input, FILTER_VALIDATE_EMAIL)) {
    //             Mail::to($user->email)->send(new OtpMail($otp));
    //         }

    //         // Return response
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'OTP sent successfully.',
    //             'otp' => $otp
    //         ]);

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => "Unable to send OTP: {$th->getMessage()}"
    //         ]);
    //     }
    // }

    public function requestLoginOtp(Request $request)
    {
        try {
            $request->validate([
                'email_or_phone' => 'required'
            ]);

            $input = $request->email_or_phone;

            $user = Customer::where('email', $input)
                            ->orWhere('phone', $input)
                            ->first();

            // Blocked user check
            if ($user && $user->status === 'blocked') {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is blocked. You cannot request OTP.'
                ], 403);
            }

            // Create user if not exists
            if (!$user) {
                $user = new Customer();
                if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
                    $user->email = $input;
                } else {
                    $user->phone = $input;
                }
                $user->customer_type = 'hotel';
                $user->save();
            }

            // Generate OTP
             $isTestAccount = ($user->email === 'review@whitepeonytea.com');

            if ($isTestAccount) {
                $otp = '123456';   // fixed OTP for Apple review
            } else {
                $otp = rand(100000, 999999);
            }
            
            $user->otp = $otp;
            $user->otp_created_at = now();
            $user->save();

            /* ======================
            EMAIL OTP
            ====================== */
            if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
                Mail::to($user->email)->send(new OtpMail($otp));
            }

            /* ======================
            MOBILE OTP (TWILIO)
            ====================== */
            if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {

                $client = new Client(
                    env('TWILIO_SID'),
                    env('TWILIO_AUTH_TOKEN')
                );

                $client->messages->create(
                    $user->phone, // +91XXXXXXXXXX
                    [
                        'from' => env('TWILIO_FROM'),
                        'body' => "Your login OTP is {$otp}. It is valid for 5 minutes."
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully.'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to send OTP.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function verifyLoginOtp(Request $request)
    {
        try {
            $request->validate([
                'email_or_phone' => 'required',
                'otp' => 'required|string|digits:6',
                'fcm_token' => 'nullable|string'
            ]);

            $user = Customer::where('email', $request->email_or_phone)
                            ->orWhere('phone', $request->email_or_phone)
                            ->first();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            // Check if blocked
            if ($user->status === 'blocked') {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is blocked. You cannot login.'
                ], 403);
            }

            if ($user->otp !== $request->otp) {
                return response()->json(['message' => 'Invalid OTP.'], 401);
            }

            if ($user->otp_created_at && $user->otp_created_at->diffInMinutes(now()) > 5) {
                return response()->json(['message' => 'OTP expired.'], 401);
            }

            // Clear OTP after success
            $user->fcm_token = $request->fcm_token;
            $user->otp = null;
            $user->otp_created_at = null;
            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;

            $isProfileIncomplete = empty($user->name) || empty($user->customer_type);
            $user->load('tier'); // load the tier relationship

            return response()->json([
                'success' => true,
                'message' => $isProfileIncomplete
                    ? "Login successful, please complete your profile."
                    : "Login successful as {$user->customer_type}.",
                'access_token' => $token,
                'token_type' => 'Bearer',
                'redirect_to' => $isProfileIncomplete ? 'profile' : 'dashboard',
                'user' => $user,
                'tiertype' => $user->tier ? $user->tier->type : null, // safe check
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to Login.',
                'error' => $th->getMessage()
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ]);
    }

    public function destroy(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }

            DB::beginTransaction();

            // If you want to also delete related data (optional)
            // $user->orders()->delete();
            // $user->addresses()->delete();
            // etc.

            $user->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Your account has been deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete account.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}