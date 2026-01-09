@extends('inventory.layouts.inventory')

@section('title','Purchase Detail')
@section('heading','Purchase Detail')
@section('subtitle','View items, costs and expiry dates')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <div class="text-sm text-slate-500">Supplier</div>
      <div class="font-semibold">{{ $purchase->supplier->name ?? '—' }}</div>
    </div>
    <div>
      <div class="text-sm text-slate-500">Purchase Date</div>
      <div class="font-semibold">{{ $purchase->purchase_date->format('Y-m-d') }}</div>
    </div>
    <div>
      <div class="text-sm text-slate-500">Invoice No</div>
      <div class="font-semibold">{{ $purchase->invoice_no ?? '—' }}</div>
    </div>
  </div>

  <div class="mt-4">
    <div class="text-sm text-slate-500">Total Cost</div>
    <div class="text-2xl font-bold">Rs {{ number_format((float)$purchase->total_cost, 2) }}</div>
  </div>
</div>

<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="text-slate-500 bg-slate-50">
      <tr>
        <th class="text-left px-5 py-3">Product</th>
        <th class="text-left px-5 py-3">Qty</th>
        <th class="text-left px-5 py-3">Unit Cost</th>
        <th class="text-left px-5 py-3">Expiry</th>
        <th class="text-left px-5 py-3">Line Total</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      @foreach($purchase->items as $it)
        <tr>
          <td class="px-5 py-4 font-semibold">{{ $it->product->name ?? '—' }}</td>
          <td class="px-5 py-4">{{ $it->qty }}</td>
          <td class="px-5 py-4">Rs {{ number_format((float)$it->unit_cost, 2) }}</td>
          <td class="px-5 py-4">{{ $it->expiry_date?->format('Y-m-d') ?? '—' }}</td>
          <td class="px-5 py-4">Rs {{ number_format((float)$it->line_total, 2) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">
  <a href="{{ route('inventory.purchases.index') }}" class="underline">← Back to Purchases</a>
</div>
@endsection
