@extends('inventory.layouts.inventory')

@section('title','Purchases')
@section('heading','Purchases (Stock-In)')
@section('subtitle','Record purchases and increase stock')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
  <form class="flex gap-2" method="GET" action="{{ route('inventory.purchases.index') }}">
    <input name="q" value="{{ $q }}" placeholder="Search supplier or invoice..."
           class="w-full md:w-80 rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-200" />
    <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Search
    </button>
  </form>

  <a href="{{ route('inventory.purchases.create') }}"
     class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
    + New Purchase
  </a>
</div>

<div x-data="{
  open: false,
  mode: 'all',
  fromDate: '',
  toDate: '',
  getExportUrl(type) {
    if (this.mode === 'all' || !this.fromDate || !this.toDate) {
      return '{{ route('inventory.purchases.export', 'TYPE') }}'.replace('TYPE', type);
    }
    return '{{ route('inventory.purchases.export', 'TYPE') }}'.replace('TYPE', type) + '?from=' + this.fromDate + '&to=' + this.toDate;
  }
}" class="relative">
  <button
    type="button"
    @click="open = !open"
    class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
    Export ▾
  </button>

  <div
    x-cloak
    x-show="open"
    x-transition
    @click.outside="open = false"
    class="absolute right-0 mt-2 w-72 bg-white border border-slate-200 rounded-xl shadow z-50 p-3 space-y-3"
  >

    <!-- Mode selector -->
    <div class="flex gap-2">
      <button
        @click="mode='all'"
        :class="mode==='all' ? 'bg-slate-900 text-white' : 'bg-slate-100'"
        class="flex-1 px-3 py-1 rounded-lg text-sm">
        All
      </button>

      <button
        @click="mode='range'"
        :class="mode==='range' ? 'bg-slate-900 text-white' : 'bg-slate-100'"
        class="flex-1 px-3 py-1 rounded-lg text-sm">
        Date Range
      </button>
    </div>

    <!-- Date range -->
    <div x-show="mode==='range'" class="space-y-2">
      <input type="date" x-model="fromDate"
        class="w-full rounded-lg border-slate-200 text-sm"
        placeholder="From date">
      
      <input type="date" x-model="toDate"
        class="w-full rounded-lg border-slate-200 text-sm"
        placeholder="To date">
      
      <div x-show="mode==='range' && (!fromDate || !toDate)" 
           class="text-xs text-amber-600 bg-amber-50 p-2 rounded">
        Please select both "From" and "To" dates to filter exports
      </div>
    </div>

    <!-- Export buttons -->
    <div class="grid grid-cols-3 gap-2 pt-2 border-t">
      <a :href="getExportUrl('excel')"
        class="text-center px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-sm">
        Excel
      </a>

      <a :href="getExportUrl('csv')"
        class="text-center px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-sm">
        CSV
      </a>

      <a :href="getExportUrl('pdf')"
        class="text-center px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-sm">
        PDF
      </a>
    </div>

  </div>
</div>

<div class="mt-5 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="text-slate-500 bg-slate-50">
      <tr>
        <th class="text-left px-5 py-3">Date</th>
        <th class="text-left px-5 py-3">Supplier</th>
        <th class="text-left px-5 py-3">Invoice No</th>
        <th class="text-left px-5 py-3">Total Cost</th>
        <th class="text-left px-5 py-3">Created By</th>
        <th class="text-right px-5 py-3">Action</th>
      </tr>
    </thead>


    <tbody class="divide-y">
      @forelse($purchases as $p)
        <tr>
          <td class="px-5 py-4">{{ $p->purchase_date->format('Y-m-d') }}</td>
          <td class="px-5 py-4 font-semibold">{{ $p->supplier->name ?? '—' }}</td>
          <td class="px-5 py-4">{{ $p->invoice_no ?? '—' }}</td>
          <td class="px-5 py-4">Rs {{ number_format((float)$p->total_cost, 2) }}</td>
          <td class="px-5 py-4">{{ $p->creator->name ?? '—' }}</td>
          <td class="px-5 py-4 text-right">
            <a class="underline" href="{{ route('inventory.purchases.show',$p) }}">View</a>
          </td>
        </tr>
      @empty
        <tr><td class="px-5 py-6 text-slate-500" colspan="6">No purchases found.</td></tr>
      @endforelse
      
    </tbody>
  </table>
  
</div>

<div class="mt-4">
  {{ $purchases->links() }}

</div>



  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

</div>
@endsection
