@extends('inventory.layouts.inventory')

@section('title', 'Order Details - Ecommerce')
@section('heading', 'Order Details')
@section('subtitle', 'View and manage order')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('inventory.orders.index') }}" class="flex items-center gap-2 text-slate-600 hover:text-slate-900">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">Order {{ $order->order_number }}</h2>
                            <p class="text-sm text-slate-500 mt-1">Placed on {{ $order->created_at->format('M d, Y at h:i A') }}</p>
                        </div>
                        <div class="flex gap-2">
                            @if($order->customer_email)
                                <button type="button" id="sendOrderEmailBtn"
                                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                                    <i class="fas fa-envelope mr-2"></i> Send Email
                                </button>
                            @endif
                            @if($order->delivery_status !== 'cancelled' && $order->delivery_status !== 'delivered')
                                <button onclick="document.getElementById('deliveryModal').showModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                                    <i class="fas fa-truck mr-2"></i> Update Delivery
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Order Items</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                                <img src="{{ $item->image ?? asset('assets/img/product/product1.jpg') }}" alt="{{ $item->product_name }}" class="w-16 h-16 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h4 class="font-medium text-slate-900">{{ $item->product_name }}</h4>
                                    <p class="text-sm text-slate-500">Rs. {{ number_format($item->price, 0) }} x {{ $item->quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-semibold text-slate-900">Rs. {{ number_format($item->subtotal, 0) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Totals -->
                    <div class="mt-6 flex justify-end">
                        <div class="w-64 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600">Subtotal</span>
                                <span class="font-medium">Rs. {{ number_format($order->subtotal, 0) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600">Delivery</span>
                                <span class="font-medium">Rs. {{ number_format($order->delivery_charge, 0) }}</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-200 pt-2">
                                <span class="font-semibold">Total</span>
                                <span class="font-bold text-emerald-600">Rs. {{ number_format($order->total_amount, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Slip Verification (for Connect IPS) -->
            @if($order->payment_method === 'connectips' && $order->payment_slip)
                <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-200">
                        <h3 class="text-lg font-semibold text-slate-900">Payment Slip</h3>
                    </div>
                    <div class="p-6">
                        @if($order->payment_slip_is_pdf)
                            <a href="{{ $order->payment_slip_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-700 mb-4">
                                <i class="fas fa-file-pdf text-red-600"></i>
                                <span>Open Payment Slip (PDF)</span>
                            </a>
                        @else
                            <img src="{{ $order->payment_slip_url }}" alt="Payment Slip" class="max-w-xs rounded-lg border border-slate-200 mb-4">
                        @endif
                        
                        @if($order->payment_status === 'pending')
                            <form method="POST" action="{{ route('inventory.orders.verify-slip', $order) }}" class="flex gap-3">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="payment_status" value="verified">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                                    <i class="fas fa-check mr-2"></i> Verify Payment
                                </button>
                            </form>
                            <form method="POST" action="{{ route('inventory.orders.verify-slip', $order) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="payment_status" value="failed">
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium ml-2">
                                    <i class="fas fa-times mr-2"></i> Reject
                                </button>
                            </form>
                        @else
                            <div class="mt-4">
                                @if($order->payment_status === 'verified')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-2"></i> Payment Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-2"></i> Payment Rejected
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Cancellation Request Card -->
            @if($order->cancellation_request_status === 'pending')
                <div class="bg-white shadow-xl rounded-3xl border border-amber-200 overflow-hidden">
                    <div class="p-6 border-b border-amber-200 bg-amber-50">
                        <h3 class="text-lg font-semibold text-amber-900"><i class="fas fa-ban mr-2"></i>Cancellation Request</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($order->cancellation_requested_at)
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Requested At</label>
                                <span class="text-sm text-slate-700">{{ $order->cancellation_requested_at->format('M d, Y h:i A') }}</span>
                            </div>
                        @endif
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Customer Reason</label>
                            <p class="text-sm text-slate-700 bg-slate-50 rounded-lg p-3 border border-slate-200">{{ $order->cancellation_request_reason }}</p>
                        </div>
                        <form method="POST" action="{{ route('inventory.orders.cancel-request.approve', $order) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium mb-2"
                                    onclick="return confirm('Approve cancellation? This will cancel the order and restore stock.')">
                                <i class="fas fa-check mr-2"></i> Approve Cancellation
                            </button>
                        </form>
                        <form method="POST" action="{{ route('inventory.orders.cancel-request.reject', $order) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 text-sm font-medium">
                                <i class="fas fa-times mr-2"></i> Reject Request
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($order->cancellation_request_status === 'approved')
                <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
                    <div class="p-6">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-ban mr-2"></i> Cancellation Approved
                        </span>
                    </div>
                </div>
            @elseif($order->cancellation_request_status === 'rejected')
                <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
                    <div class="p-6">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-slate-100 text-slate-700">
                            <i class="fas fa-times mr-2"></i> Cancellation Rejected
                        </span>
                    </div>
                </div>
            @endif

            <!-- Customer Info -->
            <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Customer Details</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Name</label>
                        <span class="font-medium text-slate-900">{{ $order->customer_name }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Phone</label>
                        <span class="font-medium text-slate-900">{{ $order->customer_phone }}</span>
                    </div>
                    @if($order->customer_email)
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Email</label>
                        <span class="font-medium text-slate-900">{{ $order->customer_email }}</span>
                    </div>
                    @endif
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Delivery Address</label>
                        <span class="text-slate-700">{{ $order->delivery_address }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Cards -->
            <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Status</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex flex-wrap gap-2">
                        @if($order->isLocked())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Delivered &amp; Locked</span>
                        @endif
                        @if($order->isPaymentLocked())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Payment Locked</span>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-2">Delivery Status</label>
                        @if($order->isLocked() || $order->delivery_status === 'cancelled')
                            <select name="delivery_status"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-slate-100 text-slate-600 cursor-not-allowed"
                                    disabled>
                                <option value="pending" {{ $order->delivery_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->delivery_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->delivery_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->delivery_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->delivery_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Status is locked and cannot be changed</p>
                        @else
                            <form method="POST" action="{{ route('inventory.orders.delivery-status', $order) }}">
                                @csrf
                                @method('PATCH')
                                <select name="delivery_status"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                        onchange="this.form.submit()">
                                    <option value="pending" {{ $order->delivery_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->delivery_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->delivery_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->delivery_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->delivery_status === 'cancelled' ? 'selected' : '' }} {{ $order->delivery_status === 'shipped' ? 'disabled' : '' }}>Cancelled</option>
                                </select>
                            </form>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-2">Payment Status</label>
                        @if($order->isLocked() || $order->delivery_status === 'cancelled')
                            <select name="payment_state"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-slate-100 text-slate-600 cursor-not-allowed"
                                    disabled>
                                <option value="paid" {{ $order->payment_status === 'verified' ? 'selected' : '' }}>Paid</option>
                                <option value="unpaid" selected>Unpaid</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Order is locked and cannot be changed</p>
                        @elseif($order->payment_method === 'esewa')
                            {{-- eSewa is always auto-verified at checkout; no manual override. --}}
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1.5"></i> Auto-Verified (eSewa)
                            </span>
                        @elseif($order->payment_method === 'connectips')
                            {{-- Bank transfer: payment state is set only via the slip verification card above. --}}
                            @if($order->payment_status === 'verified')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1.5"></i> Payment Verified
                                </span>
                            @elseif($order->payment_status === 'failed')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1.5"></i> Payment Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1.5"></i> Awaiting Slip Verification
                                </span>
                                <p class="text-xs text-slate-500 mt-1">Use the payment slip card to verify or reject.</p>
                            @endif
                        @elseif($order->isPaymentLocked())
                            <select name="payment_state"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-slate-100 text-slate-600 cursor-not-allowed"
                                    disabled>
                                <option value="paid" selected>Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Status is locked and cannot be changed</p>
                        @else
                            {{-- COD only: admin marks as paid when cash is collected at delivery. --}}
                            <form method="POST" action="{{ route('inventory.orders.payment-status', $order) }}">
                                @csrf
                                @method('PATCH')
                                <select name="payment_state"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                        onchange="this.form.submit()">
                                    <option value="paid" {{ $order->payment_status === 'verified' ? 'selected' : '' }}>Paid (cash collected)</option>
                                    <option value="unpaid" {{ $order->payment_status !== 'verified' ? 'selected' : '' }}>Unpaid</option>
                                </select>
                            </form>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Payment Method</label>
                        <span class="font-medium text-slate-900">
                            @if($order->payment_method === 'esewa')
                                <i class="fas fa-mobile-alt mr-1"></i> eSewa
                            @elseif($order->payment_method === 'connectips')
                                <i class="fas fa-university mr-1"></i> Connect IPS
                            @else
                                <i class="fas fa-money-bill-wave mr-1"></i> Cash on Delivery
                            @endif
                        </span>
                    </div>

                    @if($order->transaction_id)
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Transaction ID</label>
                        <span class="text-sm text-slate-700 font-mono">{{ $order->transaction_id }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Message Modal -->
<div id="orderEmailModal"
     class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
     style="display: none; align-items: center; justify-content: center; padding: 1rem;">
    <div class="bg-white rounded-2xl shadow-2xl p-6 animate-scaleIn"
         style="width: 100%; max-width: 520px; margin: 0 auto;">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Send Message to Customer</h3>
        <p class="text-gray-600 text-sm mb-5">Write a quick note and send it to the customer's email.</p>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Customer Email</label>
            <input type="email" id="orderCustomerEmailInput" class="w-full rounded-xl border border-gray-300 px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:outline-none" readonly>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
            <textarea id="orderCustomerMessageInput" rows="5"
                      class="w-full rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                      placeholder="Type your message here..."></textarea>
            <p id="orderEmailError" class="text-sm text-red-600 mt-2 hidden">Please enter a message.</p>
        </div>

        <div class="flex gap-3">
            <button type="button" id="sendOrderEmailConfirm" class="flex-1 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition">
                Send Email
            </button>
            <button type="button" id="sendOrderEmailCancel" class="flex-1 py-2 rounded-xl bg-gray-600 text-white hover:bg-gray-700 transition">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
const orderEmailModal = document.getElementById('orderEmailModal');
const sendOrderEmailBtn = document.getElementById('sendOrderEmailBtn');
const sendOrderEmailConfirm = document.getElementById('sendOrderEmailConfirm');
const sendOrderEmailCancel = document.getElementById('sendOrderEmailCancel');
const orderCustomerEmailInput = document.getElementById('orderCustomerEmailInput');
const orderCustomerMessageInput = document.getElementById('orderCustomerMessageInput');
const orderEmailError = document.getElementById('orderEmailError');

if (sendOrderEmailBtn) {
    sendOrderEmailBtn.addEventListener('click', function () {
        orderCustomerEmailInput.value = "{{ $order->customer_email ?? '' }}";
        orderCustomerMessageInput.value = '';
        orderEmailError.classList.add('hidden');
        orderEmailModal.style.display = 'flex';
    });
}

sendOrderEmailConfirm?.addEventListener('click', function () {
    const message = orderCustomerMessageInput.value.trim();

    if (!message) {
        orderEmailError.classList.remove('hidden');
        return;
    }

    orderEmailError.classList.add('hidden');
    orderEmailModal.style.display = 'none';

    sendOrderEmailBtn.disabled = true;
    sendOrderEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';

    fetch('{{ route("inventory.orders.send-message", $order) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message })
    })
        .then(response => response.json())
        .then(data => {
            const container = document.querySelector('.space-y-6');
            if (!container) {
                return;
            }
            const alertDiv = document.createElement('div');
            alertDiv.className = data.success
                ? 'p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm'
                : 'p-4 rounded-xl bg-red-100 text-red-700 border border-red-200 shadow-sm';
            alertDiv.textContent = data.message || 'Unable to send the email.';
            container.insertBefore(alertDiv, container.firstChild);
            setTimeout(() => alertDiv.remove(), 5000);
        })
        .catch(() => {
            const container = document.querySelector('.space-y-6');
            if (!container) {
                return;
            }
            const alertDiv = document.createElement('div');
            alertDiv.className = 'p-4 rounded-xl bg-red-100 text-red-700 border border-red-200 shadow-sm';
            alertDiv.textContent = 'Failed to send email. Please try again.';
            container.insertBefore(alertDiv, container.firstChild);
            setTimeout(() => alertDiv.remove(), 5000);
        })
        .finally(() => {
            sendOrderEmailBtn.disabled = false;
            sendOrderEmailBtn.innerHTML = '<i class="fas fa-envelope mr-2"></i> Send Email';
        });
});

sendOrderEmailCancel?.addEventListener('click', function () {
    orderEmailModal.style.display = 'none';
});

orderEmailModal?.addEventListener('click', function (event) {
    if (event.target === orderEmailModal) {
        orderEmailModal.style.display = 'none';
    }
});
</script>

<!-- Delivery Status Modal -->
@php
    $cancelDisabled = $order->delivery_status === 'shipped';
@endphp
<dialog id="deliveryModal" class="modal p-6 rounded-2xl shadow-2xl border border-slate-200 max-w-md">
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-slate-900">Update Delivery Status</h3>
        <form method="POST" action="{{ route('inventory.orders.delivery-status', $order) }}">
            @csrf
            @method('PATCH')
            <div class="space-y-3">
                <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="delivery_status" value="pending" class="text-emerald-600">
                    <span>Pending</span>
                </label>
                <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="delivery_status" value="processing" class="text-emerald-600">
                    <span>Processing</span>
                </label>
                <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="delivery_status" value="shipped" class="text-emerald-600">
                    <span>Shipped</span>
                </label>
                <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="delivery_status" value="delivered" class="text-emerald-600">
                    <span>Delivered</span>
                </label>
                <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg {{ $cancelDisabled ? 'bg-slate-50 text-slate-400 cursor-not-allowed' : 'hover:bg-slate-50 cursor-pointer' }}">
                    <input type="radio" name="delivery_status" value="cancelled" class="text-red-600" {{ $cancelDisabled ? 'disabled' : '' }}>
                    <span class="{{ $cancelDisabled ? 'text-slate-400' : 'text-red-600' }}">Cancelled</span>
                </label>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">Update</button>
                <button type="button" onclick="document.getElementById('deliveryModal').close()" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-medium">Cancel</button>
            </div>
        </form>
    </div>
</dialog>
@endsection
