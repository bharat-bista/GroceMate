<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\PasswordOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DB;

class ForgetPasswordController extends Controller
{
    public function showEmailForm()
    {
        return view('frontend.account.forgetpass');

    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if(!$user){
            return back()->withErrors(['email'=> 'Email not register']);
        }

        $rawOtp = mt_rand(100000, 999999);

        //hash Otp to store

        $otpHash = Hash::make($rawOtp);

        // store OTP in DB (invalidate previous OTPs)
        DB::table('otp_resets')->where('user_id', $user->id)->delete();
        DB::table('otp_resets')->insert([
            'user_id'    => $user->id,
            'otp_hash'   => $otpHash,
            'expires_at' => Carbon::now()->addMinutes(10),
            'attempts'   => 0,
            'used'       => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // send OTP email (could be queued)

        Mail::to($user->email)->send(new PasswordOtpMail($rawOtp, $user->name));
        return redirect()->route('password.otpForm')->with('status','OTP send to your email (check spam folder).');

    }
    // show OTP input form
    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }
    //verify otp
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        
        $userId = optional(auth()->user())->id; // not logged in; instead search by email in session? We'll find the otp by user input via earlier email.
        // Better: locate latest otp_reset by email. For this, keep the email in session:
        $email = session('password_reset_email'); // set this in sendOtp below

        if(!$email){
            return redirect()->route('password.request')->withErrors(['email'=>'Session expired. please request Otp again.']);

        }
        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'Invalid session.']);
        }
        $otpRecord = DB::table('otp_resets')->where('user_id', $user->id)->where('used', false)->orderBy('created_at', 'desc')->first();
        if (!$otpRecord) {
            return redirect()->route('password.request')->withErrors(['otp' => 'No OTP request found.']);
        }
        // check expiration
        if (Carbon::now()->greaterThan(Carbon::parse($otpRecord->expires_at))) {
            return redirect()->route('password.request')->withErrors(['otp' => 'OTP expired. Please request a new one.']);
        }
        // check attempts
        if ($otpRecord->attempts >= 5) {
            return redirect()->route('password.request')->withErrors(['otp' => 'Too many attempts. Request new OTP.']);
        }
        // increment attempt count
        DB::table('otp_resets')->where('id', $otpRecord->id)->increment('attempts');

        // verify using Hash::check
        if (!Hash::check($request->otp, $otpRecord->otp_hash)) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }
         // mark used
        DB::table('otp_resets')->where('id', $otpRecord->id)->update(['used' => true]);

        // set session flag to allow password reset
        session(['password_reset_user_id' => $user->id]);

        return redirect()->route('password.resetForm');


    }
    // show reset password form
    public function showResetForm()
    {
        if (!session('password_reset_user_id')) {
            return redirect()->route('password.request')->withErrors(['otp' => 'Unauthorized or session expired.']);
        }
        return view('auth.reset-password');
    }

    //reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $userId = session('password_reset_user_id');
        if (!$userId) {
            return redirect()->route('password.request')->withErrors(['otp' => 'Session expired.']);
        }

        // update password
        \App\Models\User::where('id', $userId)->update([
            'password' => Hash::make($request->password)
        ]);

        // cleanup sessions and OTPs
        DB::table('otp_resets')->where('user_id', $userId)->delete();
        session()->forget(['password_reset_user_id', 'password_reset_email']);

        return redirect()->route('page-login')->with('status', 'Password reset successful. You can now login.');
    }
}
