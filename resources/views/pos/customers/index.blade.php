@extends('inventory.layouts.inventory')

@section('title','Customers')
@section('heading','Customers')
@section('subtitle','Manage retail and wholesale customers')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
  <form class="flex gap-2" method="GET" action="{{ route('pos.customers.index') }}">
    <input name="q" value="{{ $q }}" placeholder="Search name/phone..."
           class="w-full md:w-80 rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-200" />
    <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Search
    </button>
  </form>

  <a href="{{ route('pos.customers.create') }}"
     class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
    + Add Customer
  </a>
</div>

<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="text-slate-500 bg-slate-50">
      <tr>
        <th class="text-left px-5 py-3">Customer</th>
        <th class="text-left px-5 py-3">Type</th>
        <th class="text-left px-5 py-3">Phone</th>
        <th class="text-left px-5 py-3">VAT Number</th>
        <th class="text-left px-5 py-3">Total Due</th>
        <th class="text-right px-5 py-3">Actions</th>
      </tr>
    </thead>

    <tbody class="divide-y">
      @forelse($customers as $c)
        <tr>
          <td class="px-5 py-4">
            <div class="font-semibold">{{ $c->name }}</div>
            <div class="text-xs text-slate-500">ID: {{ $c->id }}</div>
          </td>
          <td class="px-5 py-4 capitalize">{{ $c->customer_type }}</td>
          <td class="px-5 py-4">{{ $c->phone }}</td>
          <td class="px-5 py-4">{{ $c->vat_number ?? '-' }}</td>
          <td class="px-5 py-4">
            @if($c->total_due > 0)
              <span class="text-red-600 font-semibold">
                Rs {{ number_format($c->total_due,2) }}
              </span>
            @else
              <span class="text-emerald-600 font-semibold">0.00</span>
            @endif
          </td>
          <td class="px-5 py-4 text-right">
            <div class="inline-flex items-center gap-3">
              <a class="underline text-slate-800"
                 href="{{ route('pos.customers.edit',$c) }}">Edit</a>

              <form method="POST" action="{{ route('pos.customers.destroy',$c) }}"
                    onsubmit="return confirm('Delete this customer?');">
                @csrf
                @method('DELETE')
                <button class="text-red-700 underline">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td class="px-5 py-6 text-slate-500" colspan="6">
            No customers found.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $customers->links() }}
</div>
@endsection
