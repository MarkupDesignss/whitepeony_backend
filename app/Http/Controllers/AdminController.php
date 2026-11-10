<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
Use App\Mail\TwoFactorOtpMail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;

class AdminController extends Controller
{
    public $middleware = [];

    public function index()
    {
        return view('admin.auth.login');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $orders = Order::where('payment_status','paid')->count();
        $activecustomer = Customer::where('status','active')->count();
        $blockcustomer = Customer::where('status','blocked')->count();
        $products =   Product::count();
        
        return view('admin.dashboard',compact('user','orders','activecustomer','blockcustomer','products'));
    } 
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            if ($user->two_factor_enabled) {
                $user->two_factor_code = rand(100000, 999999);
                $user->two_factor_expires_at = now()->addMinutes(10);
                $user->save();
                
                Mail::to($user->email)->send(new TwoFactorOtpMail($user));
                
                $request->session()->put('2fa:user:id', $user->id);
                
                return redirect()->route('admin.2fa')->with('info', 'We have sent an OTP to your email.');
            }
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard')->with('success', 'Login successful');
        }
        return redirect()->back()->with([
            'message' => trans('Invalid-credentials'),
            'alert-type' => 'error'
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    public function updateProfile(Request $request)
    {
        try {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);
        $user = Auth()->user();
        $user->update($request->only('name'));
        return back()->with('success', 'Profile updated successfully!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Profile not updated!');
        }
    }

    public function changePassword(Request $request)
    {
        try {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);
        $user = Auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();
        return back()->with('success', 'Password changed successfully!');
         } catch (\Throwable $th) {
            return back()->with('error', 'Password not changed!');
        }
    }

    public function toggle2FA(User $user)
    {
        $user->two_factor_enabled = !$user->two_factor_enabled;
        $user->save();

        return response()->json([
            'two_factor_enabled' => $user->two_factor_enabled
        ]);
    }

    public function show2FAForm(Request $request)
    {
        $userId = $request->session()->get('2fa:user:id');
        if (!$userId) {
            return redirect()->route('admin.login')->with('error', 'Session expired. Please login again.');
        }
        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('admin.login')->with('error', 'User not found.');
        }
        return view('emails.2fa', compact('user'));
    }

    public function verify2FA(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userId = $request->session()->get('2fa:user:id');
        if (!$userId) {
            return redirect()->route('admin.login')->with('error', 'Session expired. Please login again.');
        }
        $user = User::find($userId);

        if (
            $user &&
            $user->two_factor_code === $request->otp &&
            \Carbon\Carbon::parse($user->two_factor_expires_at)->gt(now())
        ) {
            // Reset OTP
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
            $user->save();

            Auth::login($user);
            $request->session()->forget('2fa:user:id');

            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()->with([
            'message' => 'Invalid or expired OTP',
            'alert-type' => 'error'
        ]);
    }

    public function resend2FA(Request $request)
    {
        $userId = $request->session()->get('2fa:user:id');
        $user = $userId ? User::find($userId) : null;

        if (!$user || !$user->two_factor_enabled) {
            return redirect()->route('admin.login')->with([
                'message' => 'Unauthorized access',
                'alert-type' => 'error'
            ]);
        }

        // Generate new OTP
        $user->two_factor_code = rand(100000, 999999);
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        // Send again via email
        Mail::to($user->email)->send(new TwoFactorOtpMail($user));

        return redirect()->route('admin.2fa')->with([
            'message' => 'A new OTP has been sent to your email',
            'alert-type' => 'success'
        ]);
    }
}