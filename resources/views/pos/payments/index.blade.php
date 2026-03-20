@extends('inventory.layouts.inventory')

@section('title','Supplier Payments')
@section('heading','Supplier Payments')
@section('subtitle','Track and manage all supplier payment transactions')

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

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Paid (All Time)</p>
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($totalPaid, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium">This Month</p>
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($thisMonthPaid, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Total Outstanding Due</p>
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($totalOutstandingDue, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Top Suppliers + Payment Methods ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Top Suppliers by Amount Paid --}}
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Top Suppliers by Amount Paid</h3>
            <div class="space-y-4">
                @php $maxPaid = $topSuppliers->max('total_paid') ?: 1; @endphp
                @forelse($topSuppliers as $ts)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($ts->supplier_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $ts->supplier_name }}</p>
                                <p class="text-xs text-slate-500 capitalize">{{ $ts->supplier_type }} · {{ $ts->payment_count }} payments</p>
                            </div>
                        </div>
                        <div class="text-right min-w-[120px]">
                            <p class="text-sm font-bold text-blue-600">Rs {{ number_format($ts->total_paid, 2) }}</p>
                            <div class="w-24 bg-slate-200 rounded-full h-2 mt-1 ml-auto">
                                <div class="bg-blue-500 h-2 rounded-full"
                                     style="width: {{ min(100, ($ts->total_paid / $maxPaid) * 100) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500">
                        <p>No payment data available</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Payment Method Breakdown --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Payment Methods</h3>
            <div class="space-y-3">
                @forelse($paymentMethodStats as $pm)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full
                                @if($pm->payment_method == 'cash') bg-green-500
                                @elseif($pm->payment_method == 'bank') bg-blue-500
                                @elseif(in_array($pm->payment_method, ['esewa','Esewa'])) bg-purple-500
                                @elseif(in_array($pm->payment_method, ['khalti','Khalti','khalti_external'])) bg-orange-500
                                @else bg-slate-400
                                @endif">
                            </div>
                            <span class="text-sm font-medium text-slate-700 capitalize">{{ ucfirst($pm->payment_method) }}</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-800">Rs {{ number_format($pm->total, 2) }}</p>
                            <p class="text-xs text-slate-500">{{ $pm->count }} tx</p>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-4">No payment data</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── Suppliers with Outstanding Due ── --}}
    @if($suppliersWithDue->count() > 0)
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">
            ⚠️ Suppliers with Outstanding Due
        </h3>
        <div class="space-y-3">
            @foreach($suppliersWithDue as $sd)
                <div class="flex items-center justify-between p-4 rounded-xl
                    {{ $sd->total_due > 20000 ? 'bg-red-50 border border-red-200' : 'bg-amber-50 border border-amber-200' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg
                            {{ $sd->total_due > 20000 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ substr($sd->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $sd->name }}</p>
                            <p class="text-xs text-slate-500 capitalize">
                                {{ $sd->supplier_type }}
                                @if($sd->lastPayment)
                                    · Last paid {{ \Carbon\Carbon::parse($sd->lastPayment->date)->diffForHumans() }}
                                @else
                                    · No payments yet
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="font-bold {{ $sd->total_due > 20000 ? 'text-red-600' : 'text-amber-600' }}">
                                Rs {{ number_format($sd->total_due, 2) }}
                            </p>
                            <p class="text-xs text-slate-500">outstanding</p>
                        </div>
                        <a href="{{ route('pos.supplier-payments.create') }}?supplier_id={{ $sd->id }}"
                           class="px-3 py-1.5 text-xs font-medium rounded-lg
                               {{ $sd->total_due > 20000 ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-amber-500 text-white hover:bg-amber-600' }}
                               transition duration-150">
                            Pay Now
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    

    {{-- ── Payment Transactions Table ── --}}
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">

        {{-- Header --}}
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">💸 Payment Transactions</h2>
                    <p class="text-sm text-slate-600 mt-1">All supplier payment records</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('pos.supplier-payments.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Payment
                    </a>
                    
                    <!-- Bulk Export Dropdown -->
                    <div class="relative inline-block text-left">
                        <button type="button" id="paymentExportDropdown" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export All
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div id="paymentExportMenu" class="hidden origin-top-right absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 border border-gray-200">
                            <div class="py-2">
                                <!-- Date Range Inputs -->
                                <div class="px-4 py-2 border-b border-gray-200">
                                    <div class="space-y-2">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">From Date</label>
                                            <input type="date" id="paymentExportFromDate" value="{{ request('date_from') }}" 
                                                   class="w-full text-xs border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-green-500 focus:border-green-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">To Date</label>
                                            <input type="date" id="paymentExportToDate" value="{{ request('date_to') }}" 
                                                   class="w-full text-xs border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-green-500 focus:border-green-500">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Export Options -->
                                <div class="py-1">
                                    <button onclick="exportPayment('pdf')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                        Export as PDF
                                    </button>
                                    <button onclick="exportPayment('excel')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                        Export as Excel
                                    </button>
                                    <button onclick="exportPayment('csv')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
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
            <form method="GET" action="{{ route('pos.supplier-payments.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Supplier name, reference..."
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Payment Method</label>
                        <select name="payment_method"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Methods</option>
                            <option value="cash"            @selected(request('payment_method') == 'cash')>Cash</option>
                            <option value="bank"            @selected(request('payment_method') == 'bank')>Bank</option>
                            <option value="khalti"          @selected(request('payment_method') == 'khalti')>Khalti</option>
                            <option value="khalti_external" @selected(request('payment_method') == 'khalti_external')>Khalti External</option>
                            <option value="esewa"           @selected(request('payment_method') == 'esewa')>eSewa</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Search
                    </button>
                    <a href="{{ route('pos.supplier-payments.index') }}"
                       class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Results count --}}
        @if($payments->count() > 0)
            <div class="text-xs text-slate-500 px-6 pt-4 pb-2">
                Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
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
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Supplier</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Amount</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Method</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Remaining Due</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Note</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($payments as $index => $payment)
                        <tr class="hover:bg-slate-50">

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-500">
                                    {{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">
                                    PAY-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                                @if($payment->payment_reference)
                                    <div class="text-xs text-slate-400">{{ $payment->payment_reference }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">
                                    {{ \Carbon\Carbon::parse($payment->date)->format('M d, Y') }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($payment->supplier)
                                    <div class="text-sm font-medium text-slate-900">{{ $payment->supplier->name }}</div>
                                    <div class="text-xs text-slate-400 capitalize">{{ $payment->supplier->supplier_type }}</div>
                                @else
                                    <div class="text-sm text-slate-400">N/A</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-red-600">
                                    − Rs {{ number_format($payment->amount, 2) }}
                                </div>
                                @if($payment->bank_charge > 0)
                                    <div class="text-xs text-slate-400">+Rs {{ number_format($payment->bank_charge, 2) }} charge</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($payment->payment_method == 'cash') bg-green-100 text-green-800
                                    @elseif($payment->payment_method == 'bank') bg-blue-100 text-blue-800
                                    @elseif(in_array($payment->payment_method, ['esewa','Esewa'])) bg-purple-100 text-purple-800
                                    @elseif(in_array($payment->payment_method, ['khalti','Khalti','khalti_external'])) bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($payment->payment_method) }}
                                </span>
                                @if($payment->payment_type == 'integrated')
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-indigo-100 text-indigo-700">
                                        Online
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($payment->supplier)
                                    @if($payment->supplier->total_due > 0)
                                        <span class="text-sm font-semibold text-red-600">
                                            Rs {{ number_format($payment->supplier->total_due, 2) }}
                                        </span>
                                    @else
                                        <span class="text-sm font-semibold text-emerald-600">Cleared</span>
                                    @endif
                                @else
                                    <span class="text-sm text-slate-400">—</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($payment->note)
                                    <div class="text-xs text-slate-500 max-w-xs truncate" title="{{ $payment->note }}">
                                        {{ $payment->note }}
                                    </div>
                                @else
                                    <div class="text-sm text-slate-400">—</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-3">
                                    <a href="{{ route('pos.supplier-payments.edit', $payment) }}"
                                       class="text-blue-600 hover:text-blue-900 font-medium">Edit</a>
                                    <form method="POST"
                                          action="{{ route('pos.supplier-payments.destroy', $payment) }}"
                                          class="inline"
                                          onsubmit="return confirm('Delete this payment record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No payment records found</p>
                                    <p class="text-sm mt-1">Try adjusting your filters or add a new payment.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('pos.supplier-payments.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Add Payment
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
        @if($payments->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $payments->appends(request()->query())->links() }}
            </div>
        @endif

    </div>

</div>

<script>
// Export dropdown functionality
const paymentExportDropdown = document.getElementById('paymentExportDropdown');
const paymentExportMenu = document.getElementById('paymentExportMenu');

paymentExportDropdown.addEventListener('click', function() {
    paymentExportMenu.classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!paymentExportDropdown.contains(e.target) && !paymentExportMenu.contains(e.target)) {
        paymentExportMenu.classList.add('hidden');
    }
});

// Export function with date range
function exportPayment(type) {
    const fromDate = document.getElementById('paymentExportFromDate').value;
    const toDate = document.getElementById('paymentExportToDate').value;
    
    // Get current filter parameters
    const currentParams = new URLSearchParams(window.location.search);
    
    // Build export URL with all parameters
    const exportParams = new URLSearchParams();
    
    // Add current filters
    if (currentParams.get('q')) exportParams.set('q', currentParams.get('q'));
    if (currentParams.get('payment_method')) exportParams.set('payment_method', currentParams.get('payment_method'));
    
    // Add date range from export inputs
    if (fromDate) exportParams.set('from', fromDate);
    if (toDate) exportParams.set('to', toDate);
    
    // Build the export URL
    const exportUrl = `/pos/supplier-payments/export/${type}?${exportParams.toString()}`;
    
    // Navigate to export URL
    window.location.href = exportUrl;
}
</script>
@endsection