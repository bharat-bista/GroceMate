@extends('inventory.layouts.inventory')

@section('title','Add Category')
@section('heading','Add Category')
@section('subtitle','Create a new category')

@section('content')
<form method="POST" action="{{ route('inventory.categories.store') }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5 max-w-xl" enctype="multipart/form-data">
  @csrf

  <div>
    <label class="text-sm text-slate-600">Category Name</label>
    <input name="name" value="{{ old('name') }}"
           class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    @error('name')
      <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
  </div>

  <div>
    <label class="text-sm text-slate-600">Category Image</label>
    <input type="file" name="image" accept="image/*"
           class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    @error('image')
      <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
  </div>

  <div>
    <label class="text-sm text-slate-600">Order (Display Sequence)</label>
    <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
           class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    @error('order')
      <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
  </div>

  <div class="flex gap-3">
    <button class="px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
      Save
    </button>
    <a href="{{ route('inventory.categories.index') }}" data-back-button
       class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Cancel
    </a>
  </div>
</form>
@endsection
