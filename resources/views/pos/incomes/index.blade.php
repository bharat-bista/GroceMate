@extends('inventory.layouts.inventory')

@section('title','Income Records')
@section('heading','Income Records')
@section('subtitle','Manage all income transactions and payments received')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
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

<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
  @if($incomes->count() > 0)
    <div class="text-xs text-slate-500 p-4 pb-2">
        Showing {{ $incomes->firstItem() }} to {{ $incomes->lastItem() }} of {{ $incomes->total() }} results
    </div>
  @endif
  <table class="w-full text-sm">
    <thead class="text-slate-500 bg-slate-50">
      <tr>
        <th class="text-left px-5 py-3">#</th>
        <th class="text-left px-5 py-3">Reference</th>
        <th class="text-left px-5 py-3">Date</th>
        <th class="text-left px-5 py-3">Customer</th>
        <th class="text-left px-5 py-3">Amount</th>
        <th class="text-left px-5 py-3">Payment Method</th>
        <th class="text-left px-5 py-3">Income Type</th>
        <th class="text-right px-5 py-3">Actions</th>
      </tr>
    </thead>

    <tbody class="divide-y">
      @forelse($incomes as $index => $income)
        <tr>
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
          <td class="px-5 py-6 text-slate-500" colspan="7">
            No income records found.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($incomes->hasPages())
<div class="mt-4">
  {{ $incomes->links('pagination::tailwind') }}
</div>
@endif

@if(session('success'))
<div class="mt-4 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
  {{ session('success') }}
</div>
@endif

@endsection