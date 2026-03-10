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

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Payment Method <span class="text-red-500">*</span></label>
                    <select name="payment_method" required
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm px-4 py-2.5">
                        <option value="">Select Method</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method }}" @selected(old('payment_method') == $method)>{{ ucfirst($method) }}</option>
                        @endforeach
                    </select>
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
