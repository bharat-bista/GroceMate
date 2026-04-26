@extends('inventory.layouts.inventory')

@section('title', 'Create Account')
@section('subtitle', 'Add a new admin or staff user')
@section('heading', 'Create Account')

@section('content')
<div class="max-w-2xl mx-auto" x-data="{ step: {{ session('otp_sent') ? 2 : 1 }} }">

  {{-- Back link --}}
  <a href="{{ route('admin.accounts.index') }}"
     class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 mb-6">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Back to Accounts
  </a>

  {{-- Step Indicator --}}
  <div class="flex items-center gap-4 mb-8">
    <div class="flex items-center gap-2">
      <div :class="step === 1 ? 'bg-slate-900 text-white' : 'bg-emerald-500 text-white'"
           class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition">
        <span x-show="step === 1">1</span>
        <svg x-show="step === 2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
        </svg>
      </div>
      <span class="text-sm font-medium text-slate-700">Account Details</span>
    </div>
    <div class="flex-1 h-px bg-slate-300"></div>
    <div class="flex items-center gap-2">
      <div :class="step === 2 ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-400'"
           class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition">
        2
      </div>
      <span class="text-sm font-medium" :class="step === 2 ? 'text-slate-700' : 'text-slate-400'">Verify Email</span>
    </div>
  </div>

  {{-- Step 1: Account Details Form --}}
  <div x-show="step === 1" x-transition>
    <div class="bg-white rounded-xl border border-slate-200 p-6">
      <h3 class="text-lg font-semibold text-slate-900 mb-1">Account Details</h3>
      <p class="text-sm text-slate-500 mb-6">Choose Admin or Staff, then enter account details. An OTP will be sent to that email for verification.</p>

      <form action="{{ route('admin.accounts.sendOtp') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Account Type --}}
        <div>
          <label for="role_type" class="block text-sm font-medium text-slate-700 mb-1">Account Type</label>
          <select id="role_type" name="role_type" required
                  class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            <option value="admin" {{ old('role_type', 'admin') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="staff" {{ old('role_type') === 'staff' ? 'selected' : '' }}>Staff</option>
          </select>
          @error('role_type')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Full Name --}}
        <div>
          <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
          <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required
                 class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                 placeholder="e.g. Bharat Bista">
          @error('full_name')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Email --}}
        <div>
          <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" required
                 class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                 placeholder="e.g. admin@grocemate.com">
          @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Password --}}
        <div>
          <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
          <input type="password" id="password" name="password" required
                 class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                 placeholder="Minimum 6 characters">
          @error('password')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
          <input type="password" id="password_confirmation" name="password_confirmation" required
                 class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                 placeholder="Re-enter password">
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-3 bg-slate-900 text-white font-medium rounded-lg hover:bg-slate-800 transition flex items-center justify-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          Send OTP to Email
        </button>
      </form>
    </div>
  </div>

  {{-- Step 2: OTP Verification --}}
  <div x-show="step === 2" x-transition>
    <div class="bg-white rounded-xl border border-slate-200 p-6">
      <h3 class="text-lg font-semibold text-slate-900 mb-1">Verify Email</h3>
      <p class="text-sm text-slate-500 mb-6">
         An OTP has been sent to the selected account email.
         Ask them for the 6-digit code and enter it below.
       </p>

      <form action="{{ route('admin.accounts.verifyOtp') }}" method="POST" class="space-y-5">
        @csrf

        {{-- OTP Input --}}
        <div>
          <label for="otp" class="block text-sm font-medium text-slate-700 mb-1">Enter 6-Digit OTP</label>
          <input type="tel" id="otp" name="otp" maxlength="6" pattern="[0-9]{6}" required autofocus
                 class="w-full px-4 py-3 text-center text-xl font-mono border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                 placeholder="123456">
          @error('otp')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-3 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition flex items-center justify-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          Verify & Create Account
        </button>
      </form>

      {{-- Start Over --}}
      <div class="mt-4 text-center">
        <a href="{{ route('admin.accounts.create') }}"
           class="text-sm text-slate-500 hover:text-slate-700 underline">
          Start Over
        </a>
      </div>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp');
    
    if (otpInput) {
        // Handle paste event
        otpInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text');
            
            // Extract only digits and limit to 6
            const digitsOnly = pastedData.replace(/\D/g, '').substring(0, 6);
            
            // Pad with leading zeros if needed
            const sixDigits = digitsOnly.padStart(6, '0');
            
            this.value = sixDigits;
        });
        
        // Handle input to ensure only digits
        otpInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').substring(0, 6);
        });
    }
});
</script>

@endsection
