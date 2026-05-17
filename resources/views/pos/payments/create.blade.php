@extends('inventory.layouts.inventory')

@section('title', 'Add Supplier Payment')
@section('heading', 'Add Supplier Payment')
@section('subtitle', 'Record payment to supplier')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-700 p-6 text-white">
            <h2 class="text-2xl font-bold">New Supplier Payment</h2>
            <p class="text-sm opacity-90">Record payment details and transaction information</p>
        </div>

        <!-- Form -->
        <form id="payment-form" method="POST" action="{{ route('pos.supplier-payments.store') }}" class="p-8 space-y-8">
            @csrf

            <!-- Payment Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Payment Date -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">
                        Payment Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5"
                           required>
                </div>

                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">
                        Supplier <span class="text-red-500">*</span>
                    </label>
                    <select name="supplier_id" required
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Business Account -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Business Account</label>
                    <select name="business_account"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5">
                        <option value="">Select Business</option>
                        @foreach($businesses as $business)
                            <option value="{{ $business->id }}" @selected(old('business_account') == $business->id)>
                                {{ $business->business_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">
                        Payment Amount (NPR) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" name="amount" id="payment-amount"
                           value="{{ old('amount') }}" placeholder="0.00"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5"
                           required>
                </div>

                <!-- Payment Method -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-600 mb-3">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">

                        <!-- External Payment -->
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <label class="flex items-start cursor-pointer p-4 bg-slate-50 hover:bg-slate-100 transition-colors">
                                <input type="radio" name="payment_type" value="external"
                                       id="type_external"
                                       class="mt-1 mr-3 text-emerald-600 focus:ring-emerald-500"
                                       checked>
                                <div class="flex-1">
                                    <div class="font-medium text-sm">Customer pays externally</div>
                                    <div class="text-xs text-slate-500">Cash, Bank, or customer's own Khalti</div>
                                </div>
                            </label>

                            <div id="external-fields" class="p-4 bg-white border-t border-slate-200">
                                <select name="payment_method" id="external-payment-method"
                                        class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" @selected(old('payment_method') == 'cash')>
                                        💵 Cash
                                    </option>
                                    <option value="bank" @selected(old('payment_method') == 'bank')>
                                        🏦 Bank Transfer
                                    </option>
                                    <option value="khalti_external" @selected(old('payment_method') == 'khalti_external')>
                                        📱 Khalti (Customer's phone)
                                    </option>
                                </select>

                                <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg mt-3">
                                    <div class="text-sm text-amber-700">
                                        <strong>External Payment:</strong> Record this payment and mark as "paid"
                                        after customer confirms transaction.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Integrated Payment -->
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <label class="flex items-start cursor-pointer p-4 bg-slate-50 hover:bg-slate-100 transition-colors">
                                <input type="radio" name="payment_type" value="integrated"
                                       id="type_integrated"
                                       class="mt-1 mr-3 text-emerald-600 focus:ring-emerald-500">
                                <div class="flex-1">
                                    <div class="font-medium text-sm">System Integrated Payment</div>
                                    <div class="text-xs text-slate-500">Process payment through Khalti or eSewa integration</div>
                                </div>
                            </label>

                            <div id="integrated-fields" class="p-4 bg-white border-t border-slate-200 hidden">
                                <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-lg mb-4">
                                    <div class="text-sm text-emerald-700">
                                        <strong>Integrated Payment:</strong> Click the payment button below to
                                        be redirected to the payment gateway. Payment will be recorded automatically after success.
                                    </div>
                                </div>

                                <!-- Payment Gateway Buttons -->
                                <div class="flex flex-wrap gap-3">

                                    <!-- Pay with Khalti -->
                                    <button type="button" id="khalti-pay"
                                        class="mt-2 inline-flex items-center gap-2 px-5 py-2.5 rounded-lg transition duration-200 hover:shadow-lg active:scale-95"
                                        style="background-color: #ffffff; border: 2px solid #5C2D91; color: #5C2D91;"
                                        onmouseover="this.style.backgroundColor='#5C2D91'; this.style.color='#ffffff';"
                                        onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#5C2D91';">
                                        <img
                                            src="{{ asset('assets/img/logo/khalti.png') }}"
                                            alt="Khalti"
                                            class="h-6 w-auto object-contain"
                                        >
                                        <span class="font-semibold text-sm">Pay with Khalti</span>
                                    </button>

                                    <!-- Pay with eSewa -->
                                    <button type="button" id="esewa-pay"
                                        class="mt-2 inline-flex items-center gap-2 px-5 py-2.5 rounded-lg transition duration-200 hover:shadow-lg active:scale-95"
                                        style="background-color: #ffffff; border: 2px solid #60BB46; color: #60BB46;"
                                        onmouseover="this.style.backgroundColor='#60BB46'; this.style.color='#ffffff';"
                                        onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#60BB46';">
                                        <img
                                            src="{{ asset('assets/img/logo/esewa.png') }}"
                                            alt="eSewa"
                                            class="h-6 w-auto object-contain"
                                        >
                                        <span class="font-semibold text-sm">Pay with eSewa</span>
                                    </button>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Payment Reference -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Payment Reference</label>
                    <input name="payment_reference" value="{{ old('payment_reference') }}"
                           placeholder="Transaction reference number"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5">
                </div>

                <!-- Bank Charge -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Bank Charge</label>
                    <input type="number" step="0.01" name="bank_charge" value="{{ old('bank_charge', 0) }}"
                           placeholder="0.00"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5">
                </div>

                <!-- TDS Applicable -->
                <div class="flex items-center">
                    <input type="checkbox" name="tds_applicable" value="1" @checked(old('tds_applicable'))
                           class="rounded border-slate-300 text-emerald-600 focus:border-emerald-500 focus:ring-emerald-500">
                    <label class="ml-2 text-sm font-medium text-slate-600">TDS Applicable</label>
                </div>

            </div>

            <!-- Notes -->
            <div class="border-t pt-6">
                <label class="block text-sm font-medium text-slate-600 mb-2">Additional Notes</label>
                <textarea name="note" rows="4"
                          placeholder="Add any additional notes about this payment..."
                          class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5">{{ old('note') }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t">
                <a href="{{ route('pos.supplier-payments.index') }}" data-back-button
                   class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">
                    Cancel
                </a>

                <div class="flex gap-3">
                    <!-- External payment buttons -->
                    <div id="external-submit-buttons">
                        <button type="reset"
                                class="px-6 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition duration-200">
                            Reset
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition duration-200">
                            Save Payment
                        </button>
                    </div>

                    <!-- Integrated payment — no submit button -->
                    <div id="integrated-submit-buttons" class="hidden">
                        <button type="reset"
                                class="px-6 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition duration-200">
                            Reset
                        </button>
                        <span class="px-6 py-2.5 bg-slate-200 text-slate-400 rounded-xl text-sm cursor-not-allowed">
                            Use payment button above ↑
                        </span>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Helper: get field value safely ──
    function getFieldValue(selector) {
        const el = document.querySelector(selector);
        return el ? el.value : '';
    }

    // ── Toggle Payment Fields ──
    function togglePaymentFields(type) {
        const externalFields          = document.getElementById('external-fields');
        const externalPaymentMethod   = document.getElementById('external-payment-method');
        const integratedFields        = document.getElementById('integrated-fields');
        const externalSubmitButtons   = document.getElementById('external-submit-buttons');
        const integratedSubmitButtons = document.getElementById('integrated-submit-buttons');

        if (type === 'external') {
            externalFields.classList.remove('hidden');
            externalPaymentMethod.disabled = false;
            externalPaymentMethod.required = true;
            integratedFields.classList.add('hidden');
            externalSubmitButtons.classList.remove('hidden');
            integratedSubmitButtons.classList.add('hidden');
        } else {
            integratedFields.classList.remove('hidden');
            externalFields.classList.add('hidden');
            externalPaymentMethod.disabled = true;
            externalPaymentMethod.required = false;
            externalSubmitButtons.classList.add('hidden');
            integratedSubmitButtons.classList.remove('hidden');
        }
    }

    // Initialize on page load
    togglePaymentFields('external');

    // Listen for radio changes
    document.querySelectorAll('input[name="payment_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            togglePaymentFields(this.value);
        });
    });

    // ── Helper: Build and submit a fresh form ──
    function submitPaymentForm(action, paymentMethod) {
        const supplierId = getFieldValue('select[name="supplier_id"]');
        const amount     = getFieldValue('#payment-amount');

        if (!supplierId) { alert('⚠️ Please select a supplier.'); return; }
        if (!amount || parseFloat(amount) <= 0) { alert('⚠️ Please enter a valid amount.'); return; }

        // Build fresh form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = action;

        const fields = {
            _token:           document.querySelector('meta[name="csrf-token"]').content,
            supplier_id:      supplierId,
            amount:           amount,
            date:             getFieldValue('input[name="date"]'),
            business_account: getFieldValue('select[name="business_account"]'),
            note:             getFieldValue('textarea[name="note"]'),
            bank_charge:      getFieldValue('input[name="bank_charge"]') || 0,
            payment_method:   paymentMethod,
            payment_type:     'integrated',
        };

        Object.entries(fields).forEach(([key, value]) => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = key;
            input.value = value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    // ── Khalti Pay Button ──
    document.getElementById('khalti-pay')?.addEventListener('click', function (e) {
        e.preventDefault();
        submitPaymentForm("{{ route('pos.khalti.initiate') }}", 'khalti');
    });

    // ── eSewa Pay Button ──
    document.getElementById('esewa-pay')?.addEventListener('click', function (e) {
        e.preventDefault();
        submitPaymentForm("{{ route('pos.esewa.initiate') }}", 'esewa');
    });

});
</script>
@endpush
