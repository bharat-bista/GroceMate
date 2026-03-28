@extends('inventory.layouts.inventory')

@section('title','Expiry Alerts')
@section('heading','Expiry Alerts')
@section('subtitle','Expiring soon and expired batches')

@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
  <form method="GET" action="{{ route('inventory.alerts.expiry') }}"
        class="flex flex-col sm:flex-row gap-2 sm:items-end">
    <div>
      <label class="text-sm text-slate-600">Days (expiring soon)</label>
      <input type="number" min="1" name="days" value="{{ $days }}"
             class="mt-1 w-40 rounded-xl border-slate-200" />
    </div>
    <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Apply
    </button>
  </form>

  <a href="{{ route('inventory.dashboard') }}"
     data-back-button
     class="inline-flex items-center px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700">
    Back
  </a>
</div>

<div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-4">
  <!-- Expiring Soon -->
  <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
    <div class="p-5 border-b border-slate-200">
      <div class="font-semibold">Expiring Soon ({{ $days }} days)</div>
    </div>
    <table class="w-full text-sm">
      <thead class="text-slate-500 bg-slate-50">
        <tr>
          <th class="text-left px-5 py-3">Product</th>
          <th class="text-left px-5 py-3">Qty</th>
          <th class="text-left px-5 py-3">Expiry</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($expiringSoon as $it)
          <tr>
            <td class="px-5 py-4 font-semibold">{{ $it->product->name ?? '—' }}</td>
            <td class="px-5 py-4">{{ $it->qty }}</td>
            <td class="px-5 py-4">{{ $it->expiry_date?->format('Y-m-d') }}</td>
          </tr>
        @empty
          <tr><td class="px-5 py-6 text-slate-500" colspan="3">No expiring items.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4">{{ $expiringSoon->links() }}</div>
  </div>

  <!-- Expired -->
  <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
    <div class="p-5 border-b border-slate-200">
      <div class="font-semibold">Expired</div>
    </div>
    <table class="w-full text-sm">
      <thead class="text-slate-500 bg-slate-50">
        <tr>
          <th class="text-left px-5 py-3">Product</th>
          <th class="text-left px-5 py-3">Qty</th>
          <th class="text-left px-5 py-3">Expiry</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($expired as $it)
          <tr>
            <td class="px-5 py-4 font-semibold">{{ $it->product->name ?? '—' }}</td>
            <td class="px-5 py-4">{{ $it->qty }}</td>
            <td class="px-5 py-4">{{ $it->expiry_date?->format('Y-m-d') }}</td>
          </tr>
        @empty
          <tr><td class="px-5 py-6 text-slate-500" colspan="3">No expired items.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4">{{ $expired->links() }}</div>
  </div>
</div>
@endsection
