@extends('inventory.layouts.inventory')

@section('title','Add Supplier Payment')
@section('heading','Add Supplier Payment')
@section('subtitle','Record payment to supplier')

@section('content')
<div class="max-w-5xl mx-auto">

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-700 p-6 text-white">
            <h2 class="text-2xl font-bold">New Supplier Payment</h2>
            <p class="text-sm opacity-90">Record payment details and transaction information</p>
        </div>

        <!-- Form Section -->
        <form method="POST" action="{{ route('pos.supplier-payments.store') }}" class="p-8 space-y-8">
            @csrf

            <!-- Payment Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5"
                           required />
                </div>

                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Supplier <span class="text-red-500">*</span></label>
                    <select name="supplier_id" required
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
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
                            <option value="{{ $business->id }}" @selected(old('business_account') == $business->id)>{{ $business->business_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Payment Amount <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}"
                           placeholder="0.00"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5"
                           required />
                </div>

                <!-- Payment Method Type Selection -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-600 mb-3">Payment Method <span class="text-red-500">*</span></label>
                    
                    <div class="space-y-3">
                        <!-- External Payment Option -->
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <label class="flex items-start cursor-pointer p-4 bg-slate-50 hover:bg-slate-100 transition-colors">
                                <input type="radio" name="payment_type" value="external" 
                                       class="mt-1 mr-3 text-emerald-600 focus:ring-emerald-500" onchange="togglePaymentFields('external')" checked>
                                <div class="flex-1">
                                    <div class="font-medium text-sm">Customer pays externally</div>
                                    <div class="text-xs text-slate-500">Cash, Bank, or customer's own Khalti</div>
                                </div>
                            </label>
                            
                            <!-- External Payment Dropdown -->
                            <div id="external-fields" class="p-4 bg-white border-t border-slate-200">
                                <select name="payment_method_external"
                                        class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" @selected(old('payment_method_external') == 'cash')>💵 Cash (Customer will pay)</option>
                                    <option value="bank" @selected(old('payment_method_external') == 'bank')>🏦 Bank Transfer (Customer will transfer)</option>
                                    <option value="khalti_external" @selected(old('payment_method_external') == 'khalti_external')>📱 Khalti (Customer's phone)</option>
                                </select>
                                
                                <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg mt-3">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0-1 .975 1.925C5.025 1 2.25 2.25a1 1 0 0 0-2 2v12.5a1 1 0 0 0 2-2V5a1 1 0 0 0-2 2H6a1 1 0 0 0-2 2v12.5a1 1 0 0 0 2-2V5a1 1 0 0 0-2 2h12a1 1 0 0 0 2 2z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-sm text-amber-700">
                                            <strong>External Payment:</strong> Record this payment and mark as "paid" after customer confirms they've completed the transaction.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Integrated Payment Option -->
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <label class="flex items-start cursor-pointer p-4 bg-slate-50 hover:bg-slate-100 transition-colors">
                                <input type="radio" name="payment_type" value="integrated" 
                                       class="mt-1 mr-3 text-emerald-600 focus:ring-emerald-500" onchange="togglePaymentFields('integrated')">
                                <div class="flex-1">
                                    <div class="font-medium text-sm">System Integrated Payment</div>
                                    <div class="text-xs text-slate-500">Process payment through system's eSewa integration</div>
                                </div>
                            </label>
                            
                            <!-- Integrated Payment Fields -->
                            <div id="integrated-fields" class="p-4 bg-white border-t border-slate-200 hidden">
                                <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-lg mb-3">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-emerald-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-sm text-emerald-700">
                                            <strong>Integrated Payment:</strong> Payment will be processed immediately through eSewa gateway.
                                        </div>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="payment_method_integrated" value="esewa">
                                
                                <div class="flex items-center space-x-3 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                    <div class="text-purple-700 font-medium">eSewa Payment</div>
                                    <div class="text-xs text-purple-600">System will redirect to eSewa for payment processing</div>
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
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5" />
                </div>

                <!-- Bank Charge -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Bank Charge</label>
                    <input type="number" step="0.01" name="bank_charge" value="{{ old('bank_charge', 0) }}"
                           placeholder="0.00"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5" />
                </div>

                <!-- TDS Applicable -->
                <div class="flex items-center">
                    <input type="checkbox" name="tds_applicable" value="1" @checked(old('tds_applicable'))
                           class="rounded border-slate-300 text-emerald-600 focus:border-emerald-500 focus:ring-emerald-500" />
                    <label class="ml-2 text-sm font-medium text-slate-600">TDS Applicable</label>
                </div>

            </div>

            <!-- Notes Section -->
            <div class="border-t pt-6">
                <label class="block text-sm font-medium text-slate-600 mb-2">Additional Notes</label>
                <textarea name="note" rows="4" placeholder="Add any additional notes about this payment..."
                          class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5">{{ old('note') }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t">
                <a href="{{ route('pos.supplier-payments.index') }}" 
                   class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">
                    Cancel
                </a>
                <div class="flex gap-3">
                    <button type="reset" class="px-6 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition duration-200">
                        Reset
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition duration-200">
                        Save Payment
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePaymentFields(type) {
    const externalFields = document.getElementById('external-fields');
    const externalSelect = document.querySelector('select[name="payment_method_external"]');
    const integratedFields = document.getElementById('integrated-fields');
    
    if (type === 'external') {
        // Show external fields, hide integrated fields
        externalFields.classList.remove('hidden');
        externalSelect.disabled = false;
        externalSelect.required = true;
        integratedFields.classList.add('hidden');
    } else if (type === 'integrated') {
        // Show integrated fields, hide external fields
        integratedFields.classList.remove('hidden');
        externalFields.classList.add('hidden');
        externalSelect.disabled = true;
        externalSelect.required = false;
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.querySelector('input[name="amount"]');
    const externalSelect = document.querySelector('select[name="payment_method_external"]');
    
    // Initialize default state: external fields visible, integrated hidden
    externalSelect.disabled = false;
    externalSelect.required = true;
    document.getElementById('integrated-fields').classList.add('hidden');
    
    // Add event listener for amount input
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            // No special handling needed for amount input
        });
    }
});
</script>
@endpush
