@extends('inventory.layouts.inventory')

@section('title','Income Dashboard')
@section('heading','Income Dashboard')
@section('subtitle','Business income analytics and transaction overview')

@section('content')
<div class="space-y-6">
    <!-- Search and Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form class="flex gap-2" method="GET" action="{{ route('pos.income.index') }}">
            <input name="q" value="{{ $q ?? '' }}" placeholder="Search reference/description..."
                   class="w-full md:w-80 rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-200" />
            <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
                Search
            </button>
        </form>

        <a href="{{ route('pos.income.create') }}"
           class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
            + Add Income
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Income</p>
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($totalIncome, 2) }}</p>
                </div>
                <div class="p-3">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">This Month</p>
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($thisMonthIncome, 2) }}</p>
                </div>
                <div class="p-3">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Today</p>
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($todayIncome, 2) }}</p>
                </div>
                <div class="p-3">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Business Income Chart -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Business Income & Balance</h3>
            <div class="space-y-4">
                @forelse($businessIncomeStats as $business)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold">
                                {{ substr($business->business_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $business->business_name }}</p>
                                <p class="text-sm text-slate-500">{{ $business->business_type }} • {{ $business->incomes_count }} transactions this month</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-slate-600">Balance: <span class="font-bold text-blue-600">Rs {{ number_format($business->current_balance ?? 0, 2) }}</span></p>
                            <p class="text-xs text-slate-500 mt-1">Total Income - Supplier Payments</p>
                            <div class="w-24 bg-slate-200 rounded-full h-2 mt-1">
                                @if($thisMonthIncome > 0)
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ (($business->incomes_sum_amount_received ?? 0) / $thisMonthIncome) * 100 }}%"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <p>No business income data available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Payment Methods</h3>
            <div class="space-y-3">
                @forelse($paymentStats as $payment)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full
                                @if($payment->payment_method == 'cash') bg-green-500
                                @elseif($payment->payment_method == 'bank') bg-blue-500
                                @elseif($payment->payment_method == 'Esewa') bg-purple-500
                                @elseif($payment->payment_method == 'Khalti') bg-orange-500
                                @else bg-slate-500
                                @endif"></div>
                            <span class="text-sm font-medium text-slate-700">{{ ucfirst($payment->payment_method) }}</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-800">Rs {{ number_format($payment->total, 2) }}</p>
                            <p class="text-xs text-slate-500">{{ $payment->count }} tx</p>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-4">No payment data</p>
                @endforelse
            </div>
        </div>
    </div>

    
    <!-- Full Transactions Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">All Transactions</h3>
        </div>
        
        @if($incomes->count() > 0)
            <div class="text-xs text-slate-500 px-6 pt-4 pb-2">
                Showing {{ $incomes->firstItem() }} to {{ $incomes->lastItem() }} of {{ $incomes->total() }} results
            </div>
        @endif
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-slate-500 bg-slate-50">
                    <tr>
                        <th class="text-left px-5 py-3">#</th>
                        <th class="text-left px-5 py-3">Reference</th>
                        <th class="text-left px-5 py-3">Date</th>
                        <th class="text-left px-5 py-3">Customer</th>
                        <th class="text-left px-5 py-3">Business</th>
                        <th class="text-left px-5 py-3">Amount</th>
                        <th class="text-left px-5 py-3">Payment Method</th>
                        <th class="text-left px-5 py-3">Income Type</th>
                        <th class="text-right px-5 py-3">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($incomes as $index => $income)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4 font-medium text-slate-500">{{ ($incomes->currentPage() - 1) * $incomes->perPage() + $index + 1 }}</td>
                            <td class="px-5 py-4">
                                <div class="font-semibold">{{ $income->reference_no ?? 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-xs text-slate-500">ID: {{ $income->id }}</div>
                            </td>
                            <td class="px-5 py-4">{{ date('M d, Y', strtotime($income->transaction_date ?? $income->created_at)) }}</td>
                            <td class="px-5 py-4">
                                @if(isset($income->customer))
                                    <div class="font-medium">{{ $income->customer->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $income->customer->phone ?? '' }}</div>
                                    @if($income->customer->total_due > 0)
                                        <div class="text-xs text-amber-600 font-semibold">Due: Rs {{ number_format($income->customer->total_due, 2) }}</div>
                                    @else
                                        <div class="text-xs text-emerald-600 font-semibold">No Due</div>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if(isset($income->business))
                                    <div class="font-medium">{{ $income->business->business_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $income->business->business_type ?? '' }}</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-emerald-600 font-semibold">
                                    Rs {{ number_format($income->amount_received, 2) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($income->payment_method == 'cash') bg-green-100 text-green-700
                                    @elseif($income->payment_method == 'bank') bg-blue-100 text-blue-700
                                    @elseif($income->payment_method == 'Esewa') bg-purple-100 text-purple-700
                                    @elseif($income->payment_method == 'Khalti') bg-orange-100 text-orange-700
                                    @else bg-slate-100 text-slate-700
                                    @endif">
                                    {{ ucfirst($income->payment_method) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($income->income_type == 'Sale') bg-emerald-100 text-emerald-700
                                    @elseif($income->income_type == 'Due Collection') bg-amber-100 text-amber-700
                                    @else bg-slate-100 text-slate-700
                                    @endif">
                                    {{ $income->income_type }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <a class="underline text-slate-800"
                                       href="{{ route('pos.income.edit', $income) }}">Edit</a>

                                    <form method="POST" action="{{ route('pos.income.destroy', $income) }}"
                                          onsubmit="return confirm('Delete this income record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-700 underline">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-5 py-6 text-slate-500" colspan="8">
                                No income records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($incomes->hasPages())
        <div class="mt-4">
            {{ $incomes->links('pagination::tailwind') }}
        </div>
    @endif
</div>

@if(session('success'))
<div class="mt-4 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
    {{ session('success') }}
</div>
@endif
@endsection
