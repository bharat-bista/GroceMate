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
     * List all admin and staff accounts
     */
    public function index()
    {
        $accounts = User::whereIn('role_id', [1, 3])->latest()->get();
        return view('inventory.accounts.index', compact('accounts'));
    }

    /**
     * Show create admin/staff account form
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
            'role_type' => 'required|in:admin,staff',
        ]);

        $roleId = $validated['role_type'] === 'staff' ? 3 : 1;
        $roleLabel = $validated['role_type'] === 'staff' ? 'Staff' : 'Admin';

        // Store account data in session
        session([
            'managed_account' => [
                'full_name' => $validated['full_name'],
                'email'     => $validated['email'],
                'password'  => $validated['password'],
                'role_id'   => $roleId,
                'role_label' => $roleLabel,
            ]
        ]);

        // Generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // Store OTP hash in a real existing user row to satisfy the foreign key constraint
        $adminUserId = auth()->id();

        OtpReset::where('user_id', $adminUserId)
            ->where('purpose', 'managed_account_register')
            ->delete();

        OtpReset::create([
            'user_id'    => $adminUserId,
            'otp_hash'   => Hash::make($otp),
            'expires_at' => now()->addMinutes(10),
            'attempts'   => 0,
            'used'       => false,
            'purpose'    => 'managed_account_register',
        ]);

        // Send OTP to the new account's email
        Mail::to($validated['email'])->send(new PasswordOtpMail($otp, $validated['full_name']));

        return redirect()->route('admin.accounts.create')
            ->with('otp_sent', true)
            ->with('success', $roleLabel . ' OTP sent to ' . $validated['email'] . '. Please enter it below.');
    }

    /**
     * Step 2: Verify OTP, create admin/staff account
     */
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $accountData = session('managed_account');

        if (!$accountData) {
            return redirect()->route('admin.accounts.create')
                ->with('error', 'Session expired. Please start over.');
        }

        // Find OTP record
        $otpRecord = OtpReset::where('user_id', auth()->id())
            ->where('purpose', 'managed_account_register')
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

        // OTP valid — create account
        $otpRecord->update(['used' => true]);

        User::create([
            'full_name' => $accountData['full_name'],
            'email'     => $accountData['email'],
            'password'  => $accountData['password'], // auto-hashed by model cast
            'gender'    => 'other',
            'role_id'   => $accountData['role_id'],
            'status'    => 'Y',
        ]);

        // Cleanup session
        session()->forget('managed_account');
        OtpReset::where('user_id', auth()->id())
            ->where('purpose', 'managed_account_register')
            ->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', '✅ ' . $accountData['role_label'] . ' account for ' . $accountData['full_name'] . ' created successfully!');
    }

    /**
     * Delete an admin/staff account (prevent self-deletion)
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.accounts.index')
                ->with('error', '❌ You cannot delete your own account.');
        }

        if (!$user->isAdmin() && !$user->isStaff()) {
            return redirect()->route('admin.accounts.index')
                ->with('error', '❌ This user is not an admin or staff account.');
        }

        $name = $user->full_name;
        $roleLabel = $user->isStaff() ? 'Staff' : 'Admin';
        $user->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', '✅ ' . $roleLabel . ' account "' . $name . '" deleted successfully.');
    }
}
