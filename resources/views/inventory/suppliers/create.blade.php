@extends('inventory.layouts.inventory')

@section('title','Add Supplier')
@section('heading','Add Supplier')
@section('subtitle','Create a new supplier/vendor')

@section('content')
<form method="POST" action="{{ route('inventory.suppliers.store') }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5 max-w-2xl">
  @csrf

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
      <label class="text-sm text-slate-600">Supplier Name</label>
      <input name="name" value="{{ old('name') }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    </div>

    <div>
      <label class="text-sm text-slate-600">Phone (optional)</label>
      <input name="phone" value="{{ old('phone') }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    </div>

    <div>
      <label class="text-sm text-slate-600">Email (optional)</label>
      <input name="email" value="{{ old('email') }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    </div>

    <div class="md:col-span-2">
      <label class="text-sm text-slate-600">Address (optional)</label>
      <textarea name="address" rows="3"
                class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200">{{ old('address') }}</textarea>
    </div>
  </div>

  <div class="flex gap-3">
    <button class="px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
      Save
    </button>
    <a href="{{ route('inventory.suppliers.index') }}"
       class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Cancel
    </a>
  </div>
</form>
@endsection
