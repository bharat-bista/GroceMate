<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Models\OtpReset;
use App\Mail\PasswordOtpMail;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    // =========================
    // LOGIN
    // =========================
    public function login()
    {
        if (Auth::check()) {
            return auth()->user()->isAdmin()
                ? redirect()->route('inventory.dashboard')
                : redirect()->route('home');
        }

        return view('frontend.account.login');
    }

    public function store(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Check if email exists
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->with('popup_error', 'Email address is incorrect.');
    }

    // Check if account is verified
    if ($user->status !== 'Y') {
        return back()->with('popup_error', 'Your account is not verified.');
    }

    // Check password
    if (!Hash::check($request->password, $user->password)) {
        return back()->with('popup_error', 'Password is incorrect.');
    }

    // Login user
    Auth::login($user, $request->remember);

    if ($user->isAdmin()) {
        return redirect()->route('inventory.dashboard')->with('success', 'Login successful!');
    }

    return redirect()->route('home')->with('success', 'Login successful!');
}


    // =========================
    // REGISTER (EMAIL + OTP)
    // =========================
    public function register()
    {
        return view('frontend.account.register');
    }

    // Submit register form -> create user (inactive) -> send OTP
    public function registerPost(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'gender'    => 'required|in:male,female,other',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed',
        ]);

        // Create user but NOT verified yet
        $user = User::create([
            'full_name' => $data['full_name'],
            'gender'    => $data['gender'],
            'email'     => $data['email'],
            'password'  => $data['password'], // auto-hashed by model casts
            'role_id'   => 2,
            'status'    => 'N', // inactive until OTP verified
        ]);

        // Generate OTP
        $otp = (string) random_int(100000, 999999);

        // Store OTP (hashed) in otp_resets table
        OtpReset::updateOrCreate(
            ['user_id' => $user->id, 'purpose' => 'register'],
            [
                'otp_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
                'attempts' => 0,
                'used' => 0,
            ]
        );

        // Send OTP email (reusing your existing mail)
        Mail::to($user->email)->send(new PasswordOtpMail($otp, $user->full_name));

        return redirect()->route('register.otpForm', $user->id)
            ->with('success', 'OTP sent to your email.');
    }

    // Show register OTP form
    public function showRegisterOtpForm(User $user)
    {
        return view('frontend.account.register-otp', compact('user'));
    }

    // Verify register OTP -> activate user -> login
    public function verifyRegisterOtp(Request $request, User $user)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $row = OtpReset::where('user_id', $user->id)
            ->where('purpose', 'register')
            ->first();

        if (!$row) {
            return back()->withErrors(['otp' => 'OTP not found. Please register again.']);
        }

        if ($row->used) {
            return back()->withErrors(['otp' => 'OTP already used. Please register again.']);
        }

        if ($row->expires_at && now()->greaterThan($row->expires_at)) {
            return back()->withErrors(['otp' => 'OTP expired. Please register again.']);
        }

        if ($row->attempts >= 5) {
            return back()->withErrors(['otp' => 'Too many attempts. Please register again.']);
        }

        if (!Hash::check($request->otp, $row->otp_hash)) {
            $row->increment('attempts');
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        // Mark used & activate user
        $row->update(['used' => 1]);
        $user->update(['status' => 'Y']);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Account verified and created!');
    }

    // =========================
    // GOOGLE SIGN-IN / SIGN-UP
    // =========================
    public function googleRedirect()
    {
        return Socialite::driver('google')
            ->stateless()
            ->with([
                'prompt' => 'select_account',
            ])
            ->redirect();
    }

    public function googleCallback(Request $request)
    {
        if ($request->filled('error')) {
            return redirect()->route('page-login')
                ->with('popup_error', 'Google sign-in was canceled.');
        }

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('page-login')->with('popup_error', 'Google login failed.');
        }

        $email = $googleUser->getEmail();

        if (!$email) {
            return redirect()->route('page-login')
                ->with('popup_error', 'Google account email is unavailable.');
        }

        $user = User::where('email', $email)->first();

        if ($user && $user->status !== 'Y') {
            return redirect()->route('page-login')
                ->with('popup_error', 'Your account is not verified.');
        }

        if (!$user) {
            $user = User::create([
                'full_name' => $googleUser->getName() ?? 'Google User',
                'email'     => $email,
                'google_id' => $googleUser->getId(),
                'gender'    => 'other',
                'role_id'   => 2,
                'status'    => 'Y',
                'password'  => Str::random(32),
            ]);
        } elseif (!$user->google_id && $googleUser->getId()) {
            $user->update([
                'google_id' => $googleUser->getId(),
            ]);
        }

        Auth::login($user);

        if ($user->isAdmin()) {
            return redirect()->route('inventory.dashboard')->with('success', 'Logged in with Google!');
        }

        return redirect()->route('home')->with('success', 'Logged in with Google!');
    }

    // =========================
    // LOGOUT
    // =========================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
