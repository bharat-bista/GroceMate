@extends('inventory.layouts.inventory')

@section('title','Expiry Alerts')
@section('heading','Expiry Alerts')
@section('subtitle','Expiring soon and expired batches')

@section('content')
<div class="space-y-6">
  <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-200">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold text-slate-900">Stock Expiry Monitor</h2>
          <p class="text-sm text-slate-600 mt-1">Track soon-to-expire and expired purchase batches</p>
        </div>
        <a href="{{ route('inventory.dashboard') }}" data-back-button
           class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 text-sm font-medium">
          Back
        </a>
      </div>
    </div>

    <div class="p-6 bg-slate-50 border-b border-slate-200">
      <form method="GET" action="{{ route('inventory.alerts.expiry') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Days (expiring soon)</label>
            <input type="number" min="1" name="days" value="{{ $days }}"
                   class="w-full md:w-60 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
          </div>
          <div class="flex items-end gap-3">
            <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Apply</button>
            <a href="{{ route('inventory.alerts.expiry') }}" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium">Reset</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <!-- Expiring Soon -->
  <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
    <div class="p-5 border-b border-slate-200">
      <div class="font-semibold text-slate-900">Expiring Soon ({{ $days }} days)</div>
    </div>
    <table class="w-full text-sm">
      <thead class="bg-slate-100 border-b border-slate-200">
        <tr>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Product</th>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Company</th>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Business</th>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Purchase Date</th>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Qty</th>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Expiry</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200">
        @forelse($expiringSoon as $it)
          <tr class="hover:bg-slate-50">
            <td class="px-5 py-4 font-semibold">{{ $it->product->name ?? '—' }}</td>
            <td class="px-5 py-4">{{ $it->product->brandRelation->name ?? $it->company_name ?? '—' }}</td>
            <td class="px-5 py-4">{{ $it->purchase->business->business_name ?? '—' }}</td>
            <td class="px-5 py-4">{{ $it->purchase->purchase_date?->format('Y-m-d') ?? '—' }}</td>
            <td class="px-5 py-4">{{ $it->qty }}</td>
            <td class="px-5 py-4">{{ $it->expiry_date?->format('Y-m-d') }}</td>
          </tr>
        @empty
          <tr><td class="px-5 py-10 text-center text-slate-500" colspan="6">No expiring items.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4 bg-slate-50 border-t border-slate-200">{{ $expiringSoon->links() }}</div>
  </div>

  <!-- Expired -->
  <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
    <div class="p-5 border-b border-slate-200">
      <div class="font-semibold text-slate-900">Expired</div>
    </div>
    <table class="w-full text-sm">
      <thead class="bg-slate-100 border-b border-slate-200">
        <tr>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Product</th>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Company</th>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Business</th>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Purchase Date</th>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Qty</th>
          <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Expiry</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200">
        @forelse($expired as $it)
          <tr class="hover:bg-slate-50">
            <td class="px-5 py-4 font-semibold">{{ $it->product->name ?? '—' }}</td>
            <td class="px-5 py-4">{{ $it->product->brandRelation->name ?? $it->company_name ?? '—' }}</td>
            <td class="px-5 py-4">{{ $it->purchase->business->business_name ?? '—' }}</td>
            <td class="px-5 py-4">{{ $it->purchase->purchase_date?->format('Y-m-d') ?? '—' }}</td>
            <td class="px-5 py-4">{{ $it->qty }}</td>
            <td class="px-5 py-4">{{ $it->expiry_date?->format('Y-m-d') }}</td>
          </tr>
        @empty
          <tr><td class="px-5 py-10 text-center text-slate-500" colspan="6">No expired items.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4 bg-slate-50 border-t border-slate-200">{{ $expired->links() }}</div>
  </div>
</div>
</div>
@endsection
