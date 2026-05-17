@extends('inventory.layouts.inventory')

@section('title','Add Customer')
@section('heading','Add Customer')
@section('subtitle','Create a new retail/wholesale customer')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">

        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-700 p-6 text-white">
            <h2 class="text-2xl font-bold">New Customer Setup</h2>
            <p class="text-sm opacity-90">Add customer details and contact information</p>
        </div>

        <!-- Form Section -->
        <form method="POST" action="{{ route('pos.customers.store') }}" class="p-8 space-y-8">
            @csrf

            <!-- Customer Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Customer Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-600">Customer Name <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name') }}"
                           placeholder="Enter full name"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5"
                           required />
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Phone Number <span class="text-red-500">*</span></label>
                    <input name="phone" value="{{ old('phone') }}"
                           placeholder="Enter phone number"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5"
                           required />
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Email (optional)</label>
                    <input name="email" value="{{ old('email') }}"
                           placeholder="Enter email address"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5" />
                </div>

                <!-- VAT Number -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">VAT Number (optional)</label>
                    <input name="vat_number" value="{{ old('vat_number') }}"
                           placeholder="Enter VAT number"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5" />
                </div>

                <!-- PAN Number -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">PAN Number (optional)</label>
                    <input name="pan_number" value="{{ old('pan_number') }}"
                           placeholder="Enter PAN number"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5" />
                </div>

                <!-- Customer Type -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Customer Type <span class="text-red-500">*</span></label>
                    <select name="customer_type"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5"
                            required>
                        <option value="">Select Type</option>
                        <option value="retail" {{ old('customer_type')=='retail'?'selected':'' }}>Retail</option>
                        <option value="wholesale" {{ old('customer_type')=='wholesale'?'selected':'' }}>Wholesale</option>
                        <option value="regular" {{ old('customer_type')=='regular'?'selected':'' }}>Regular</option>
                    </select>
                </div>

                <!-- Opening Due -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Opening Due Amount (if any)</label>
                    <input type="number" step="0.01" name="opening_due"
                           value="{{ old('opening_due',0) }}"
                           placeholder="0.00"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5" />
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-600">Address</label>
                    <textarea name="address" rows="3"
                              placeholder="Enter customer address"
                              class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5">{{ old('address') }}</textarea>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-600">Notes (optional)</label>
                    <textarea name="notes" rows="2"
                              placeholder="Any additional notes"
                              class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5">{{ old('notes') }}</textarea>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between">
                <a href="{{ route('pos.customers.index') }}" data-back-button
                   class="px-6 py-3 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Customers
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition duration-200">
                    <i class="fas fa-save mr-2"></i>Save Customer
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
