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
                    <a href="{{ route('inventory.orders.export', ['type' => 'csv']) }}?status={{ request('status') }}&payment_status={{ request('payment_status') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                        <i class="fas fa-download mr-2"></i> Export CSV
                    </a>
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
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ request('payment_status') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cod" {{ request('payment_status') == 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
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
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Payment</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Delivery</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($orders as $order)
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
                                @if($order->payment_status == 'verified')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
                                @elseif($order->payment_status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($order->payment_status == 'failed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">COD</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($order->delivery_status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($order->delivery_status == 'processing')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Processing</span>
                                @elseif($order->delivery_status == 'shipped')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Shipped</span>
                                @elseif($order->delivery_status == 'delivered')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Delivered</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>
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
                            <td class="px-6 py-12 text-center text-slate-500" colspan="8">No orders found.</td>
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
@endsection