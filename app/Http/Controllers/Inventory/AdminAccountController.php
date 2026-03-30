<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpReset;
use App\Mail\PasswordOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminAccountController extends Controller
{
    /**
     * List all admin accounts
     */
    public function index()
    {
        $admins = User::where('role_id', 1)->latest()->get();
        return view('inventory.accounts.index', compact('admins'));
    }

    /**
     * Show create admin account form
     */
    public function create()
    {
        return view('inventory.accounts.create');
    }

    /**
     * Step 1: Validate form, store data in session, send OTP
     */
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed',
        ]);

        // Store admin data in session
        session([
            'admin_account' => [
                'full_name' => $validated['full_name'],
                'email'     => $validated['email'],
                'password'  => $validated['password'],
            ]
        ]);

        // Generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // Store OTP hash in a real existing user row to satisfy the foreign key constraint
        $adminUserId = auth()->id();

        OtpReset::where('user_id', $adminUserId)
            ->where('purpose', 'admin_register')
            ->delete();

        OtpReset::create([
            'user_id'    => $adminUserId,
            'otp_hash'   => Hash::make($otp),
            'expires_at' => now()->addMinutes(10),
            'attempts'   => 0,
            'used'       => false,
            'purpose'    => 'admin_register',
        ]);

        // Send OTP to the new admin's email
        Mail::to($validated['email'])->send(new PasswordOtpMail($otp, $validated['full_name']));

        return redirect()->route('admin.accounts.create')
            ->with('otp_sent', true)
            ->with('success', 'OTP sent to ' . $validated['email'] . '. Please enter it below.');
    }

    /**
     * Step 2: Verify OTP, create admin account
     */
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $adminData = session('admin_account');

        if (!$adminData) {
            return redirect()->route('admin.accounts.create')
                ->with('error', 'Session expired. Please start over.');
        }

        // Find OTP record
        $otpRecord = OtpReset::where('user_id', auth()->id())
            ->where('purpose', 'admin_register')
            ->where('used', false)
            ->latest()
            ->first();

        if (!$otpRecord) {
            return redirect()->route('admin.accounts.create')
                ->with('error', 'No OTP found. Please start over.');
        }

        // Check expiration
        if (now()->greaterThan($otpRecord->expires_at)) {
            return redirect()->route('admin.accounts.create')
                ->with('error', 'OTP expired. Please start over.');
        }

        // Check max attempts
        if ($otpRecord->attempts >= 5) {
            return redirect()->route('admin.accounts.create')
                ->with('error', 'Too many attempts. Please start over.');
        }

        // Verify OTP
        if (!Hash::check($request->otp, $otpRecord->otp_hash)) {
            $otpRecord->increment('attempts');
            return redirect()->route('admin.accounts.create')
                ->with('otp_sent', true)
                ->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

        // OTP valid — create admin account
        $otpRecord->update(['used' => true]);

        User::create([
            'full_name' => $adminData['full_name'],
            'email'     => $adminData['email'],
            'password'  => $adminData['password'], // auto-hashed by model cast
            'gender'    => 'other',
            'role_id'   => 1,
            'status'    => 'Y',
        ]);

        // Cleanup session
        session()->forget('admin_account');
        OtpReset::where('user_id', auth()->id())
            ->where('purpose', 'admin_register')
            ->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', '✅ Admin account for ' . $adminData['full_name'] . ' created successfully!');
    }

    /**
     * Delete an admin account (prevent self-deletion)
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.accounts.index')
                ->with('error', '❌ You cannot delete your own account.');
        }

        if (!$user->isAdmin()) {
            return redirect()->route('admin.accounts.index')
                ->with('error', '❌ This user is not an admin.');
        }

        $name = $user->full_name;
        $user->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', '✅ Admin account "' . $name . '" deleted successfully.');
    }
}
