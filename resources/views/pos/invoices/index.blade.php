@extends('inventory.layouts.inventory')

@section('title', 'POS Invoices')
@section('heading', 'POS Invoices')
@section('subtitle', 'Manage sales invoices and customer transactions')

@section('content')
<div class="max-w-7xl mx-auto">
    @if(session('success'))
        <div id="success-message" class="mb-4 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-700 p-6 text-white">
            <h2 class="text-2xl font-bold">POS Invoices</h2>
            <p class="text-sm opacity-90">Manage sales invoices and customer transactions</p>
        </div>

        <!-- Filters Section -->
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="text-sm text-slate-600">Search:</label>
                    <input type="text" 
                           placeholder="Search by invoice number, customer, or product..." 
                           class="mt-1 w-full md:w-64 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                </div>
                
                <div>
                    <label class="text-sm text-slate-600">Date From:</label>
                    <input type="date" 
                           class="mt-1 w-full md:w-40 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                </div>
                
                <div>
                    <label class="text-sm text-slate-600">Date To:</label>
                    <input type="date" 
                           class="mt-1 w-full md:w-40 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2">
                </div>

                <div>
                    <a href="{{ route('pos.invoices.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Invoice
                    </a>
                </div>

                <div x-data="{ 
                    open: false,
                    mode: 'all',
                    fromDate: '',
                    toDate: '',
                    getExportUrl(format) {
                        if (this.mode === 'all' || !this.fromDate || !this.toDate) {
                            return '{{ route('pos.invoices.bulk-export', 'FORMAT') }}'.replace('FORMAT', format);
                        }
                        return '{{ route('pos.invoices.bulk-export', 'FORMAT') }}'.replace('FORMAT', format) + '?from=' + this.fromDate + '&to=' + this.toDate;
                    }
                }" class="relative">
                    <button
                        type="button"
                        @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export ▾
                    </button>

                    <div
                        x-show="open"
                        x-cloak
                        @click.away="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-slate-200 z-10 p-3 space-y-3">

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
                                class="w-full rounded-lg border-slate-200 text-sm px-3 py-2"
                                placeholder="From date">
                            
                            <input type="date" x-model="toDate"
                                class="w-full rounded-lg border-slate-200 text-sm px-3 py-2"
                                placeholder="To date">
                            
                            <div x-show="mode==='range' && (!fromDate || !toDate)" 
                                 class="text-xs text-amber-600 bg-amber-50 p-2 rounded">
                                Please select both "From" and "To" dates to filter exports
                            </div>
                        </div>

                        <!-- Export buttons -->
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t">
                            <a :href="getExportUrl('pdf')"
                                    class="w-full text-center px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-sm">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                PDF
                            </a>
                            <a :href="getExportUrl('excel')"
                                    class="w-full text-center px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-sm">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v7m3-2h6"></path>
                                </svg>
                                Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <!-- Invoices Table -->
        <div class="p-6">
            @if($invoices->count() > 0)
                <div class="overflow-x-auto border border-slate-200 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="text-slate-700 bg-slate-100">
                            <tr>
                                <th class="text-left px-4 py-3 font-medium">Invoice No</th>
                                <th class="text-left px-4 py-3 font-medium">Business</th>
                                <th class="text-left px-4 py-3 font-medium">Customer</th>
                                <th class="text-left px-4 py-3 font-medium">Date</th>
                                <th class="text-left px-4 py-3 font-medium">Payment</th>
                                <th class="text-left px-4 py-3 font-medium">Items</th>
                                <th class="text-right px-4 py-3 font-medium">Total</th>
                                <th class="text-center px-4 py-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($invoices as $invoice)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-medium">{{ $invoice->invoice_no }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $invoice->business->business_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $invoice->customer->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $invoice->purchase_date->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        @switch($invoice->payment_method)
                                            @case('cash')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                    Cash
                                                </span>
                                                @break
                                            @case('credit')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                    </svg>
                                                    Credit
                                                </span>
                                                @break
                                            @case('bank')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                    Bank
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $invoice->payment_method }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="max-w-xs">
                                            @if($invoice->items->count() > 1)
                                                <span class="text-slate-600">{{ $invoice->items->count() }} items</span>
                                            @else
                                                <span class="text-slate-600">{{ $invoice->items->first()->product_name }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-slate-900">
                                        Rs {{ number_format($invoice->total_cost, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('pos.invoices.show', $invoice->id) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                View
                                            </a>
                                            
                                            <div x-data="{ 
                                                open: false,
                                                getExportUrl(format) {
                                                    return `{{ route('pos.invoices.export', ['ID', 'FORMAT']) }}`.replace('ID', {{ $invoice->id }}).replace('FORMAT', format);
                                                }
                                            }" class="relative">
                                                <button
                                                    type="button"
                                                    @click="open = !open"
                                                    class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                    Export ▾
                                                </button>

                                                <div
                                                    x-show="open"
                                                    x-cloak
                                                    @click.away="open = false"
                                                    x-transition
                                                    class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-slate-200 z-10">
                                                    <div class="py-1">
                                                        <a :href="getExportUrl('pdf')"
                                                                class="block w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">
                                                            PDF
                                                        </a>
                                                        <a :href="getExportUrl('excel')"
                                                                class="block w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">
                                                            Excel
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if(auth()->user()->can('delete-invoices'))
                                                <form method="POST" action="{{ route('pos.invoices.destroy', $invoice->id) }}" 
                                                      class="inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 text-sm font-medium ml-2">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $invoices->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 6h6m2 5H7a2 2 0 01-2-2V9a2 2 0 01-2 2H7a2 2 0 01-2-2v-1a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900 mb-2">No invoices found</h3>
                    <p class="text-slate-600 mb-6">Get started by creating your first sales invoice.</p>
                    <a href="{{ route('pos.invoices.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-base font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Your First Invoice
                    </a>
            @endif
        </div>
    </div>
</div>
@endsection
