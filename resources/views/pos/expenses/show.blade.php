@extends('inventory.layouts.inventory')

@section('title','Expense Details')
@section('heading','Expense Details')
@section('subtitle','Review a specific expense transaction')

@section('content')
<div class="space-y-6">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Expense Details</h2>
                <p class="text-sm text-slate-600 mt-1">Reference: {{ $expense->reference_no ?: 'EXP-' . str_pad($expense->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-500">Amount</p>
                <p class="text-2xl font-bold text-red-600">- Rs {{ number_format($expense->amount, 0) }}</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wide">Expense Type</p>
                <p class="text-base font-semibold text-slate-900 mt-1">{{ $expense->expense_type }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wide">Date</p>
                <p class="text-base font-semibold text-slate-900 mt-1">{{ $expense->transaction_date?->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wide">Payment Method</p>
                <p class="text-base font-semibold text-slate-900 mt-1">{{ ucfirst($expense->payment_method) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wide">Business Account</p>
                <p class="text-base font-semibold text-slate-900 mt-1">{{ $expense->business->business_name ?? 'Not linked' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wide">Created By</p>
                <p class="text-base font-semibold text-slate-900 mt-1">{{ optional($expense->creator)->full_name ?? optional($expense->creator)->name ?? 'System' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs text-slate-500 uppercase tracking-wide">Description</p>
                <p class="text-sm text-slate-700 mt-1">{{ $expense->description ?: 'No description provided.' }}</p>
            </div>
        </div>

        <div class="px-6 pb-6 flex flex-wrap gap-3">
            <a href="{{ route('pos.expenses.index') }}" data-back-button class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">Back to Expenses</a>
            <a href="{{ route('pos.expenses.edit', $expense) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Edit</a>
            <form method="POST" action="{{ route('pos.expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense record?')">
                @csrf
                @method('DELETE')
                <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
