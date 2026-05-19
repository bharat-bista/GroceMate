@extends('inventory.layouts.inventory')

@section('title','Edit Expense')
@section('heading','Edit Expense')
@section('subtitle','Update an existing expense transaction')

@section('content')
<form method="POST" action="{{ route('pos.expenses.update', $expense) }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="text-sm text-slate-600">Reference No</label>
            <input type="text" name="reference_no"
                   value="{{ old('reference_no', $expense->reference_no) }}"
                   placeholder="EXP-0001"
                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
        </div>

        <div>
            <label class="text-sm text-slate-600">Date *</label>
            <input type="date" name="transaction_date"
                   value="{{ old('transaction_date', $expense->transaction_date?->format('Y-m-d')) }}"
                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                   required />
        </div>

        <div>
            <label class="text-sm text-slate-600">Business (optional)</label>
            <select name="business_id"
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
                <option value="">Select Business</option>
                @foreach($businesses as $business)
                    <option value="{{ $business->id }}" @selected(old('business_id', $expense->business_id) == $business->id)>{{ $business->business_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm text-slate-600">Amount *</label>
            <input type="number" step="any" min="1" name="amount"
                   data-money inputmode="numeric" max="9999999"
                   value="{{ old('amount', $expense->amount) }}"
                   placeholder="0"
                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                   required />
        </div>

        <div>
            <label class="text-sm text-slate-600">Payment Method *</label>
            <select name="payment_method"
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                    required>
                <option value="">Select Method</option>
                @foreach($paymentMethods as $method)
                    <option value="{{ $method }}" @selected(old('payment_method', $expense->payment_method) == $method)>
                        {{ ucfirst($method) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm text-slate-600">Expense Type *</label>
            <input type="text" name="expense_type"
                   value="{{ old('expense_type', $expense->expense_type) }}"
                   placeholder="Office Rent, Transport, Utility"
                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                   required />
        </div>

        <div class="md:col-span-2">
            <label class="text-sm text-slate-600">Description (optional)</label>
            <textarea name="description" rows="2"
                      class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                      placeholder="Any note about this expense transaction">{{ old('description', $expense->description) }}</textarea>
        </div>
    </div>

    <div class="flex justify-between pt-4">
        <a href="{{ route('pos.expenses.index') }}" data-back-button
           class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">
            Back
        </a>
        <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200">
            Update Expense
        </button>
    </div>

</form>
@endsection
