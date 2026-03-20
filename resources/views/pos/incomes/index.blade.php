@extends('inventory.layouts.inventory')

@section('title','Income Dashboard')
@section('heading','Income Dashboard')
@section('subtitle','Business income analytics and transaction overview')

@section('content')
<div class="space-y-6">

    {{-- Messages --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-100 text-red-700 border border-red-200 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Income</p>
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($totalIncome, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">This Month</p>
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($thisMonthIncome, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Today</p>
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($todayIncome, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- Business Stats + Payment Methods --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Business Income & Balance</h3>
            <div class="space-y-4">
                @forelse($businessIncomeStats as $business)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($business->business_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $business->business_name }}</p>
                                <p class="text-sm text-slate-500">
                                    {{ $business->business_type }} • {{ $business->incomes_count }} transactions this month
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-slate-600">
                                Balance:
                                <span class="font-bold {{ ($business->current_balance ?? 0) >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                    Rs {{ number_format($business->current_balance ?? 0, 2) }}
                                </span>
                            </p>
                            <p class="text-xs text-slate-500 mt-1">Net balance (income − expenses)</p>
                            <div class="w-24 bg-slate-200 rounded-full h-2 mt-1">
                                @if($thisMonthIncome > 0)
                                    <div class="bg-emerald-500 h-2 rounded-full"
                                         style="width: {{ min(100, max(0, (($business->incomes_sum_amount_received ?? 0) / $thisMonthIncome) * 100)) }}%">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p>No business income data available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Payment Methods</h3>
            <div class="space-y-3">
                @forelse($paymentStats as $payment)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full
                                @if($payment->payment_method == 'cash') bg-green-500
                                @elseif($payment->payment_method == 'bank') bg-blue-500
                                @elseif(in_array($payment->payment_method, ['Esewa','esewa'])) bg-purple-500
                                @elseif(in_array($payment->payment_method, ['Khalti','khalti'])) bg-orange-500
                                @else bg-slate-500
                                @endif">
                            </div>
                            <span class="text-sm font-medium text-slate-700">
                                {{ ucfirst($payment->payment_method) }}
                            </span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-800">
                                Rs {{ number_format($payment->total, 2) }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $payment->count }} tx</p>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-4">No payment data</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── Income Transactions Table ── --}}
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">

        {{-- Header --}}
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">💰 Income Transactions</h2>
                    <p class="text-sm text-slate-600 mt-1">Sales, due collections & other income records</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('pos.income.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Income
                    </a>
                    
                    <!-- Bulk Export Dropdown -->
                    <div class="relative inline-block text-left">
                        <button type="button" id="incomeExportDropdown" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 2v6m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export All
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div id="incomeExportMenu" class="hidden origin-top-right absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 border border-gray-200">
                            <div class="py-2">
                                <!-- Date Range Inputs -->
                                <div class="px-4 py-2 border-b border-gray-200">
                                    <div class="space-y-2">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">From Date</label>
                                            <input type="date" id="exportFromDate" value="{{ request('date_from') }}" 
                                                   class="w-full text-xs border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">To Date</label>
                                            <input type="date" id="exportToDate" value="{{ request('date_to') }}" 
                                                   class="w-full text-xs border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Export Options -->
                                <div class="py-1">
                                    <button onclick="exportIncome('pdf')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                        Export as PDF
                                    </button>
                                    <button onclick="exportIncome('excel')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                        Export as Excel
                                    </button>
                                    <button onclick="exportIncome('csv')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                        Export as CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search and Filters --}}
        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <form method="GET" action="{{ route('pos.income.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Customer name, reference, notes..."
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Income Type</label>
                        <select name="income_type"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Types</option>
                            <option value="Sale"           @selected(request('income_type') == 'Sale')>Sale</option>
                            <option value="Due Collection" @selected(request('income_type') == 'Due Collection')>Due Collection</option>
                            <option value="Other"          @selected(request('income_type') == 'Other')>Other</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                        Search
                    </button>
                    <a href="{{ route('pos.income.index') }}"
                       class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Results count --}}
        @if($incomes->count() > 0)
            <div class="text-xs text-slate-500 px-6 pt-4 pb-2">
                Showing {{ $incomes->firstItem() }} to {{ $incomes->lastItem() }} of {{ $incomes->total() }} results
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">#</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Reference</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Customer</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Business</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Amount</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Payment Method</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Type</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Notes</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($incomes as $index => $income)
                        <tr class="hover:bg-slate-50">

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-500">
                                    {{ ($incomes->currentPage() - 1) * $incomes->perPage() + $index + 1 }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">
                                    {{ $income->reference_no ?? 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="text-xs text-slate-400">ID: {{ $income->id }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">
                                    {{ \Carbon\Carbon::parse($income->transaction_date ?? $income->created_at)->format('M d, Y') }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($income->customer)
                                    <div class="text-sm font-medium text-slate-900">{{ $income->customer->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $income->customer->phone ?? '' }}</div>
                                    @if($income->customer->total_due > 0)
                                        <div class="text-xs text-amber-600 font-semibold">
                                            Due: Rs {{ number_format($income->customer->total_due, 2) }}
                                        </div>
                                    @else
                                        <div class="text-xs text-emerald-600">No Due</div>
                                    @endif
                                @else
                                    <div class="text-sm text-slate-400">N/A</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($income->business)
                                    <div class="text-sm text-slate-900">{{ $income->business->business_name }}</div>
                                    <div class="text-xs text-slate-400">{{ $income->business->business_type ?? '' }}</div>
                                @else
                                    <div class="text-sm text-slate-400">N/A</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-emerald-600">
                                    + Rs {{ number_format($income->amount_received, 2) }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($income->payment_method == 'cash') bg-green-100 text-green-800
                                    @elseif($income->payment_method == 'bank') bg-purple-100 text-purple-800
                                    @elseif(in_array($income->payment_method, ['Esewa','esewa'])) bg-indigo-100 text-indigo-800
                                    @elseif(in_array($income->payment_method, ['Khalti','khalti','khalti_external'])) bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($income->payment_method) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($income->income_type == 'Sale')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Sale</span>
                                @elseif($income->income_type == 'Due Collection')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Due Collection</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $income->income_type ?? 'Other' }}</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($income->notes || $income->description)
                                    <div class="text-xs text-slate-500 max-w-xs truncate"
                                         title="{{ $income->notes ?? $income->description }}">
                                        {{ $income->notes ?? $income->description }}
                                    </div>
                                @else
                                    <div class="text-sm text-slate-400">-</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-3">
                                    <a href="{{ route('pos.income.edit', $income) }}"
                                       class="text-blue-600 hover:text-blue-900 font-medium">Edit</a>
                                    <form method="POST"
                                          action="{{ route('pos.income.destroy', $income) }}"
                                          class="inline"
                                          onsubmit="return confirm('Delete this income record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-900 font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No income records found</p>
                                    <p class="text-sm mt-1">Try adjusting your filters or add a new income record.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('pos.income.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Add Income
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($incomes->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $incomes->appends(request()->query())->links() }}
            </div>
        @endif

    </div>

</div>

<script>
// Export dropdown functionality
const incomeExportDropdown = document.getElementById('incomeExportDropdown');
const incomeExportMenu = document.getElementById('incomeExportMenu');

incomeExportDropdown.addEventListener('click', function() {
    incomeExportMenu.classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!incomeExportDropdown.contains(e.target) && !incomeExportMenu.contains(e.target)) {
        incomeExportMenu.classList.add('hidden');
    }
});

// Export function with date range
function exportIncome(type) {
    const fromDate = document.getElementById('exportFromDate').value;
    const toDate = document.getElementById('exportToDate').value;
    
    // Get current filter parameters
    const currentParams = new URLSearchParams(window.location.search);
    
    // Build export URL with all parameters
    const exportParams = new URLSearchParams();
    
    // Add current filters
    if (currentParams.get('q')) exportParams.set('q', currentParams.get('q'));
    if (currentParams.get('income_type')) exportParams.set('income_type', currentParams.get('income_type'));
    
    // Add date range from export inputs
    if (fromDate) exportParams.set('from', fromDate);
    if (toDate) exportParams.set('to', toDate);
    
    // Build the export URL
    const exportUrl = `/pos/income/export/${type}?${exportParams.toString()}`;
    
    // Navigate to export URL
    window.location.href = exportUrl;
}
</script>
@endsection