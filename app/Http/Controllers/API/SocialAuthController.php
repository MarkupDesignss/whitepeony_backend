<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    // Google Login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $customer = Customer::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'profile_image' => $googleUser->getAvatar(),
            ]
        );

        Auth::guard('customer')->login($customer);

        return response()->json([
            'success' => true,
            'token' => $customer->createToken('auth_token')->plainTextToken,
            'customer' => $customer,
        ]);
    }

    // Apple Login
    public function redirectToApple()
    {
        return Socialite::driver('apple')->stateless()->redirect();
    }

    public function handleAppleCallback()
    {
        $appleUser = Socialite::driver('apple')->stateless()->user();

        $email = $appleUser->getEmail() ?? $appleUser->getId().'@appleuser.com';
        $name = $appleUser->getName() ?? 'Apple User';

        $customer = Customer::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'apple_id' => $appleUser->getId(),
                'profile_image' => null,
            ]
        );

        Auth::guard('customer')->login($customer);

        return response()->json([
            'success' => true,
            'token' => $customer->createToken('auth_token')->plainTextToken,
            'customer' => $customer,
        ]);
    }
}