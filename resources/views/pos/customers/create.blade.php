@extends('inventory.layouts.inventory')

@section('title','Add Customer')
@section('heading','Add Customer')
@section('subtitle','Create a new retail/wholesale customer')

@section('content')
<form method="POST" action="{{ route('pos.customers.store') }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5 max-w-3xl">
  @csrf

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <!-- Customer Name -->
    <div class="md:col-span-2">
      <label class="text-sm text-slate-600">Customer Name *</label>
      <input name="name" value="{{ old('name') }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200"
             required />
    </div>

    <!-- Phone -->
    <div>
      <label class="text-sm text-slate-600">Phone Number *</label>
      <input name="phone" value="{{ old('phone') }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200"
             required />
    </div>

    <!-- Alternate Phone -->
    <div>
      <label class="text-sm text-slate-600">Alternate Phone (optional)</label>
      <input name="alternate_phone" value="{{ old('alternate_phone') }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    </div>

    <!-- Email -->
    <div>
      <label class="text-sm text-slate-600">Email (optional)</label>
      <input name="email" value="{{ old('email') }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    </div>

    <!-- Customer Type -->
    <div>
      <label class="text-sm text-slate-600">Customer Type *</label>
      <select name="customer_type"
              class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200"
              required>
        <option value="">Select Type</option>
        <option value="retail" {{ old('customer_type')=='retail'?'selected':'' }}>Retail</option>
        <option value="wholesale" {{ old('customer_type')=='wholesale'?'selected':'' }}>Wholesale</option>
        <option value="regular" {{ old('customer_type')=='regular'?'selected':'' }}>Regular</option>
      </select>
    </div>

    <!-- Opening Due -->
    <div>
      <label class="text-sm text-slate-600">Opening Due Amount (if any)</label>
      <input type="number" step="0.01" name="opening_due"
             value="{{ old('opening_due',0) }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    </div>

    <!-- Address -->
    <div class="md:col-span-2">
      <label class="text-sm text-slate-600">Address</label>
      <textarea name="address" rows="3"
                class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200">{{ old('address') }}</textarea>
    </div>

    <!-- Notes -->
    <div class="md:col-span-2">
      <label class="text-sm text-slate-600">Notes (optional)</label>
      <textarea name="notes" rows="2"
                class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200">{{ old('notes') }}</textarea>
    </div>

  </div>

  <div class="flex gap-3">
    <button class="px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
      Save Customer
    </button>
    <a href="{{ route('pos.customers.index') }}"
       class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Cancel
    </a>
  </div>
</form>
@endsection
