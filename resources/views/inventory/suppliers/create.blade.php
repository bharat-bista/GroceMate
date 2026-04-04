@extends('inventory.layouts.inventory')

@section('title','Add Supplier')
@section('heading','Add Supplier')
@section('subtitle','Create a new retail/wholesale supplier')

@section('content')
<div class="max-w-5xl mx-auto">

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-700 p-6 text-white">
            <h2 class="text-2xl font-bold">New Supplier Setup</h2>
            <p class="text-sm opacity-90">Add supplier details and contact information</p>
        </div>

        <!-- Form Section -->
        <form method="POST" action="{{ route('inventory.suppliers.store') }}" class="p-8 space-y-8">
            @csrf

            <!-- Supplier Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Supplier Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-600">Supplier Name <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name') }}"
                           placeholder="Enter supplier name"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5"
                           required />
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Phone Number (optional)</label>
                    <input name="phone" value="{{ old('phone') }}"
                           placeholder="Enter phone number"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5" />
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Email (optional)</label>
                    <input name="email" value="{{ old('email') }}"
                           placeholder="Enter email address"
                           class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5" />
                </div>

                <!-- Supplier Type -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Supplier Type <span class="text-red-500">*</span></label>
                    <select name="supplier_type"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5"
                            required>
                        <option value="">Select Type</option>
                        <option value="retail" {{ old('supplier_type')=='retail'?'selected':'' }}>Retail</option>
                        <option value="wholesale" {{ old('supplier_type')=='wholesale'?'selected':'' }}>Wholesale</option>
                        <option value="regular" {{ old('supplier_type')=='regular'?'selected':'' }}>Regular</option>
                    </select>
                </div>

                <!-- Business Account -->
                <div>
                    <label class="block text-sm font-medium text-slate-600">Business Account <span class="text-red-500">*</span></label>
                    <select name="business_account"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5"
                            required>
                        <option value="">Select Business Account</option>
                        @foreach($businesses as $business)
                            <option value="{{ $business->id }}" {{ old('business_account') == $business->id ? 'selected' : '' }}>
                                {{ $business->business_name }}
                            </option>
                        @endforeach
                    </select>
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
                              placeholder="Enter supplier address"
                              class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5">{{ old('address') }}</textarea>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between">
                <a href="{{ route('inventory.suppliers.index') }}" data-back-button
                   class="px-6 py-3 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Suppliers
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition duration-200">
                    <i class="fas fa-save mr-2"></i>Save Supplier
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
