@extends('inventory.layouts.inventory')

@section('title', 'Purchases')
@section('heading', 'Purchases (Stock-In)')
@section('subtitle', 'Record purchases and increase stock')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden mb-6">
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Purchase Records</h2>
                    <p class="text-sm text-slate-600 mt-1">Manage and review all stock-in purchases</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('inventory.purchases.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Purchase
                    </a>

                    <div class="relative inline-block text-left">
                        <button type="button" id="exportDropdown"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export All
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div id="exportMenu"
                             class="hidden origin-top-right absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 border border-gray-200 p-3 space-y-3">
                            <div class="flex gap-2">
                                <button type="button" id="exportModeAll"
                                        class="flex-1 px-3 py-1.5 rounded-lg text-sm bg-blue-600 text-white">
                                    All
                                </button>
                                <button type="button" id="exportModeRange"
                                        class="flex-1 px-3 py-1.5 rounded-lg text-sm bg-slate-100 text-slate-700">
                                    Date Range
                                </button>
                            </div>

                            <div id="exportDateRange" class="hidden space-y-2">
                                <div>
                                    <label for="exportFromDate" class="block text-xs font-medium text-slate-600 mb-1">From Date</label>
                                    <input type="date" id="exportFromDate" value="{{ request('date_from', $dateFrom) }}"
                                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="exportToDate" class="block text-xs font-medium text-slate-600 mb-1">To Date</label>
                                    <input type="date" id="exportToDate" value="{{ request('date_to', $dateTo) }}"
                                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                                <div id="exportDateWarning" class="hidden text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                                    Please select both from and to dates.
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2 pt-1">
                                <a href="{{ route('inventory.purchases.export', ['type' => 'pdf']) }}"
                                   data-export-type="pdf"
                                   class="text-center px-3 py-2 rounded-lg bg-slate-100 text-sm text-slate-700 hover:bg-slate-200">PDF</a>
                                <a href="{{ route('inventory.purchases.export', ['type' => 'excel']) }}"
                                   data-export-type="excel"
                                   class="text-center px-3 py-2 rounded-lg bg-slate-100 text-sm text-slate-700 hover:bg-slate-200">Excel</a>
                                <a href="{{ route('inventory.purchases.export', ['type' => 'csv']) }}"
                                   data-export-type="csv"
                                   class="text-center px-3 py-2 rounded-lg bg-slate-100 text-sm text-slate-700 hover:bg-slate-200">CSV</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <form method="GET" action="{{ route('inventory.purchases.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search', $q) }}"
                               placeholder="Invoice, supplier, or business..."
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Business</label>
                        <select name="business_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">All Businesses</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}" @selected((string) request('business_id', $businessId) === (string) $business->id)>{{ $business->business_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from', $dateFrom) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to', $dateTo) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        Search
                    </button>
                    <a href="{{ route('inventory.purchases.index') }}"
                       class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Invoice No</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Supplier</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Business</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Created By</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Total Cost</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Payment</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">
                                    {{ $purchase->invoice_no ?? 'PUR-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">{{ $purchase->purchase_date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">{{ $purchase->supplier->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">{{ $purchase->business->business_name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">{{ $purchase->creator->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-900">Rs {{ number_format((float) $purchase->total_cost, 0) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $pm = $purchase->payment_method;
                                    [$bg, $text] = match($pm) {
                                        'cash'   => ['bg-emerald-100', 'text-emerald-800'],
                                        'credit' => ['bg-amber-100',   'text-amber-800'],
                                        'bank'   => ['bg-blue-100',    'text-blue-800'],
                                        default  => ['bg-slate-100',   'text-slate-600'],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bg }} {{ $text }}">
                                    {{ ucfirst($pm ?? '—') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-2">
                                    <a href="{{ route('inventory.purchases.show', $purchase) }}"
                                       class="text-green-600 hover:text-green-900 font-medium">View</a>

                                    <div class="relative inline-block text-left">
                                        <button type="button"
                                                class="text-blue-600 hover:text-blue-900 font-medium export-btn">
                                            Export
                                            <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <div class="hidden export-menu origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 border border-gray-200">
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4H5m14 8H9m10 4H5"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No purchases found</p>
                                    <p class="text-sm mt-1">Get started by creating your first purchase entry.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('inventory.purchases.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Create Purchase
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $purchases->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<script>
const exportDropdown = document.getElementById('exportDropdown');
const exportMenu = document.getElementById('exportMenu');
const exportModeAll = document.getElementById('exportModeAll');
const exportModeRange = document.getElementById('exportModeRange');
const exportDateRange = document.getElementById('exportDateRange');
const exportFromDate = document.getElementById('exportFromDate');
const exportToDate = document.getElementById('exportToDate');
const exportDateWarning = document.getElementById('exportDateWarning');
const exportLinks = document.querySelectorAll('[data-export-type]');
let exportMode = 'all';

if (exportDropdown && exportMenu) {
    exportDropdown.addEventListener('click', function () {
        exportMenu.classList.toggle('hidden');
    });
}

function updateExportModeUi() {
    if (!exportModeAll || !exportModeRange || !exportDateRange) {
        return;
    }

    if (exportMode === 'all') {
        exportModeAll.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-blue-600 text-white';
        exportModeRange.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-slate-100 text-slate-700';
        exportDateRange.classList.add('hidden');
        exportDateWarning?.classList.add('hidden');
    } else {
        exportModeAll.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-slate-100 text-slate-700';
        exportModeRange.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-blue-600 text-white';
        exportDateRange.classList.remove('hidden');
    }
}

function getPurchaseExportUrl(type) {
    const baseUrl = "{{ route('inventory.purchases.export', ['type' => 'TYPE']) }}".replace('TYPE', type);
    const businessId = document.querySelector('select[name="business_id"]')?.value || '';
    const url = new URL(baseUrl, window.location.origin);

    if (businessId) {
        url.searchParams.set('business_id', businessId);
    }

    if (exportMode === 'all') {
        return url.toString();
    }

    const from = exportFromDate?.value;
    const to = exportToDate?.value;

    if (!from || !to) {
        exportDateWarning?.classList.remove('hidden');
        return null;
    }

    exportDateWarning?.classList.add('hidden');

    url.searchParams.set('from', from);
    url.searchParams.set('to', to);

    return url.toString();
}

if (exportModeAll && exportModeRange) {
    exportModeAll.addEventListener('click', function () {
        exportMode = 'all';
        updateExportModeUi();
    });

    exportModeRange.addEventListener('click', function () {
        exportMode = 'range';
        updateExportModeUi();
    });
}

if (exportFromDate && exportToDate) {
    [exportFromDate, exportToDate].forEach(input => {
        input.addEventListener('input', function () {
            if (exportFromDate.value && exportToDate.value) {
                exportDateWarning?.classList.add('hidden');
            }
        });
    });
}

exportLinks.forEach(link => {
    link.addEventListener('click', function (e) {
        const exportUrl = getPurchaseExportUrl(this.dataset.exportType);
        if (!exportUrl) {
            e.preventDefault();
            return;
        }

        this.setAttribute('href', exportUrl);
    });
});

updateExportModeUi();

document.querySelectorAll('.export-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const menu = this.nextElementSibling;

        document.querySelectorAll('.export-menu').forEach(existingMenu => {
            if (existingMenu !== menu) {
                existingMenu.classList.add('hidden');
            }
        });

        menu.classList.toggle('hidden');
    });
});

document.addEventListener('click', function (e) {
    if (exportDropdown && exportMenu && !exportDropdown.contains(e.target) && !exportMenu.contains(e.target)) {
        exportMenu.classList.add('hidden');
    }

    document.querySelectorAll('.export-menu').forEach(menu => {
        const btn = menu.previousElementSibling;
        if (!btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });
});
</script>
@endsection
