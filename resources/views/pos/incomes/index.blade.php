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

    {{-- Search and Actions --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form class="flex gap-2" method="GET" action="{{ route('pos.income.index') }}">
            <input name="q" value="{{ request('q') }}"
                   placeholder="Search reference/description..."
                   class="w-full md:w-80 rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-200" />
            <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
                Search
            </button>
        </form>
        <a href="{{ route('pos.income.create') }}"
           class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-center">
            + Add Income
        </a>
    </div>

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

    {{-- Transactions Table --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800">All Transactions</h3>
            {{-- Legend --}}
            <div class="flex items-center gap-4 text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                    Income
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>
                    Supplier Payment
                </span>
            </div>
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
                        <th class="text-left px-5 py-3">Customer / Note</th>
                        <th class="text-left px-5 py-3">Business</th>
                        <th class="text-left px-5 py-3">Amount</th>
                        <th class="text-left px-5 py-3">Payment Method</th>
                        <th class="text-left px-5 py-3">Type</th>
                        <th class="text-right px-5 py-3">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($incomes as $index => $income)

                        {{-- Red tint for supplier payment expenses, normal for income --}}
                        <tr class="{{ $income->amount_received < 0 ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-slate-50' }} transition-colors">

                            <td class="px-5 py-4 font-medium text-slate-500">
                                {{ ($incomes->currentPage() - 1) * $incomes->perPage() + $index + 1 }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-800">
                                    {{ $income->reference_no ?? 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="text-xs text-slate-400">ID: {{ $income->id }}</div>
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ \Carbon\Carbon::parse($income->transaction_date ?? $income->created_at)->format('M d, Y') }}
                            </td>

                            <td class="px-5 py-4">
                                @if($income->customer)
                                    <div class="font-medium text-slate-800">{{ $income->customer->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $income->customer->phone ?? '' }}</div>
                                    @if($income->customer->total_due > 0)
                                        <div class="text-xs text-amber-600 font-semibold">
                                            Due: Rs {{ number_format($income->customer->total_due, 2) }}
                                        </div>
                                    @else
                                        <div class="text-xs text-emerald-600">No Due</div>
                                    @endif
                                @elseif($income->notes)
                                    {{-- Show notes for supplier payments --}}
                                    <div class="text-xs text-slate-500 max-w-xs truncate">
                                        {{ $income->notes }}
                                    </div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                @if($income->business)
                                    <div class="font-medium text-slate-800">{{ $income->business->business_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $income->business->business_type ?? '' }}</div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            {{-- Amount: green + for income, red − for expense --}}
                            <td class="px-5 py-4">
                                @if($income->amount_received < 0)
                                    <span class="text-red-600 font-bold">
                                        − Rs {{ number_format(abs($income->amount_received), 2) }}
                                    </span>
                                @else
                                    <span class="text-emerald-600 font-bold">
                                        + Rs {{ number_format($income->amount_received, 2) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Payment Method --}}
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    @if($income->payment_method == 'cash') bg-green-100 text-green-700
                                    @elseif($income->payment_method == 'bank') bg-blue-100 text-blue-700
                                    @elseif(in_array($income->payment_method, ['Esewa','esewa'])) bg-purple-100 text-purple-700
                                    @elseif(in_array($income->payment_method, ['Khalti','khalti','khalti_external'])) bg-orange-100 text-orange-700
                                    @else bg-slate-100 text-slate-700
                                    @endif">
                                    {{ ucfirst($income->payment_method) }}
                                </span>
                            </td>

                            {{-- Type --}}
                            <td class="px-5 py-4">
                                @if($income->amount_received < 0)
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-medium">
                                        Supplier Payment
                                    </span>
                                @elseif($income->income_type == 'Sale')
                                    <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700 font-medium">
                                        Sale
                                    </span>
                                @elseif($income->income_type == 'Due Collection')
                                    <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700 font-medium">
                                        Due Collection
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-600 font-medium">
                                        {{ $income->income_type ?? 'Other' }}
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-3">
                                    @if($income->amount_received >= 0)
                                        <a class="underline text-slate-700 hover:text-slate-900"
                                           href="{{ route('pos.income.edit', $income) }}">
                                            Edit
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Auto</span>
                                    @endif

                                    <form method="POST"
                                          action="{{ route('pos.income.destroy', $income) }}"
                                          onsubmit="return confirm('Delete this record? Business balance will be restored.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 underline hover:text-red-800">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td class="px-5 py-10 text-center text-slate-400" colspan="9">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($incomes->hasPages())
        <div class="mt-4">
            {{ $incomes->links('pagination::tailwind') }}
        </div>
    @endif

</div>
@endsection