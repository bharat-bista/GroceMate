@extends('inventory.layouts.inventory')

@section('title','Brands')
@section('heading','Brands')
@section('subtitle','Create and manage product brands')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
  <form class="flex gap-2" method="GET" action="{{ route('inventory.brands.index') }}">
    <input name="q" value="{{ $q }}" placeholder="Search brand..."
           class="w-full md:w-80 rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-200" />
    <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Search
    </button>
  </form>

  <a href="{{ route('inventory.brands.create') }}"
     class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
    + Add Brand
  </a>
</div>

<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="text-slate-500 bg-slate-50">
      <tr>
        <th class="text-left px-5 py-3">Order</th>
        <th class="text-left px-5 py-3">Brand Name</th>
        <th class="text-left px-5 py-3">Image</th>
        <th class="text-left px-5 py-3">Company Discount</th>
        <th class="text-right px-5 py-3">Actions</th>
      </tr>
    </thead>

    <tbody class="divide-y">
      @forelse($brands as $brand)
        <tr>
          <td class="px-5 py-4">{{ $brand->order }}</td>
          <td class="px-5 py-4 font-semibold">{{ $brand->name }}</td>
          <td class="px-5 py-4">
            @if($brand->image)
              <img src="{{ asset('assets/img/brands/' . $brand->image) }}" alt="{{ $brand->name }}" class="w-12 h-12 object-cover rounded-lg">
            @else
              <span class="text-slate-400">No image</span>
            @endif
          </td>
          <td class="px-5 py-4">{{ $brand->company_discount }}%</td>
          <td class="px-5 py-4 text-right">
            <div class="inline-flex items-center gap-3">
              <a class="underline text-slate-800" href="{{ route('inventory.brands.edit',$brand) }}">Edit</a>

              <form method="POST" action="{{ route('inventory.brands.destroy',$brand) }}"
                    onsubmit="return confirm('Delete this brand?');">
                @csrf
                @method('DELETE')
                <button class="text-red-700 underline">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td class="px-5 py-6 text-slate-500" colspan="5">No brands found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $brands->links() }}
</div>
@endsection
