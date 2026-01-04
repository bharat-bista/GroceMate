@extends('inventory.layouts.inventory')


@section('title','Inventory Dashboard')
@section('heading','Dashboard')
@section('subtitle','Overview of products & alerts')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <div class="text-sm text-slate-500">Total Products</div>
    <div class="text-3xl font-bold mt-2">{{ $totalProducts }}</div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <div class="text-sm text-slate-500">Active Products</div>
    <div class="text-3xl font-bold mt-2">{{ $activeProducts }}</div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <div class="text-sm text-slate-500">Low Stock</div>
    <div class="text-3xl font-bold mt-2">{{ $lowStockCount }}</div>
  </div>
</div>

<div class="mt-6 bg-white border border-slate-200 rounded-2xl shadow-sm">
  <div class="p-5 border-b border-slate-200 flex items-center justify-between">
    <div class="font-semibold">Low Stock Items</div>
    <a class="text-sm text-slate-700 underline" href="{{ route('inventory.products.index') }}">View Products</a>
  </div>
  <div class="p-5 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-slate-500">
        <tr>
          <th class="text-left py-2">Product</th>
          <th class="text-left py-2">Qty</th>
          <th class="text-left py-2">Reorder Level</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($topLowStock as $row)
          <tr>
            <td class="py-3">{{ $row->product->name ?? '—' }}</td>
            <td class="py-3 font-semibold">{{ $row->quantity }}</td>
            <td class="py-3">{{ $row->reorder_level }}</td>
          </tr>
        @empty
          <tr><td class="py-3 text-slate-500" colspan="3">No low stock items.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
