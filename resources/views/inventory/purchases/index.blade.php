@extends('inventory.layouts.inventory')

@section('title','Purchases')
@section('heading','Purchases (Stock-In)')
@section('subtitle','Record purchases and increase stock')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
  <form class="flex gap-2" method="GET" action="{{ route('inventory.purchases.index') }}">
    <input name="q" value="{{ $q }}" placeholder="Search supplier or invoice..."
           class="w-full md:w-80 rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-200" />
    <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Search
    </button>
  </form>

  <a href="{{ route('inventory.purchases.create') }}"
     class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
    + New Purchase
  </a>
</div>

<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="text-slate-500 bg-slate-50">
      <tr>
        <th class="text-left px-5 py-3">Date</th>
        <th class="text-left px-5 py-3">Supplier</th>
        <th class="text-left px-5 py-3">Invoice No</th>
        <th class="text-left px-5 py-3">Total Cost</th>
        <th class="text-left px-5 py-3">Created By</th>
        <th class="text-right px-5 py-3">Action</th>
      </tr>
    </thead>

    <tbody class="divide-y">
      @forelse($purchases as $p)
        <tr>
          <td class="px-5 py-4">{{ $p->purchase_date->format('Y-m-d') }}</td>
          <td class="px-5 py-4 font-semibold">{{ $p->supplier->name ?? '—' }}</td>
          <td class="px-5 py-4">{{ $p->invoice_no ?? '—' }}</td>
          <td class="px-5 py-4">Rs {{ number_format((float)$p->total_cost, 2) }}</td>
          <td class="px-5 py-4">{{ $p->creator->name ?? '—' }}</td>
          <td class="px-5 py-4 text-right">
            <a class="underline" href="{{ route('inventory.purchases.show',$p) }}">View</a>
          </td>
        </tr>
      @empty
        <tr><td class="px-5 py-6 text-slate-500" colspan="6">No purchases found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $purchases->links() }}
</div>
@endsection
