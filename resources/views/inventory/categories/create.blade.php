@extends('inventory.layouts.inventory')

@section('title','Add Category')
@section('heading','Add Category')
@section('subtitle','Create a new category')

@section('content')
<form method="POST" action="{{ route('inventory.categories.store') }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6 max-w-3xl" enctype="multipart/form-data">
  @csrf

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="text-sm text-slate-600">Category Name *</label>
      <input name="name" value="{{ old('name') }}"
             class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
      @error('name')
        <span class="text-red-500 text-sm">{{ $message }}</span>
      @enderror
    </div>

    <div>
      <label class="text-sm text-slate-600">Category Image</label>
      <input type="file" name="image" accept="image/*"
             class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm" />
      @error('image')
        <span class="text-red-500 text-sm">{{ $message }}</span>
      @enderror
    </div>

    <div>
      <label class="text-sm text-slate-600">Order (Display Sequence)</label>
      <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
             class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
      @error('order')
        <span class="text-red-500 text-sm">{{ $message }}</span>
      @enderror
    </div>
  </div>

  <div class="flex justify-between pt-2">
    <a href="{{ route('inventory.categories.index') }}" data-back-button
       class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">
      Back
    </a>
    <button class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200">
      Save Category
    </button>
  </div>
</form>
@endsection
