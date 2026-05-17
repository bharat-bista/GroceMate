@extends('inventory.layouts.inventory')

@section('title', 'Purchase Details')
@section('heading', 'Purchase Details')
@section('subtitle', 'View purchase details and stock-in items')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-700 p-6 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">
                        Purchase #{{ $purchase->invoice_no ?? 'PUR-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}
                    </h2>
                    <p class="text-sm opacity-90">Stock-In Entry - {{ $purchase->purchase_date->format('M d, Y') }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <div class="relative inline-block text-left">
                        <button type="button" id="exportDropdown"
                                class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 text-white rounded-lg hover:bg-opacity-30 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div id="exportMenu"
                             class="hidden origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 border border-gray-200">
                            <div class="py-1">
                                <a href="{{ route('inventory.purchases.export-individual', ['purchase' => $purchase->id, 'type' => 'pdf']) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">PDF</a>
                                <a href="{{ route('inventory.purchases.export-individual', ['purchase' => $purchase->id, 'type' => 'excel']) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">Excel</a>
                                <a href="{{ route('inventory.purchases.export-individual', ['purchase' => $purchase->id, 'type' => 'csv']) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">CSV</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Business Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-slate-600">Business:</span>
                        <span class="text-sm font-medium text-slate-900 text-right">{{ $purchase->business->business_name ?? 'N/A' }}</span>
                    </div>
                    @if($purchase->business)
                        <div class="flex justify-between gap-4">
                            <span class="text-sm text-slate-600">Type:</span>
                            <span class="text-sm font-medium text-slate-900 text-right">{{ $purchase->business->business_type ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-sm text-slate-600">Phone:</span>
                            <span class="text-sm font-medium text-slate-900 text-right">{{ $purchase->business->phone ?? 'N/A' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Supplier Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-slate-600">Supplier:</span>
                        <span class="text-sm font-medium text-slate-900 text-right">{{ $purchase->supplier->name ?? 'N/A' }}</span>
                    </div>
                    @if($purchase->supplier)
                        <div class="flex justify-between gap-4">
                            <span class="text-sm text-slate-600">Phone:</span>
                            <span class="text-sm font-medium text-slate-900 text-right">{{ $purchase->supplier->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-sm text-slate-600">Email:</span>
                            <span class="text-sm font-medium text-slate-900 text-right">{{ $purchase->supplier->email ?? 'N/A' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Purchase Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-slate-600">Invoice No:</span>
                        <span class="text-sm font-medium text-slate-900 text-right">
                            {{ $purchase->invoice_no ?? 'PUR-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-slate-600">Purchase Date:</span>
                        <span class="text-sm font-medium text-slate-900 text-right">{{ $purchase->purchase_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-slate-600">Created By:</span>
                        <span class="text-sm font-medium text-slate-900 text-right">{{ $purchase->creator->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-sm text-slate-600">Items:</span>
                        <span class="text-sm font-medium text-slate-900 text-right">{{ $purchase->items->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Purchase Items</h3>

            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="text-slate-700 bg-slate-100">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium">Product</th>
                            <th class="text-left px-4 py-3 font-medium">Batch No</th>
                            <th class="text-left px-4 py-3 font-medium">Unit</th>
                            <th class="text-left px-4 py-3 font-medium">Quantity</th>
                            <th class="text-left px-4 py-3 font-medium">Unit Cost</th>
                            <th class="text-left px-4 py-3 font-medium">Expiry</th>
                            <th class="text-left px-4 py-3 font-medium">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($purchase->items as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $item->product_name ?? $item->product->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ $item->batch_no ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $item->unit ?? $item->product->unit ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ number_format($item->qty, 3) }}</td>
                                <td class="px-4 py-3">Rs {{ number_format((float) $item->unit_cost, 2) }}</td>
                                <td class="px-4 py-3">{{ $item->expiry_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">Rs {{ number_format((float) $item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-300">
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-right font-semibold text-slate-700">Subtotal:</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">Rs {{ number_format((float) $purchase->items->sum('line_total'), 2) }}</td>
                        </tr>
                        @if($purchase->total_cost > $purchase->items->sum('line_total'))
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-right font-semibold text-slate-700">Tax Applied:</td>
                                <td class="px-4 py-3 font-semibold text-red-600">
                                    Rs {{ number_format((float) ($purchase->total_cost - $purchase->items->sum('line_total')), 2) }}
                                </td>
                            </tr>
                        @endif
                        <tr class="bg-slate-100">
                            <td colspan="6" class="px-4 py-4 text-right font-bold text-lg text-slate-900">Total Amount:</td>
                            <td class="px-4 py-4 font-bold text-lg text-green-700">Rs {{ number_format((float) $purchase->total_cost, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="p-6 border-t border-slate-200 flex gap-3">
            <a href="{{ route('inventory.purchases.index') }}" data-back-button
               class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
                Back to Purchases
            </a>
        </div>
    </div>
</div>

<script>
const exportDropdown = document.getElementById('exportDropdown');
const exportMenu = document.getElementById('exportMenu');

if (exportDropdown && exportMenu) {
    exportDropdown.addEventListener('click', function () {
        exportMenu.classList.toggle('hidden');
    });
}

document.addEventListener('click', function (e) {
    if (exportDropdown && exportMenu && !exportDropdown.contains(e.target) && !exportMenu.contains(e.target)) {
        exportMenu.classList.add('hidden');
    }
});
</script>
@endsection
