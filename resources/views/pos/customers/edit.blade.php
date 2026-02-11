@extends('inventory.layouts.inventory')

@section('title','Edit Customer')
@section('heading','Edit Customer')
@section('subtitle','Update retail/wholesale customer details')

@section('content')
<form method="POST" action="{{ route('pos.customers.update', $customer) }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5 max-w-3xl">
  @csrf
  @method('PUT')

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <!-- Name -->
    <div class="md:col-span-2">
      <label class="text-sm text-slate-600">Customer Name *</label>
      <input name="name" value="{{ old('name', $customer->name) }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" required />
    </div>

    <!-- Phone -->
    <div>
      <label class="text-sm text-slate-600">Phone *</label>
      <input name="phone" value="{{ old('phone', $customer->phone) }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" required />
    </div>

    <!-- Alternate Phone -->
    <div>
      <label class="text-sm text-slate-600">Alternate Phone</label>
      <input name="alternate_phone"
             value="{{ old('alternate_phone', $customer->alternate_phone) }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    </div>

    <!-- Email -->
    <div>
      <label class="text-sm text-slate-600">Email</label>
      <input name="email"
             value="{{ old('email', $customer->email) }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    </div>

    <!-- Customer Type -->
    <div>
      <label class="text-sm text-slate-600">Customer Type *</label>
      <select name="customer_type"
              class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" required>
        <option value="retail" {{ $customer->customer_type=='retail'?'selected':'' }}>Retail</option>
        <option value="wholesale" {{ $customer->customer_type=='wholesale'?'selected':'' }}>Wholesale</option>
        <option value="regular" {{ $customer->customer_type=='regular'?'selected':'' }}>Regular</option>
      </select>
    </div>

    <!-- Total Due (Readonly) -->
    <div>
      <label class="text-sm text-slate-600">Current Total Due</label>
      <input value="Rs {{ number_format($customer->total_due,2) }}"
             class="mt-1 w-full rounded-xl border-slate-200 bg-slate-100"
             readonly />
    </div>

    <!-- Address -->
    <div class="md:col-span-2">
      <label class="text-sm text-slate-600">Address</label>
      <textarea name="address" rows="3"
                class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200">{{ old('address', $customer->address) }}</textarea>
    </div>

    <!-- Notes -->
    <div class="md:col-span-2">
      <label class="text-sm text-slate-600">Notes</label>
      <textarea name="notes" rows="2"
                class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200">{{ old('notes', $customer->notes) }}</textarea>
    </div>

  </div>

  <div class="flex gap-3">
    <button class="px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
      Update Customer
    </button>
    <a href="{{ route('pos.customers.index') }}"
       class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Cancel
    </a>
  </div>
</form>
@endsection
