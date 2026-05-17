@extends('inventory.layouts.inventory')

@section('title','Edit Income')
@section('heading','Edit Income')
@section('subtitle','Update income record details')

@section('content')
<form method="POST" action="{{ route('pos.income.update', $income) }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="text-sm text-slate-600">Reference No</label>
            <input type="text" name="reference_no"
                   value="{{ old('reference_no', $income->reference_no) }}"
                   placeholder="INV-0001 or INC-0001"
                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
        </div>

        <div>
            <label class="text-sm text-slate-600">Date *</label>
            <input type="date" name="transaction_date"
                   value="{{ old('transaction_date', $income->transaction_date->format('Y-m-d')) }}"
                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                   required />
        </div>

        <div>
            <label class="text-sm text-slate-600">Customer</label>
            <select name="customer_id" 
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
                <option value="">Select Customer</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" @selected(old('customer_id', $income->customer_id)==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm text-slate-600">Business (optional)</label>
            <select name="business_id"
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
                <option value="">Select Business</option>
                @foreach($businesses as $b)
                    <option value="{{ $b->id }}" @selected(old('business_id', $income->business_id)==$b->id)>{{ $b->business_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm text-slate-600">Amount Received *</label>
            <input type="number" step="1" name="amount_received"
                   value="{{ old('amount_received', $income->amount_received) }}"
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
                    <option value="{{ $method }}" @selected(old('payment_method', $income->payment_method) == $method)>
                        {{ ucfirst($method) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm text-slate-600">Income Type *</label>
            <select name="income_type"
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                    required>
                <option value="">Select Type</option>
                <option value="Sale" @selected(old('income_type', $income->income_type) == 'Sale')>Sale</option>
                <option value="Due Collection" @selected(old('income_type', $income->income_type) == 'Due Collection')>Due Collection</option>
                <option value="Other" @selected(old('income_type', $income->income_type) == 'Other')>Other</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="text-sm text-slate-600">Description (optional)</label>
            <textarea name="description" rows="2"
                      class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                      placeholder="Any note about this transaction">{{ old('description', $income->description) }}</textarea>
        </div>
    </div>

    <div class="flex justify-between pt-4">
        <a href="{{ route('pos.income.index') }}" data-back-button
           class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">
            Back
        </a>
        <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200">
            Update Income
        </button>
    </div>

</form>
@endsection
