@extends('inventory.layouts.inventory')

@section('title', 'Stock Batches')
@section('heading', 'Stock Batches')
@section('subtitle', 'Batch breakdown and valuation')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">{{ $product->name }}</h2>
                    <p class="text-sm text-slate-600 mt-1">Unit: {{ $product->unit }}</p>
                </div>
                <a href="{{ route('inventory.products.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 text-sm font-medium">
                    Back to Products
                </a>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <form method="GET" action="{{ route('inventory.stock.batches', $product) }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                        <select name="status"
                                class="w-full md:w-60 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All</option>
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="depleted" @selected($status === 'depleted')>Depleted</option>
                            <option value="expired" @selected($status === 'expired')>Expired</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-3">
                        <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Apply</button>
                        <a href="{{ route('inventory.stock.batches', $product) }}"
                           class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-100 border-b border-slate-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Batch No</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Purchased On</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Qty Received</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Qty Remaining</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Unit Cost</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Expiry Date</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($batches as $batch)
                    @php
                        $statusClasses = [
                            'active' => 'bg-emerald-100 text-emerald-700',
                            'depleted' => 'bg-slate-100 text-slate-700',
                            'expired' => 'bg-red-100 text-red-700',
                        ];
                        $statusClass = $statusClasses[$batch->status] ?? 'bg-slate-100 text-slate-700';
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 font-semibold">{{ $batch->batch_no }}</td>
                        <td class="px-5 py-4">{{ $batch->purchased_on?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-5 py-4">{{ number_format((float) $batch->qty_received, 3) }}</td>
                        <td class="px-5 py-4">{{ number_format((float) $batch->qty_remaining, 3) }}</td>
                        <td class="px-5 py-4">Rs {{ number_format((float) $batch->unit_cost, 2) }}</td>
                        <td class="px-5 py-4">{{ $batch->expiry_date?->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($batch->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-5 py-10 text-center text-slate-500" colspan="7">No batches found.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-50 border-t border-slate-200">
                <tr>
                    <td colspan="6" class="px-5 py-4 text-right font-semibold text-slate-700">Active Stock Valuation:</td>
                    <td class="px-5 py-4 font-semibold text-emerald-700">Rs {{ number_format((float) $valuationTotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        @if($batches->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-200">{{ $batches->links() }}</div>
        @endif
    </div>
</div>
@endsection
