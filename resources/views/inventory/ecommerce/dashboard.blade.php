@extends('inventory.layouts.inventory')

@section('title','Ecommerce Dashboard')
@section('heading','Ecommerce Dashboard')
@section('subtitle','Overview of your ecommerce performance')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <!-- Today's Orders -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Today's Orders</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $quickStats['today_orders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- This Month Orders -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">This Month Orders</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $quickStats['this_month_orders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Today's Net Revenue</p>
                    <p class="text-2xl font-bold text-slate-900">Rs {{ number_format($quickStats['today_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- This Month Revenue -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Month Net Revenue</p>
                    <p class="text-2xl font-bold text-slate-900">Rs {{ number_format($quickStats['this_month_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Net Revenue</p>
                    <p class="text-2xl font-bold text-slate-900">Rs {{ number_format($quickStats['total_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Pending Orders</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $quickStats['pending_orders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Processing Orders -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Processing</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $quickStats['processing_orders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Delivered Orders -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Delivered</p>
                    <p class="text-2xl font-bold text-green-600">{{ $quickStats['delivered_orders'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Total Products</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $quickStats['total_products'] }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m0 0v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Low Stock</p>
                    <p class="text-2xl font-bold text-red-600">{{ $quickStats['low_stock_products'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Refund Summary -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Refunds Pending</p>
                    <p class="text-2xl font-bold text-amber-600">Rs {{ number_format($refundSummary['pending_amount'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Refunds Completed</p>
                    <p class="text-2xl font-bold text-emerald-600">Rs {{ number_format($refundSummary['completed_amount'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Total Refunds</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $refundSummary['total_refunds'] }}</p>
                </div>
                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders and Revenue Chart -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6" x-data="{
      chartMode: 'monthly'
    }">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-slate-900">Orders & Net Revenue Overview</h3>
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2 text-sm text-slate-500">
            <div class="flex items-center gap-1">
              <div class="w-3 h-3 bg-blue-500 rounded"></div>
              <span>Orders</span>
            </div>
            <div class="flex items-center gap-1">
              <div class="w-3 h-3 bg-green-500 rounded"></div>
              <span>Net Revenue</span>
            </div>
          </div>
          <select x-model="chartMode" @change="window.updateChartMode($event.target.value)"
                  class="rounded-lg border-slate-300 text-sm px-3 py-2">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
          </select>
        </div>
      </div>
      <div style="height: 400px; position: relative;">
        <canvas id="dashboardChart"></canvas>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Recent Active Orders</h3>
                <a href="{{ route('inventory.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View All</a>
            </div>
            
            @if($recentOrders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-left py-3 px-4 text-slate-600 font-medium">Order Number</th>
                                <th class="text-left py-3 px-4 text-slate-600 font-medium">Customer</th>
                                <th class="text-left py-3 px-4 text-slate-600 font-medium">Amount</th>
                                <th class="text-left py-3 px-4 text-slate-600 font-medium">Delivery Status</th>
                                <th class="text-left py-3 px-4 text-slate-600 font-medium">Payment Status</th>
                                <th class="text-left py-3 px-4 text-slate-600 font-medium">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-3 px-4 font-medium text-slate-900">
                                        <a href="{{ route('inventory.orders.show', $order['id']) }}" class="text-blue-600 hover:text-blue-700">
                                            {{ $order['order_number'] }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-slate-700">{{ $order['customer_name'] }}</td>
                                    <td class="py-3 px-4 text-slate-900 font-medium">Rs {{ number_format($order['total_amount'], 2) }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($order['delivery_status'] == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order['delivery_status'] == 'processing') bg-blue-100 text-blue-800
                                            @elseif($order['delivery_status'] == 'shipped') bg-indigo-100 text-indigo-800
                                            @elseif($order['delivery_status'] == 'delivered') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($order['delivery_status']) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($order['payment_status'] != 'unpaid') bg-green-100 text-green-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $order['payment_status'])) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">{{ $order['created_at']->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-slate-500">No active orders found.</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <!-- Order Status Distribution -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-6">Order Status</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-slate-700">Pending</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $orderStatusBreakdown['pending'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ ($orderStatusBreakdown['pending'] / (array_sum($orderStatusBreakdown) ?: 1)) * 100 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-slate-700">Processing</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $orderStatusBreakdown['processing'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($orderStatusBreakdown['processing'] / (array_sum($orderStatusBreakdown) ?: 1)) * 100 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-slate-700">Shipped</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $orderStatusBreakdown['shipped'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ ($orderStatusBreakdown['shipped'] / (array_sum($orderStatusBreakdown) ?: 1)) * 100 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-slate-700">Delivered</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $orderStatusBreakdown['delivered'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($orderStatusBreakdown['delivered'] / (array_sum($orderStatusBreakdown) ?: 1)) * 100 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-slate-700">Cancelled</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $orderStatusBreakdown['cancelled'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ ($orderStatusBreakdown['cancelled'] / (array_sum($orderStatusBreakdown) ?: 1)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancelled Orders -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-slate-900">Cancelled Orders</h3>
                    <a href="{{ route('inventory.orders.index', ['status' => 'cancelled']) }}" class="text-sm text-blue-600 hover:text-blue-700">View All</a>
                </div>
                
                @if($recentCancelledOrders->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentCancelledOrders as $order)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                <div>
                                    <a href="{{ route('inventory.orders.show', $order['id']) }}" class="font-medium text-slate-900 hover:text-blue-600">
                                        {{ $order['order_number'] }}
                                    </a>
                                    <p class="text-xs text-slate-500">{{ $order['customer_name'] }} • {{ $order['created_at']->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-slate-900">Rs {{ number_format($order['total_amount'], 2) }}</p>
                                    <p class="text-xs text-red-600 font-medium">Cancelled</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-slate-500">No cancelled orders found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Refund Management -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-slate-900">Refund Management</h3>
            <span class="text-sm text-slate-500">{{ $refundSummary['total_refunds'] }} refund(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Order #</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Products</th>
                        <th class="px-4 py-3 text-left font-semibold">Paid Amount</th>
                        <th class="px-4 py-3 text-left font-semibold">Refund Amount</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Cancelled At</th>
                        <th class="px-4 py-3 text-left font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($refunds as $refund)
                        @php
                            $order = $refund->order;
                            $productNames = $order?->items?->pluck('product_name')->filter()->implode(', ') ?? '—';
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $order?->order_number ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $refund->customer_name }}</div>
                                <div class="text-xs text-slate-500">{{ $refund->customer_phone ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $productNames }}</td>
                            <td class="px-4 py-3 text-slate-700">Rs {{ number_format((float) ($order?->total_amount ?? 0), 2) }}</td>
                            <td class="px-4 py-3 text-slate-700">Rs {{ number_format((float) $refund->refund_amount, 2) }}</td>
                            <td class="px-4 py-3">
                                @if($refund->refund_status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Completed</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ optional($refund->cancelled_at)->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($refund->refund_status === 'pending' && auth()->user()?->isAdmin())
                                    <form method="POST" action="{{ route('inventory.ecommerce-refunds.update', $refund) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-600 text-white hover:bg-emerald-700">
                                            Mark as Refunded
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-slate-500">No refunds recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-900 mb-6">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <a href="{{ route('inventory.orders.index') }}" 
               class="flex flex-col items-center justify-center p-4 bg-blue-50 border-2 border-blue-200 rounded-2xl hover:bg-blue-100 transition duration-200 group">
                <svg class="w-8 h-8 text-blue-600 mb-2 group-hover:scale-110 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span class="text-sm font-medium text-blue-900 text-center">View Orders</span>
            </a>

            <a href="{{ route('inventory.ecommerce-products.index') }}" 
               class="flex flex-col items-center justify-center p-4 bg-green-50 border-2 border-green-200 rounded-2xl hover:bg-green-100 transition duration-200 group">
                <svg class="w-8 h-8 text-green-600 mb-2 group-hover:scale-110 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m0 0v10l8 4"></path>
                </svg>
                <span class="text-sm font-medium text-green-900 text-center">Products</span>
            </a>

            <a href="{{ route('inventory.ecommerce-categories.index') }}" 
               class="flex flex-col items-center justify-center p-4 bg-yellow-50 border-2 border-yellow-200 rounded-2xl hover:bg-yellow-100 transition duration-200 group">
                <svg class="w-8 h-8 text-yellow-600 mb-2 group-hover:scale-110 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span class="text-sm font-medium text-yellow-900 text-center">Categories</span>
            </a>

            <a href="{{ route('inventory.ecommerce-brands.index') }}" 
               class="flex flex-col items-center justify-center p-4 bg-purple-50 border-2 border-purple-200 rounded-2xl hover:bg-purple-100 transition duration-200 group">
                <svg class="w-8 h-8 text-purple-600 mb-2 group-hover:scale-110 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.5a2 2 0 00-1 3.75A2 2 0 0017 9"></path>
                </svg>
                <span class="text-sm font-medium text-purple-900 text-center">Brands</span>
            </a>

            <a href="{{ route('inventory.ecommerce-income.index') }}" 
               class="flex flex-col items-center justify-center p-4 bg-indigo-50 border-2 border-indigo-200 rounded-2xl hover:bg-indigo-100 transition duration-200 group">
                <svg class="w-8 h-8 text-indigo-600 mb-2 group-hover:scale-110 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-indigo-900 text-center">Income</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
// Make chart data available globally
window.dashboardChartData = @json($chartData);

// Initialize chart when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('dashboardChart');
    if (ctx) {
        let dashboardChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.dashboardChartData.monthly.labels,
                datasets: [
                    {
                        label: 'Orders',
                        data: window.dashboardChartData.monthly.orders,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue',
                        data: window.dashboardChartData.monthly.revenue,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.dataset.yAxisID === 'y1') {
                                    label += 'Rs ' + context.parsed.y.toLocaleString('en-IN', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                } else {
                                    label += context.parsed.y;
                                }
                                return label;
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
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Orders'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue (Rs)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rs ' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });

        // Function to update chart data based on mode
        window.updateChartMode = function(mode) {
            const data = window.dashboardChartData[mode];
            if (data) {
                dashboardChart.data.labels = data.labels;
                dashboardChart.data.datasets[0].data = data.orders;
                dashboardChart.data.datasets[1].data = data.revenue;
                dashboardChart.update();
            }
        };
    }
});
</script>
@endpush
