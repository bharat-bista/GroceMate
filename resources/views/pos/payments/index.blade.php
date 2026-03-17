@extends('inventory.layouts.inventory')

@section('title','Supplier Payments')
@section('heading','Supplier Payments')
@section('subtitle','Manage payments to suppliers')

@section('content')



<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
  <form class="flex gap-2" method="GET" action="{{ route('pos.supplier-payments.index') }}">
    <input name="q" value="{{ $q ?? '' }}" placeholder="Search supplier/reference..."
           class="w-full md:w-80 rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-200" />
    <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Search
    </button>
  </form>

  <a href="{{ route('pos.supplier-payments.create') }}"
     class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
    + Add Payment
  </a>
</div>

<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
  @if($payments->count() > 0)
    <div class="text-xs text-slate-500 p-4 pb-2">
        Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
    </div>
  @endif
  <table class="w-full text-sm">
    <thead class="text-slate-500 bg-slate-50">
      <tr>
        <th class="text-left px-5 py-3">#</th>
        <th class="text-left px-5 py-3">Date</th>
        <th class="text-left px-5 py-3">Supplier</th>
        <th class="text-left px-5 py-3">Business Account</th>
        <th class="text-left px-5 py-3">Amount</th>
        <th class="text-left px-5 py-3">Payment Method</th>
        <th class="text-left px-5 py-3">Payment Type</th>
        <th class="text-left px-5 py-3">Status</th>
        <th class="text-left px-5 py-3">Reference</th>
        <th class="text-left px-5 py-3">Bank Charge</th>
        <th class="text-left px-5 py-3">TDS</th>
        <th class="text-right px-5 py-3">Actions</th>
      </tr>
    </thead>

    <tbody class="divide-y">
      @forelse($payments as $index => $payment)
        <tr class="hover:bg-slate-50 transition-colors">
          <td class="px-5 py-4 font-medium text-slate-500">{{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}</td>
          <td class="px-5 py-4">{{ date('M d, Y', strtotime($payment->date)) }}</td>
          <td class="px-5 py-4">
            @if(isset($payment->supplier))
              <div class="font-medium">{{ $payment->supplier->name }}</div>
              <div class="text-xs text-slate-500">{{ $payment->supplier->phone ?? '' }}</div>
            @else
              <span class="text-slate-400">-</span>
            @endif
          </td>
          <td class="px-5 py-4">
            <span class="text-sm">{{ $payment->business_account ?? '-' }}</span>
          </td>
          <td class="px-5 py-4">
            <span class="text-emerald-600 font-semibold">
              Rs {{ number_format($payment->amount, 2) }}
            </span>
          </td>
          <td class="px-5 py-4">
            <span class="px-2 py-1 text-xs rounded-full
              @if($payment->payment_method == 'cash') bg-green-100 text-green-700
              @elseif($payment->payment_method == 'bank') bg-blue-100 text-blue-700
              @elseif($payment->payment_method == 'esewa') bg-purple-100 text-purple-700
              @elseif($payment->payment_method == 'khalti') bg-orange-100 text-orange-700
              @else bg-slate-100 text-slate-700
              @endif">
              {{ ucfirst($payment->payment_method) }}
            </span>
          </td>
          <td class="px-5 py-4">
            <span class="px-2 py-1 text-xs rounded-full
              @if($payment->payment_type == 'external') bg-amber-100 text-amber-700
              @elseif($payment->payment_type == 'integrated') bg-emerald-100 text-emerald-700
              @else bg-slate-100 text-slate-700
              @endif">
              {{ ucfirst($payment->payment_type ?? 'external') }}
            </span>
          </td>
          <td class="px-5 py-4">
            <span class="px-2 py-1 text-xs rounded-full
              @if($payment->status == 'completed') bg-green-100 text-green-700
              @elseif($payment->status == 'processing') bg-blue-100 text-blue-700
              @elseif($payment->status == 'pending_confirmation') bg-amber-100 text-amber-700
              @elseif($payment->status == 'failed') bg-red-100 text-red-700
              @else bg-green-100 text-green-700
              @endif">
              {{ ucfirst($payment->status ?? 'completed') }}
            </span>
          </td>
          <td class="px-5 py-4">
            <span class="text-sm text-slate-600">{{ $payment->payment_reference ?? '-' }}</span>
          </td>
          <td class="px-5 py-4">
            <span class="text-sm text-slate-600">Rs {{ number_format($payment->bank_charge, 2) }}</span>
          </td>
          <td class="px-5 py-4">
            <span class="px-2 py-1 text-xs rounded-full
              @if($payment->tds_applicable) bg-amber-100 text-amber-700
              @else bg-slate-100 text-slate-700
              @endif">
              {{ $payment->tds_applicable ? 'Yes' : 'No' }}
            </span>
          </td>
          <td class="px-5 py-4 text-right">
            <div class="inline-flex items-center gap-3">
              <a class="underline text-slate-800"
                 href="{{ route('pos.supplier-payments.edit', $payment) }}">Edit</a>

              <form method="POST" action="{{ route('pos.supplier-payments.destroy', $payment) }}"
                    onsubmit="return confirm('Delete this payment record?');">
                @csrf
                @method('DELETE')
                <button class="text-red-700 underline">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td class="px-5 py-6 text-slate-500" colspan="12">
            No payment records found.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($payments->hasPages())
<div class="mt-4">
  {{ $payments->links('pagination::tailwind') }}
</div>
@endif

@if(session('success'))
<div class="mt-4 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
  {{ session('success') }}
</div>
@endif
@endsection
