@extends('inventory.layouts.inventory')

@section('title', 'Orders - Ecommerce')
@section('heading', 'Orders Management')
@section('subtitle', 'View and manage customer orders')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Ecommerce Orders</h2>
                    <p class="text-sm text-slate-600 mt-1">Manage customer orders and payments</p>
                </div>
                <div class="flex gap-2">
                    <div class="relative inline-block text-left">
                        <button type="button" id="exportDropdown"
                                class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                            <i class="fas fa-download mr-2"></i>
                            Export All
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div id="exportMenu"
                             class="hidden origin-top-right absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 border border-gray-200 p-3 space-y-3">
                            <div class="flex gap-2">
                                <button type="button" id="exportModeAll"
                                        class="flex-1 px-3 py-1.5 rounded-lg text-sm bg-emerald-600 text-white">
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
                                    <input type="date" id="exportFromDate"
                                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="exportToDate" class="block text-xs font-medium text-slate-600 mb-1">To Date</label>
                                    <input type="date" id="exportToDate"
                                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                </div>
                                <div id="exportDateWarning" class="hidden text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                                    Please select both from and to dates.
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2 pt-1">
                                <a href="{{ route('inventory.orders.export', ['type' => 'pdf']) }}"
                                   data-export-type="pdf"
                                   class="text-center px-3 py-2 rounded-lg bg-slate-100 text-sm text-slate-700 hover:bg-slate-200">PDF</a>
                                <a href="{{ route('inventory.orders.export', ['type' => 'excel']) }}"
                                   data-export-type="excel"
                                   class="text-center px-3 py-2 rounded-lg bg-slate-100 text-sm text-slate-700 hover:bg-slate-200">Excel</a>
                                <a href="{{ route('inventory.orders.export', ['type' => 'csv']) }}"
                                   data-export-type="csv"
                                   class="text-center px-3 py-2 rounded-lg bg-slate-100 text-sm text-slate-700 hover:bg-slate-200">CSV</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <form class="space-y-4" method="GET" action="{{ route('inventory.orders.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
                        <input name="search" value="{{ request('search') }}" placeholder="Order #, Name, Phone..."
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Delivery Status</label>
                        <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Payment Status</label>
                        <select name="payment_status" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Payments</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-3">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                            Filter
                        </button>
                        <a href="{{ route('inventory.orders.index') }}" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        @if($orders->count() > 0)
            <div class="text-xs text-slate-500 px-6 pt-4 pb-2">
                Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} results
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Order #</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Customer</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Items</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Total</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Method</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Delivery</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Payment</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($orders as $order)
                        @php
                            $paymentState = $order->payment_status === 'verified' ? 'paid' : 'unpaid';
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-900">{{ $order->order_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $order->customer_name }}</div>
                                <div class="text-xs text-slate-500">{{ $order->customer_phone }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $order->items->count() }} item(s)
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-900">Rs. {{ number_format($order->total_amount, 0) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($order->payment_method === 'esewa')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">eSewa</span>
                                @elseif($order->payment_method === 'connectips')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Connect IPS</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">COD</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($order->delivery_status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($order->delivery_status === 'processing')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Processing</span>
                                @elseif($order->delivery_status === 'shipped')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Shipped</span>
                                @elseif($order->delivery_status === 'delivered')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Delivered</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($order->payment_status === 'verified')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Unpaid</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-slate-500">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('inventory.orders.show', $order) }}" class="text-emerald-600 hover:text-emerald-900 font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-12 text-center text-slate-500" colspan="9">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $orders->links() }}
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
        exportModeAll.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-emerald-600 text-white';
        exportModeRange.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-slate-100 text-slate-700';
        exportDateRange.classList.add('hidden');
        exportDateWarning?.classList.add('hidden');
    } else {
        exportModeAll.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-slate-100 text-slate-700';
        exportModeRange.className = 'flex-1 px-3 py-1.5 rounded-lg text-sm bg-emerald-600 text-white';
        exportDateRange.classList.remove('hidden');
    }
}

function getOrderExportUrl(type) {
    const baseUrl = "{{ route('inventory.orders.export', ['type' => 'TYPE']) }}".replace('TYPE', type);
    const url = new URL(baseUrl, window.location.origin);
    const search = document.querySelector('input[name="search"]')?.value || '';
    const status = document.querySelector('select[name="status"]')?.value || '';
    const paymentStatus = document.querySelector('select[name="payment_status"]')?.value || '';

    if (search) {
        url.searchParams.set('search', search);
    }
    if (status) {
        url.searchParams.set('status', status);
    }
    if (paymentStatus) {
        url.searchParams.set('payment_status', paymentStatus);
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
        const exportUrl = getOrderExportUrl(this.dataset.exportType);
        if (!exportUrl) {
            e.preventDefault();
            return;
        }

        this.setAttribute('href', exportUrl);
    });
});

updateExportModeUi();

document.addEventListener('click', function (e) {
    if (exportDropdown && exportMenu && !exportDropdown.contains(e.target) && !exportMenu.contains(e.target)) {
        exportMenu.classList.add('hidden');
    }
});
</script>
@endsection
