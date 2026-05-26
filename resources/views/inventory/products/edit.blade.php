@extends('inventory.layouts.inventory')

@section('title','Edit Product')
@section('heading','Edit Product')
@section('subtitle','Update product information')

@section('content')
<form method="POST"
      action="{{ route('inventory.products.update', $product->id) }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5">

  @csrf
  @method('PUT')

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="text-sm text-slate-600">Business Account</label>
      <select name="business_id" class="mt-1 w-full rounded-xl border-slate-200" required>
        <option value="">Select business account</option>
        @foreach($businesses as $business)
          <option value="{{ $business->id }}" @selected(old('business_id', $product->business_id) == $business->id)>{{ $business->business_name }}</option>
        @endforeach
      </select>
      @error('business_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="text-sm text-slate-600">Product Name</label>
      <input name="name"
             value="{{ old('name', $product->name) }}"
             class="mt-1 w-full rounded-xl border-slate-200" />
      @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="text-sm text-slate-600">Brand</label>
      <select name="brand_id" class="mt-1 w-full rounded-xl border-slate-200">
        <option value="">Select a brand</option>
        @foreach($brands as $b)
          <option value="{{ $b->id }}" @selected(old('brand_id', $product->brand_id) == $b->id)>{{ $b->name }}</option>
        @endforeach
      </select>
      <input name="brand_name" value="{{ old('brand_name') }}"
             placeholder="Or type new brand name"
             class="mt-2 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
      <p class="text-xs text-slate-500 mt-1">Select existing brand or type new brand name</p>
            @error('brand_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            @error('brand_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="text-sm text-slate-600">Category</label>
      <select name="category_id" class="mt-1 w-full rounded-xl border-slate-200">
        @foreach($categories as $c)
          <option value="{{ $c->id }}"
            @selected(old('category_id', $product->category_id) == $c->id)>
            {{ $c->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-sm text-slate-600">Unit</label>
      <select name="unit" class="mt-1 w-full rounded-xl border-slate-200">
        @foreach($units as $u)
          <option value="{{ $u }}"
            @selected(old('unit', $product->unit) == $u)>
            {{ $u }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-sm text-slate-600">
        Selling Price
        <span class="text-xs text-slate-400 font-normal ml-1">(default POS price)</span>
      </label>
      <input name="selling_price" type="number" step="any" data-money inputmode="numeric" max="9999999"
             value="{{ old('selling_price', $product->selling_price) }}"
             class="mt-1 w-full rounded-xl border-slate-200" />
    </div>

    <div>
      <label class="text-sm text-slate-600">Reorder Level</label>
      <input name="reorder_level" type="number" step="0.001"
             value="{{ old('reorder_level', $product->stock->reorder_level ?? 0) }}"
             class="mt-1 w-full rounded-xl border-slate-200" />
    </div>
  </div>

  <div class="flex items-center gap-6">
    <label class="flex items-center gap-2 text-sm">
      <input type="checkbox" name="is_active" value="1"
        @checked(old('is_active', $product->is_active))>
      Active
    </label>
  </div>

  <div class="flex gap-3">
    <button class="px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
      Update Product
    </button>

    <a href="{{ route('inventory.products.index') }}" data-back-button
       class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Cancel
    </a>
  </div>
</form>
@endsection
