@extends('inventory.layouts.inventory')

@section('title','Expenses')
@section('heading','Expenses')
@section('subtitle','Track business spending and operational costs')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Expenses</p>
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($totalExpense, 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a5 5 0 00-10 0v2M5 9h14l-1 10H6L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">This Month</p>
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($thisMonthExpense, 0) }}</p>
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
                    <p class="text-3xl font-bold mt-2">Rs {{ number_format($todayExpense, 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Expense Transactions</h2>
                <p class="text-sm text-slate-600 mt-1">All operational and purchase-related spending</p>
            </div>
            <a href="{{ route('pos.expenses.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                + Add Expense
            </a>
        </div>

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <form method="GET" action="{{ route('pos.expenses.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Reference, type, business..." class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Payment Method</label>
                        <select name="payment_method" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All</option>
                            <option value="cash" @selected(request('payment_method') == 'cash')>Cash</option>
                            <option value="bank" @selected(request('payment_method') == 'bank')>Bank</option>
                            <option value="Esewa" @selected(request('payment_method') == 'Esewa')>Esewa</option>
                            <option value="Khalti" @selected(request('payment_method') == 'Khalti')>Khalti</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Expense Type</label>
                        <select name="expense_type" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Types</option>
                            @foreach($expenseTypes as $type)
                                <option value="{{ $type }}" @selected(request('expense_type') == $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-3">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Search</button>
                        <a href="{{ route('pos.expenses.index') }}" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        @if($expenses->count() > 0)
            <div class="text-xs text-slate-500 px-6 pt-4 pb-2">
                Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }} of {{ $expenses->total() }} results
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">#</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase">Date</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase">Reference</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase">Type</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase">Business</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase">Payment</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase">Amount</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase">Notes</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($expenses as $index => $expense)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ ($expenses->currentPage() - 1) * $expenses->perPage() + $index + 1 }}</td>
                            <td class="px-6 py-4">{{ $expense->transaction_date?->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-900">{{ $expense->reference_no ?: 'EXP-' . str_pad($expense->id, 4, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-xs text-slate-400">ID: {{ $expense->id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ $expense->expense_type }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $expense->business->business_name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($expense->payment_method == 'cash') bg-green-100 text-green-800
                                    @elseif($expense->payment_method == 'bank') bg-purple-100 text-purple-800
                                    @elseif(in_array($expense->payment_method, ['Esewa','esewa'])) bg-indigo-100 text-indigo-800
                                    @elseif(in_array($expense->payment_method, ['Khalti','khalti'])) bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($expense->payment_method) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-red-600">- Rs {{ number_format($expense->amount, 0) }}</td>
                            <td class="px-6 py-4">
                                @if($expense->description)
                                    <div class="text-xs text-slate-500 max-w-xs truncate" title="{{ $expense->description }}">
                                        {{ $expense->description }}
                                    </div>
                                @else
                                    <div class="text-sm text-slate-400">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-3">
                                    <a href="{{ route('pos.expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-900 font-medium">View</a>
                                    <a href="{{ route('pos.expenses.edit', $expense) }}" class="text-emerald-600 hover:text-emerald-900 font-medium">Edit</a>
                                    <form method="POST" action="{{ route('pos.expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense record?')">
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
                                    <p class="text-lg font-medium">No expense records found</p>
                                    <p class="text-sm mt-1">Try adjusting your filters or add a new expense record.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('pos.expenses.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                                            + Add Expense
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $expenses->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
