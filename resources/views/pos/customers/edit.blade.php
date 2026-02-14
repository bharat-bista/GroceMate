@extends('inventory.layouts.inventory')

@section('title','Edit Customer')
@section('heading','Edit Customer')
@section('subtitle','Update retail/wholesale customer details')

@section('content')
<form method="POST" action="{{ route('pos.customers.update', $customer) }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-lg p-6 space-y-6 max-w-3xl mx-auto">
  @csrf
  @method('PUT')

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Name -->
    <div class="md:col-span-2">
      <label class="text-sm text-gray-800 font-medium">Customer Name <span class="text-red-500">*</span></label>
      <input name="name" value="{{ old('name', $customer->name) }}"
             placeholder="Enter full name"
             class="mt-1 w-full rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 shadow-sm px-3 py-2"
             required />
    </div>

    <!-- Phone -->
    <div>
      <label class="text-sm text-gray-800 font-medium">Phone <span class="text-red-500">*</span></label>
      <input name="phone" value="{{ old('phone', $customer->phone) }}"
             placeholder="Enter phone number"
             class="mt-1 w-full rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 shadow-sm px-3 py-2"
             required />
    </div>

    <!-- Alternate Phone -->
    <div>
      <label class="text-sm text-gray-800 font-medium">Alternate Phone</label>
      <input name="alternate_phone"
             value="{{ old('alternate_phone', $customer->alternate_phone) }}"
             placeholder="Enter alternate phone"
             class="mt-1 w-full rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 shadow-sm px-3 py-2" />
    </div>

    <!-- Email -->
    <div>
      <label class="text-sm text-gray-800 font-medium">Email</label>
      <input name="email"
             value="{{ old('email', $customer->email) }}"
             placeholder="Enter email address"
             class="mt-1 w-full rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 shadow-sm px-3 py-2" />
    </div>

    <!-- Customer Type -->
    <div>
      <label class="text-sm text-gray-800 font-medium">Customer Type <span class="text-red-500">*</span></label>
      <select name="customer_type"
              class="mt-1 w-full rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 shadow-sm px-3 py-2"
              required>
        <option value="retail" {{ $customer->customer_type=='retail'?'selected':'' }}>Retail</option>
        <option value="wholesale" {{ $customer->customer_type=='wholesale'?'selected':'' }}>Wholesale</option>
        <option value="regular" {{ $customer->customer_type=='regular'?'selected':'' }}>Regular</option>
      </select>
    </div>

    <!-- Total Due (Readonly) -->
    <div>
      <label class="text-sm text-gray-800 font-medium">Current Total Due</label>
      <input value="Rs {{ number_format($customer->total_due,2) }}"
             class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-100 shadow-sm px-3 py-2"
             readonly />
    </div>

    <!-- Address -->
    <div class="md:col-span-2">
      <label class="text-sm text-gray-800 font-medium">Address</label>
      <textarea name="address" rows="3"
                placeholder="Enter customer address"
                class="mt-1 w-full rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 shadow-sm px-3 py-2">{{ old('address', $customer->address) }}</textarea>
    </div>

    <!-- Notes -->
    <div class="md:col-span-2">
      <label class="text-sm text-gray-800 font-medium">Notes</label>
      <textarea name="notes" rows="2"
                placeholder="Any additional notes"
                class="mt-1 w-full rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 shadow-sm px-3 py-2">{{ old('notes', $customer->notes) }}</textarea>
    </div>

  </div>

  <!-- Divider -->
  <hr class="my-4 border-slate-200">

  <!-- Buttons -->
  <div class="flex gap-3 justify-end">
    <button type="submit"
            class="transition-colors duration-200 px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-medium hover:bg-indigo-500 shadow-md">
      Update Customer
    </button>
    <a href="{{ route('pos.customers.index') }}"
       class="px-6 py-2.5 rounded-xl bg-white border border-slate-300 text-gray-700 font-medium hover:bg-slate-100 transition-colors duration-200">
      Cancel
    </a>
  </div>
</form>
@endsection
