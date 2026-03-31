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
            <div class="flex items-center">
                <div>
                    <h2 class="text-2xl font-bold">Invoice #{{ $invoice->invoice_no }}</h2>
                    <p class="text-sm opacity-90">Sales Invoice - {{ $invoice->invoice_date->format('M d, Y') }}</p>
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
                        <span class="text-sm font-medium text-slate-900">{{ $invoice->invoice_date->format('M d, Y') }}</span>
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
            <a href="{{ route('pos.invoices.index') }}" data-back-button
               class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
                Back to Invoices
            </a>
            
            @if($invoice->customer && $invoice->customer->email)
                <button type="button" id="sendInvoiceBtn" 
                        class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Send to Customer
                </button>
            @endif
            
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

<!-- Email Confirmation Modal -->
<div id="emailModal" 
     class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
     style="display: none; align-items: center; justify-content: center; padding: 1rem;">
    <div class="bg-white rounded-2xl shadow-2xl p-6 animate-scaleIn"
         style="width: 100%; max-width: 448px; margin: 0 auto;">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Send Invoice to Customer?</h3>
        <p class="text-gray-600 text-sm mb-6">Would you like to send this invoice to the customer's email address?</p>
        
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">Customer Email</label>
            <input type="email" id="customerEmailInput" class="w-full rounded-xl border border-gray-300 px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none" readonly>
        </div>
        
        <div class="flex gap-3">
            <button type="button" id="sendEmailYes" class="flex-1 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700 transition">
                Yes, Send Email
            </button>
            <button type="button" id="sendEmailNo" class="flex-1 py-2 rounded-xl bg-gray-600 text-white hover:bg-gray-700 transition">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
// Email modal functionality
const emailModal = document.getElementById('emailModal');
const sendInvoiceBtn = document.getElementById('sendInvoiceBtn');
const sendEmailYes = document.getElementById('sendEmailYes');
const sendEmailNo = document.getElementById('sendEmailNo');
const customerEmailInput = document.getElementById('customerEmailInput');

// Show email modal when send button is clicked
if (sendInvoiceBtn) {
    sendInvoiceBtn.addEventListener('click', function() {
        const customerEmail = "{{ $invoice->customer->email ?? '' }}";
        customerEmailInput.value = customerEmail;
        emailModal.style.display = 'flex'; // Show modal
    });
}

// Handle Yes button - send email
sendEmailYes.addEventListener('click', function() {
    emailModal.style.display = 'none'; // Hide modal
    
    // Show loading state
    sendInvoiceBtn.disabled = true;
    sendInvoiceBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Sending...';
    
    // Send AJAX request to send email
    fetch('{{ route("pos.invoices.send-email", $invoice->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const successDiv = document.createElement('div');
            successDiv.className = 'mb-4 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm';
            successDiv.textContent = 'Invoice sent successfully to customer!';
            document.querySelector('.max-w-4xl').insertBefore(successDiv, document.querySelector('.bg-white'));
            
            // Remove after 5 seconds
            setTimeout(() => successDiv.remove(), 5000);
        } else {
            // Show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'mb-4 p-4 rounded-xl bg-red-100 text-red-700 border border-red-200 shadow-sm';
            errorDiv.textContent = data.message || 'Failed to send invoice. Please try again.';
            document.querySelector('.max-w-4xl').insertBefore(errorDiv, document.querySelector('.bg-white'));
            
            // Remove after 5 seconds
            setTimeout(() => errorDiv.remove(), 5000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'mb-4 p-4 rounded-xl bg-red-100 text-red-700 border border-red-200 shadow-sm';
        errorDiv.textContent = 'Failed to send invoice. Please try again.';
        document.querySelector('.max-w-4xl').insertBefore(errorDiv, document.querySelector('.bg-white'));
        
        // Remove after 5 seconds
        setTimeout(() => errorDiv.remove(), 5000);
    })
    .finally(() => {
        // Reset button state
        sendInvoiceBtn.disabled = false;
        sendInvoiceBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg> Send to Customer';
    });
});

// Handle No button - close modal
sendEmailNo.addEventListener('click', function() {
    emailModal.style.display = 'none'; // Hide modal
});

// Close modal when clicking outside
emailModal.addEventListener('click', function(e) {
    if (e.target === emailModal) {
        emailModal.style.display = 'none'; // Hide modal
    }
});
</script>
@endsection
