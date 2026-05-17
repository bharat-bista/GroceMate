{{--
    Shared Payment Method selector — S1-2 / S2-2.
    Included via @include with optional variables:

      $paymentDefault  string  pre-selected option: 'cash'|'credit'|'bank'  (default 'cash')
      $paymentLabel    string  description line shown under the heading      (optional)

    Usage — purchase form (S1-2):
      @include('inventory.partials.payment-method', [
          'paymentDefault' => 'cash',
          'paymentLabel'   => 'How this purchase is being settled.',
      ])

    Usage — POS new sale (S2-2):
      @include('inventory.partials.payment-method', [
          'paymentDefault' => 'cash',
          'paymentLabel'   => 'How the customer will pay for this invoice.',
      ])
--}}
@php
    $paymentDefault ??= 'cash';
    $paymentLabel   ??= 'Select how this transaction will be settled.';
@endphp

<div class="border-t border-slate-200 pt-5">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-slate-900">Payment Method <span class="text-red-500">*</span></h3>
        <p class="text-sm text-slate-600">{{ $paymentLabel }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Cash --}}
        <div class="relative">
            <input type="radio" name="payment_method" value="cash" id="payment_cash"
                   @checked(old('payment_method', $paymentDefault) === 'cash') required
                   class="peer sr-only">
            <label for="payment_cash"
                   class="flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200
                          peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700
                          hover:border-slate-300 border-slate-200">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <div class="font-medium">Cash</div>
                    <div class="text-xs opacity-75">Deducts from business balance</div>
                </div>
            </label>
        </div>

        {{-- Credit --}}
        <div class="relative">
            <input type="radio" name="payment_method" value="credit" id="payment_credit"
                   @checked(old('payment_method', $paymentDefault) === 'credit')
                   class="peer sr-only">
            <label for="payment_credit"
                   class="flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200
                          peer-checked:border-yellow-500 peer-checked:bg-yellow-50 peer-checked:text-yellow-700
                          hover:border-slate-300 border-slate-200">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto mb-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <div class="font-medium">Credit</div>
                    <div class="text-xs opacity-75">Adds to supplier due</div>
                </div>
            </label>
        </div>

        {{-- Bank --}}
        <div class="relative">
            <input type="radio" name="payment_method" value="bank" id="payment_bank"
                   @checked(old('payment_method', $paymentDefault) === 'bank')
                   class="peer sr-only">
            <label for="payment_bank"
                   class="flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200
                          peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700
                          hover:border-slate-300 border-slate-200">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto mb-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <div class="font-medium">Bank</div>
                    <div class="text-xs opacity-75">Deducts from business balance</div>
                </div>
            </label>
        </div>

    </div>
</div>
