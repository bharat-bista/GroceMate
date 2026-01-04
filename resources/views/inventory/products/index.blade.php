@extends('inventory.layouts.inventory')

@section('title','Products')
@section('heading','Products')
@section('subtitle','Manage products and publish to E-commerce')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
  <form class="flex gap-2" method="GET" action="{{ route('inventory.products.index') }}">
    <input name="q" value="{{ $q }}" placeholder="Search product / SKU..."
           class="w-full md:w-80 rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-200" />
    <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Search
    </button>
  </form>

  <a href="{{ route('inventory.products.create') }}"
     class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
    + Add Product
  </a>
</div>

<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="text-slate-500 bg-slate-50">
      <tr>
        <th class="text-left px-5 py-3">Product</th>
        <th class="text-left px-5 py-3">Category</th>
        <th class="text-left px-5 py-3">Price</th>
        <th class="text-left px-5 py-3">Stock</th>
        <th class="text-left px-5 py-3">E-com</th>
        <th class="text-left px-5 py-3">Status</th>
        <th class="text-right px-5 py-3">Action</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @foreach($products as $p)
        <tr>
          <td class="px-5 py-4">
            <div class="font-semibold">{{ $p->name }}</div>
            <div class="text-xs text-slate-500">{{ $p->sku ?? 'No SKU' }} • {{ $p->unit }}</div>
          </td>
          <td class="px-5 py-4">{{ $p->category->name ?? '—' }}</td>
          <td class="px-5 py-4">Rs {{ number_format((float)$p->selling_price, 2) }}</td>
          <td class="px-5 py-4">
            <span class="font-semibold">{{ $p->stock->quantity ?? 0 }}</span>
          </td>
          <td class="px-5 py-4">
            <form method="POST" action="{{ route('inventory.products.toggle-listed',$p) }}">
              @csrf
              <button class="px-3 py-1.5 rounded-lg border
                {{ $p->is_listed ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                {{ $p->is_listed ? 'Listed' : 'Hidden' }}
              </button>
            </form>
          </td>
          <td class="px-5 py-4">
            <span class="px-2 py-1 rounded-lg text-xs
              {{ $p->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
              {{ $p->is_active ? 'Active' : 'Disabled' }}
            </span>
          </td>
          <td class="px-5 py-4 text-right">
            <a class="underline text-slate-800" href="{{ route('inventory.products.edit',$p) }}">Edit</a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $products->links() }}
</div>
@endsection
