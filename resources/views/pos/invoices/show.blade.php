@extends('inventory.layouts.inventory')

@section('title', 'Invoice Details')
@section('heading', 'Invoice Details')
@section('subtitle', 'View sales invoice details and items')

@section('content')
<div class="max-w-4xl mx-auto">
    @if(session('success'))
        <div id="success-message" class="mb-4 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-green-500 to-green-700 p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Invoice #{{ $invoice->invoice_no }}</h2>
                    <p class="text-sm opacity-90">Sales Invoice - {{ $invoice->purchase_date->format('M d, Y') }}</p>
                </div>
                <div class="text-right">
                    <a href="{{ route('pos.invoices.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 text-white rounded-lg hover:bg-opacity-30 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7 7m-8 8l7-7"></path>
                        </svg>
                        Back to Invoices
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer & Invoice Info -->
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Business Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600">Business:</span>
                        <span class="text-sm font-medium text-slate-900">{{ $invoice->business->business_name ?? 'N/A' }}</span>
                    </div>
                    @if($invoice->business)
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-600">Type:</span>
                            <span class="text-sm font-medium text-slate-900">{{ $invoice->business->business_type ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-600">Phone:</span>
                            <span class="text-sm font-medium text-slate-900">{{ $invoice->business->phone ?? 'N/A' }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Customer Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600">Name:</span>
                        <span class="text-sm font-medium text-slate-900">{{ $invoice->customer->name ?? 'N/A' }}</span>
                    </div>
                    @if($invoice->customer)
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-600">Email:</span>
                            <span class="text-sm font-medium text-slate-900">{{ $invoice->customer->email ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-600">Phone:</span>
                            <span class="text-sm font-medium text-slate-900">{{ $invoice->customer->phone ?? 'N/A' }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Invoice Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600">Invoice Number:</span>
                        <span class="text-sm font-medium text-slate-900">{{ $invoice->invoice_no }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600">Invoice Date:</span>
                        <span class="text-sm font-medium text-slate-900">{{ $invoice->purchase_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600">Payment Method:</span>
                        <span class="text-sm font-medium text-slate-900">
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
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600">Created By:</span>
                        <span class="text-sm font-medium text-slate-900">{{ $invoice->creator->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Invoice Items</h3>
            
            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="text-slate-700 bg-slate-100">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium">Product</th>
                            <th class="text-left px-4 py-3 font-medium">Unit</th>
                            <th class="text-left px-4 py-3 font-medium">Quantity</th>
                            <th class="text-left px-4 py-3 font-medium">Unit Price</th>
                            <th class="text-left px-4 py-3 font-medium">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($invoice->items as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $item->product_name }}</td>
                                <td class="px-4 py-3">{{ $item->unit }}</td>
                                <td class="px-4 py-3">{{ number_format($item->qty, 3) }}</td>
                                <td class="px-4 py-3">Rs {{ number_format($item->unit_cost, 2) }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">Rs {{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-300">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right font-semibold text-slate-700">Subtotal:</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">Rs {{ number_format($invoice->items->sum('line_total'), 2) }}</td>
                        </tr>
                        @if($invoice->total_cost > $invoice->items->sum('line_total'))
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-semibold text-slate-700">Tax Applied:</td>
                                <td class="px-4 py-3 font-semibold text-red-600">Rs {{ number_format($invoice->total_cost - $invoice->items->sum('line_total'), 2) }}</td>
                            </tr>
                        @endif
                        <tr class="bg-slate-100">
                            <td colspan="4" class="px-4 py-4 text-right font-bold text-lg text-slate-900">Total Amount:</td>
                            <td class="px-4 py-4 font-bold text-lg text-green-700">Rs {{ number_format($invoice->total_cost, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="p-6 border-t border-slate-200 flex gap-3">
            <a href="{{ route('pos.invoices.index') }}" 
               class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
                Back to Invoices
            </a>
            
            @if(auth()->user()->can('delete-invoices'))
                <form method="POST" action="{{ route('pos.invoices.destroy', $invoice->id) }}" 
                      class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-red-600 text-white hover:bg-red-700">
                        Delete Invoice
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
