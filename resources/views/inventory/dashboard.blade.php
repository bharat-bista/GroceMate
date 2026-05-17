@extends('inventory.layouts.inventory')

@section('title','Inventory Dashboard')
@section('heading','Dashboard')
@section('subtitle','Overview of products, stock levels and purchase activity')

@section('content')
@php
    $todayPurchaseTotal = (float) ($dailyPurchases->last()->total ?? 0);
    $monthPurchaseTotal = (float) ($monthlyPurchases->last()->total ?? 0);
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Total Products</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalProducts) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Active Products</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($activeProducts) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Low Stock Items</p>
                    <p class="text-2xl font-bold text-amber-600">{{ number_format($lowStockCount) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Total Suppliers</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($supplierCount) }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Expiring Soon</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($expiringSoonCount) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Expired Items</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($expiredCount) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Today's Purchases</p>
                    <p class="text-2xl font-bold text-slate-900">Rs {{ number_format($todayPurchaseTotal, 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h.01M11 15h2m8 1V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Month Purchases</p>
                    <p class="text-2xl font-bold text-slate-900">Rs {{ number_format($monthPurchaseTotal, 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <h3 class="text-lg font-semibold text-slate-900">Purchase Overview</h3>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-blue-500 rounded"></div>
                        <span>Purchase Amount</span>
                    </div>
                </div>
                <select id="inventoryChartMode" class="rounded-lg border-slate-300 text-sm px-3 py-2">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
        </div>
        <div style="height: 400px; position: relative;">
            <canvas id="inventoryDashboardChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Low Stock Products</h3>
                <a href="{{ route('inventory.products.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View All</a>
            </div>

            @if($topLowStock->count() > 0)
                <div class="space-y-3">
                    @foreach($topLowStock as $stock)
                        @php
                            $isCritical = (float) $stock->quantity <= 0;
                            $hasReorderLevel = (float) $stock->reorder_level > 0;
                            $badgeClasses = $isCritical
                                ? 'bg-red-100 text-red-600'
                                : 'bg-amber-100 text-amber-700';
                            $iconClasses = $isCritical
                                ? 'bg-red-100 text-red-600'
                                : 'bg-amber-100 text-amber-600';
                        @endphp
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $iconClasses }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">{{ $stock->product->name ?? 'Unnamed Product' }}</p>
                                    <p class="text-xs text-slate-500">
                                        @if($hasReorderLevel)
                                            Reorder level: {{ number_format($stock->reorder_level) }}
                                        @else
                                            No reorder level set
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">
                                    Qty {{ number_format($stock->quantity) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-slate-500">No low stock products found.</p>
                </div>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-6">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('inventory.products.create') }}"
                   class="flex flex-col items-center justify-center p-6 bg-blue-50 border-2 border-blue-200 rounded-2xl hover:bg-blue-100 transition duration-200 group">
                    <svg class="w-8 h-8 text-blue-600 mb-2 group-hover:scale-110 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-900">Add Product</span>
                </a>

                <a href="{{ route('inventory.suppliers.create') }}"
                   class="flex flex-col items-center justify-center p-6 bg-green-50 border-2 border-green-200 rounded-2xl hover:bg-green-100 transition duration-200 group">
                    <svg class="w-8 h-8 text-green-600 mb-2 group-hover:scale-110 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-8 8v-1a4 4 0 014-4h4a4 4 0 014 4v1M9 7a4 4 0 118 0 4 4 0 01-8 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-green-900">Add Supplier</span>
                </a>

                <a href="{{ route('inventory.purchases.create') }}"
                   class="flex flex-col items-center justify-center p-6 bg-yellow-50 border-2 border-yellow-200 rounded-2xl hover:bg-yellow-100 transition duration-200 group">
                    <svg class="w-8 h-8 text-yellow-600 mb-2 group-hover:scale-110 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h.01M11 15h2m8 1V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-yellow-900">Add Purchase</span>
                </a>

                <a href="{{ route('inventory.alerts.expiry') }}"
                   class="flex flex-col items-center justify-center p-6 bg-red-50 border-2 border-red-200 rounded-2xl hover:bg-red-100 transition duration-200 group">
                    <svg class="w-8 h-8 text-red-600 mb-2 group-hover:scale-110 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-red-900">Expiry Alerts</span>
                </a>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-semibold text-slate-900">Inventory Alerts</h4>
                    <a href="{{ route('inventory.alerts.expiry') }}" class="text-sm text-blue-600 hover:text-blue-700">Review</a>
                </div>

                <div class="space-y-3">
                    @if($expiredCount > 0)
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-red-50 border border-red-100">
                            <div class="w-9 h-9 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">{{ number_format($expiredCount) }} expired item(s)</p>
                                <p class="text-sm text-slate-600">These items should be reviewed and removed from saleable stock.</p>
                            </div>
                        </div>
                    @endif

                    @if($expiringSoonCount > 0)
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-orange-50 border border-orange-100">
                            <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">{{ number_format($expiringSoonCount) }} item(s) expiring soon</p>
                                <p class="text-sm text-slate-600">Check upcoming expiry dates and prioritize those products.</p>
                            </div>
                        </div>
                    @endif

                    @if($lowStockCount > 0)
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-amber-50 border border-amber-100">
                            <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">{{ number_format($lowStockCount) }} item(s) need stock attention</p>
                                <p class="text-sm text-slate-600">This includes items below reorder level and products already at zero or negative stock.</p>
                            </div>
                        </div>
                    @endif

                    @if($expiredCount === 0 && $expiringSoonCount === 0 && $lowStockCount === 0)
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                            <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-slate-900">Inventory looks healthy</p>
                                <p class="text-sm text-slate-600">No low stock, expiry, or expired item alerts right now.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.inventoryChartData = {
    daily: {
        labels: @json($dailyPurchases->map(fn($purchase) => \Carbon\Carbon::parse($purchase->label)->format('M d'))->values()->all()),
        totals: @json($dailyPurchases->pluck('total')->map(fn($value) => (float) $value)->values()->all()),
    },
    weekly: {
        labels: @json($weeklyPurchases->pluck('label')->values()->all()),
        totals: @json($weeklyPurchases->pluck('total')->map(fn($value) => (float) $value)->values()->all()),
    },
    monthly: {
        labels: @json($monthlyPurchases->map(fn($purchase) => \Carbon\Carbon::createFromFormat('Y-m', $purchase->label)->format('M Y'))->values()->all()),
        totals: @json($monthlyPurchases->pluck('total')->map(fn($value) => (float) $value)->values()->all()),
    },
    yearly: {
        labels: @json($yearlyPurchases->pluck('label')->values()->all()),
        totals: @json($yearlyPurchases->pluck('total')->map(fn($value) => (float) $value)->values()->all()),
    }
};

document.addEventListener('DOMContentLoaded', function () {
    const chartCanvas = document.getElementById('inventoryDashboardChart');
    const modeSelect = document.getElementById('inventoryChartMode');

    if (!chartCanvas || !modeSelect) {
        return;
    }

    const chartContext = chartCanvas.getContext('2d');
    const chart = new Chart(chartContext, {
        type: 'line',
        data: {
            labels: window.inventoryChartData.monthly.labels,
            datasets: [{
                label: 'Purchase Amount',
                data: window.inventoryChartData.monthly.totals,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.12)',
                tension: 0.35,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return 'Purchase Amount: Rs ' + context.parsed.y.toLocaleString('en-IN', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
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

    modeSelect.addEventListener('change', function () {
        const selectedMode = window.inventoryChartData[this.value];
        if (!selectedMode) {
            return;
        }

        chart.data.labels = selectedMode.labels;
        chart.data.datasets[0].data = selectedMode.totals;
        chart.update();
    });
});
</script>
@endpush
