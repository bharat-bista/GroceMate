@extends('inventory.layouts.inventory')

@section('title', 'Business Account')
@section('heading', 'Business Account')
@section('subtitle', 'View account-specific sales, purchases, payments, and cash flow')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 via-indigo-800 to-cyan-700 p-5 text-white">
            <div class="grid grid-cols-1 xl:grid-cols-[auto,1fr,auto] gap-5 items-start">
                <div class="w-24 h-24 rounded-2xl bg-white/10 border border-white/15 overflow-hidden flex items-center justify-center shadow-lg shrink-0">
                    @if($business->profile_image)
                        <img src="/assets/img/business/{{ $business->profile_image }}"
                             alt="{{ $business->business_name }}"
                             class="w-full h-full object-cover">
                    @else
                        <svg class="w-10 h-10 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V7l5-4h8l5 4v12a2 2 0 01-2 2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21V9h6v12"></path>
                        </svg>
                    @endif
                </div>

                <div class="min-w-0">
                    <div class="flex flex-col gap-2">
                        <div>
                            <h2 class="text-2xl font-bold leading-tight">{{ $business->business_name }}</h2>
                            <p class="text-sm text-white/75 mt-1">{{ $business->business_type ?: 'Business account overview' }}</p>
                        </div>

                        <div class="flex flex-wrap gap-2 text-xs">
                            <span class="px-3 py-1 rounded-full bg-white/10 border border-white/15">
                                Owner: {{ $business->owner_name ?: 'N/A' }}
                            </span>
                            <span class="px-3 py-1 rounded-full bg-white/10 border border-white/15">
                                Phone: {{ $business->phone ?: 'N/A' }}
                            </span>
                            <span class="px-3 py-1 rounded-full bg-white/10 border border-white/15">
                                PAN: {{ $business->pan_no ?: 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap xl:flex-col gap-3 xl:w-44 self-start">
                    <a href="{{ route('business.index') }}"
                       data-back-button
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-white/10 text-white rounded-xl hover:bg-white/15 text-sm font-medium border border-white/15">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back
                    </a>

                    <a href="{{ route('business.edit', $business) }}"
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-400 text-slate-900 rounded-xl hover:bg-amber-300 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m6.364 1.636l-9.9 9.9a2 2 0 01-.878.515l-3.293.94.94-3.293a2 2 0 01.515-.878l9.9-9.9a2 2 0 112.828 2.828z"></path>
                        </svg>
                        Edit Account
                    </a>

                    <div class="relative inline-block text-left xl:w-full">
                        <button type="button" id="businessExportDropdown"
                                class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-emerald-500 text-white rounded-xl hover:bg-emerald-400 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div id="businessExportMenu"
                             class="hidden origin-top-right absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 border border-gray-200 p-3 space-y-3">
                            <div class="flex gap-2">
                                <button type="button" id="businessExportModeAll"
                                        class="flex-1 px-3 py-1.5 rounded-lg text-sm bg-blue-600 text-white">
                                    All
                                </button>
                                <button type="button" id="businessExportModeRange"
                                        class="flex-1 px-3 py-1.5 rounded-lg text-sm bg-slate-100 text-slate-700">
                                    Date Range
                                </button>
                            </div>

                            <div id="businessExportDateRange" class="hidden space-y-2">
                                <div>
                                    <label for="businessExportFromDate" class="block text-xs font-medium text-slate-600 mb-1">From Date</label>
                                    <input type="date" id="businessExportFromDate"
                                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="businessExportToDate" class="block text-xs font-medium text-slate-600 mb-1">To Date</label>
                                    <input type="date" id="businessExportToDate"
                                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                                <div id="businessExportDateWarning" class="hidden text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                                    Please select both from and to dates.
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2 pt-1">
                                <a href="{{ route('business.export', ['business' => $business, 'type' => 'pdf']) }}"
                                   data-business-export-type="pdf"
                                   class="text-center px-3 py-2 rounded-lg bg-slate-100 text-sm text-slate-700 hover:bg-slate-200">PDF</a>
                                <a href="{{ route('business.export', ['business' => $business, 'type' => 'excel']) }}"
                                   data-business-export-type="excel"
                                   class="text-center px-3 py-2 rounded-lg bg-slate-100 text-sm text-slate-700 hover:bg-slate-200">Excel</a>
                                <a href="{{ route('business.export', ['business' => $business, 'type' => 'csv']) }}"
                                   data-business-export-type="csv"
                                   class="text-center px-3 py-2 rounded-lg bg-slate-100 text-sm text-slate-700 hover:bg-slate-200">CSV</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 border-b border-slate-200 bg-slate-50">
            <div class="rounded-2xl bg-white border border-slate-200 px-4 py-3">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Address</p>
                <p class="font-medium text-slate-900 text-sm">{{ $business->address ?: 'Not specified' }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-200 px-4 py-3">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">VAT Number</p>
                <p class="font-medium text-slate-900 text-sm">{{ $business->vat_no ?: 'Not specified' }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-200 px-4 py-3">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Current Balance</p>
                <p class="font-semibold text-emerald-600 text-lg">Rs {{ number_format((float) $business->balance, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <p class="text-sm text-slate-500 mb-1">Total Sales</p>
            <p class="text-2xl font-bold text-blue-600">Rs {{ number_format($salesTotal, 2) }}</p>
            <p class="text-xs text-slate-500 mt-2">{{ $salesCount }} sale record(s)</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <p class="text-sm text-slate-500 mb-1">Total Purchases</p>
            <p class="text-2xl font-bold text-amber-600">Rs {{ number_format($purchaseTotal, 2) }}</p>
            <p class="text-xs text-slate-500 mt-2">{{ $purchaseCount }} purchase record(s)</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <p class="text-sm text-slate-500 mb-1">Income Received</p>
            <p class="text-2xl font-bold text-emerald-600">Rs {{ number_format($incomeTotal, 2) }}</p>
            <p class="text-xs text-slate-500 mt-2">{{ $incomeCount }} income record(s)</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <p class="text-sm text-slate-500 mb-1">Supplier Payments</p>
            <p class="text-2xl font-bold text-rose-600">Rs {{ number_format($supplierPaymentTotal, 2) }}</p>
            <p class="text-xs text-slate-500 mt-2">{{ $supplierPaymentCount }} payment record(s)</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <p class="text-sm text-slate-500 mb-1">Net Cash Flow</p>
            <p class="text-2xl font-bold {{ $netCashFlow >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                Rs {{ number_format($netCashFlow, 2) }}
            </p>
            <p class="text-xs text-slate-500 mt-2">Income received minus supplier payments</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <p class="text-sm text-slate-500 mb-1">Current Account Balance</p>
            <p class="text-2xl font-bold text-indigo-600">Rs {{ number_format((float) $business->balance, 2) }}</p>
            <p class="text-xs text-slate-500 mt-2">Stored live balance for this business account</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <h3 class="text-lg font-semibold text-slate-900">Business Account Overview</h3>
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-amber-500"></span><span>Purchases</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-blue-500"></span><span>Sales</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-emerald-500"></span><span>Income</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-rose-500"></span><span>Supplier Payments</span></div>
                </div>
                <select id="businessChartMode" class="rounded-lg border-slate-300 text-sm px-3 py-2">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
        </div>
        <div style="height: 400px; position: relative;">
            <canvas id="businessAccountChart"></canvas>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-slate-900">Account Activity</h3>
            <p class="text-sm text-slate-500">Combined business transactions</p>
        </div>

        @if($activityFeed->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-slate-700">Date</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-700">Type</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-700">Reference</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-700">Party</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-700">Direction</th>
                            <th class="text-right px-4 py-3 font-medium text-slate-700">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($activityFeed->take(15) as $activity)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $activity['date']->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                        {{ $activity['direction'] === 'Inflow' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $activity['type_label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $activity['reference'] }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $activity['party'] }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $activity['direction'] }}</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $activity['direction'] === 'Inflow' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    Rs {{ number_format($activity['amount'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-slate-500">No transactions found for this business account.</div>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Recent Purchases</h3>
                <span class="text-sm text-slate-500">{{ $purchaseCount }} total</span>
            </div>
            @if($recentPurchases->count() > 0)
                <div class="space-y-3">
                    @foreach($recentPurchases as $purchase)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="font-medium text-slate-900">{{ $purchase->invoice_no ?: 'PUR-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-xs text-slate-500">{{ $purchase->supplier->name ?? 'N/A' }} • {{ $purchase->purchase_date->format('M d, Y') }}</p>
                            </div>
                            <p class="font-semibold text-amber-600">Rs {{ number_format((float) $purchase->total_cost, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-slate-500">No purchase records for this account.</div>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Recent Sales</h3>
                <span class="text-sm text-slate-500">{{ $salesCount }} total</span>
            </div>
            @if($recentSales->count() > 0)
                <div class="space-y-3">
                    @foreach($recentSales as $sale)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="font-medium text-slate-900">{{ $sale->invoice_no ?: 'INV-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-xs text-slate-500">{{ $sale->customer->name ?? 'Walk-in Customer' }} • {{ $sale->invoice_date->format('M d, Y') }}</p>
                            </div>
                            <p class="font-semibold text-blue-600">Rs {{ number_format((float) $sale->total_cost, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-slate-500">No sale records for this account.</div>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Recent Income</h3>
                <span class="text-sm text-slate-500">{{ $incomeCount }} total</span>
            </div>
            @if($recentIncomes->count() > 0)
                <div class="space-y-3">
                    @foreach($recentIncomes as $income)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="font-medium text-slate-900">{{ $income->reference_no ?: 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-xs text-slate-500">{{ $income->customer->name ?? 'General Income' }} • {{ $income->transaction_date->format('M d, Y') }}</p>
                            </div>
                            <p class="font-semibold text-emerald-600">Rs {{ number_format((float) $income->amount_received, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-slate-500">No income records for this account.</div>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Recent Supplier Payments</h3>
                <span class="text-sm text-slate-500">{{ $supplierPaymentCount }} total</span>
            </div>
            @if($recentSupplierPayments->count() > 0)
                <div class="space-y-3">
                    @foreach($recentSupplierPayments as $payment)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="font-medium text-slate-900">{{ $payment->payment_reference ?: 'PAY-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-xs text-slate-500">{{ $payment->supplier->name ?? 'N/A' }} • {{ $payment->date->format('M d, Y') }}</p>
                            </div>
                            <p class="font-semibold text-rose-600">Rs {{ number_format((float) $payment->amount, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-slate-500">No supplier payment records for this account.</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.businessChartData = @json($chartData);

document.addEventListener('DOMContentLoaded', function () {
    const chartCanvas = document.getElementById('businessAccountChart');
    const chartMode = document.getElementById('businessChartMode');
    const exportDropdown = document.getElementById('businessExportDropdown');
    const exportMenu = document.getElementById('businessExportMenu');
    const exportModeAll = document.getElementById('businessExportModeAll');
    const exportModeRange = document.getElementById('businessExportModeRange');
    const exportDateRange = document.getElementById('businessExportDateRange');
    const exportFromDate = document.getElementById('businessExportFromDate');
    const exportToDate = document.getElementById('businessExportToDate');
    const exportDateWarning = document.getElementById('businessExportDateWarning');
    const exportLinks = document.querySelectorAll('[data-business-export-type]');
    let exportMode = 'all';

    if (chartCanvas && chartMode) {
        const ctx = chartCanvas.getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.businessChartData.monthly.labels,
                datasets: [
                    {
                        label: 'Purchases',
                        data: window.businessChartData.monthly.purchases,
                        borderColor: 'rgb(245, 158, 11)',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.35,
                        fill: false
                    },
                    {
                        label: 'Sales',
                        data: window.businessChartData.monthly.sales,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.35,
                        fill: false
                    },
                    {
                        label: 'Income',
                        data: window.businessChartData.monthly.income,
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.35,
                        fill: false
                    },
                    {
                        label: 'Supplier Payments',
                        data: window.businessChartData.monthly.payments,
                        borderColor: 'rgb(244, 63, 94)',
                        backgroundColor: 'rgba(244, 63, 94, 0.1)',
                        tension: 0.35,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': Rs ' + context.parsed.y.toLocaleString('en-IN', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'Rs ' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });

        chartMode.addEventListener('change', function () {
            const selected = window.businessChartData[this.value];
            if (!selected) {
                return;
            }

            chart.data.labels = selected.labels;
            chart.data.datasets[0].data = selected.purchases;
            chart.data.datasets[1].data = selected.sales;
            chart.data.datasets[2].data = selected.income;
            chart.data.datasets[3].data = selected.payments;
            chart.update();
        });
    }

    function updateExportModeUi() {
        if (exportMode === 'all') {
            exportModeAll.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-blue-600 text-white';
            exportModeRange.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-slate-100 text-slate-700';
            exportDateRange.classList.add('hidden');
            exportDateWarning.classList.add('hidden');
        } else {
            exportModeAll.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-slate-100 text-slate-700';
            exportModeRange.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-blue-600 text-white';
            exportDateRange.classList.remove('hidden');
        }
    }

    function getBusinessExportUrl(type) {
        const baseUrl = "{{ route('business.export', ['business' => $business, 'type' => 'TYPE']) }}".replace('TYPE', type);

        if (exportMode === 'all') {
            return baseUrl;
        }

        if (!exportFromDate.value || !exportToDate.value) {
            exportDateWarning.classList.remove('hidden');
            return null;
        }

        exportDateWarning.classList.add('hidden');
        const url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('from', exportFromDate.value);
        url.searchParams.set('to', exportToDate.value);

        return url.toString();
    }

    if (exportDropdown && exportMenu) {
        exportDropdown.addEventListener('click', function () {
            exportMenu.classList.toggle('hidden');
        });
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

    exportLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            const exportUrl = getBusinessExportUrl(this.dataset.businessExportType);
            if (!exportUrl) {
                event.preventDefault();
                return;
            }

            this.setAttribute('href', exportUrl);
        });
    });

    document.addEventListener('click', function (event) {
        if (exportDropdown && exportMenu && !exportDropdown.contains(event.target) && !exportMenu.contains(event.target)) {
            exportMenu.classList.add('hidden');
        }
    });

    updateExportModeUi();
});
</script>
@endpush
