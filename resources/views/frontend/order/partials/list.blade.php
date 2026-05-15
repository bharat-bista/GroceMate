@if($orders->isEmpty())
    <div class="order-card">
        <div class="empty-orders">
            <i class="fas fa-shopping-bag"></i>
            <h3>No Orders Yet</h3>
            <p>You haven't placed any orders yet. Start shopping to see your orders here.</p>
            <a href="{{ route('home') }}">Start Shopping</a>
        </div>
    </div>
@else
    @foreach($orders as $order)
        @php
            $normalizedPaymentStatus = $order->payment_status === 'cod' ? 'pending' : $order->payment_status;
            $paymentLabel = $normalizedPaymentStatus === 'verified'
                ? 'Paid'
                : ($normalizedPaymentStatus === 'pending' ? 'Unpaid' : ucfirst($normalizedPaymentStatus));
        @endphp
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="order-number">{{ $order->order_number }}</div>
                    <div class="order-date">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <span class="order-status-badge status-{{ $order->delivery_status }}">
                        {{ ucfirst($order->delivery_status) }}
                    </span>
                    <span class="payment-badge payment-{{ $normalizedPaymentStatus }}">
                        {{ $paymentLabel }}
                    </span>
                </div>
            </div>
            <div class="order-summary">
                <div class="summary-item">
                    <label>Items</label>
                    <span>{{ $order->items_count }}</span>
                </div>
                <div class="summary-item">
                    <label>Delivery</label>
                    <span>{{ ucfirst($order->delivery_type) }}</span>
                </div>
                <div class="summary-item">
                    <label>Subtotal</label>
                    <span>Rs. {{ number_format($order->subtotal, 0) }}</span>
                </div>
                <div class="summary-item">
                    <label>Delivery Charge</label>
                    <span>Rs. {{ number_format($order->delivery_charge, 0) }}</span>
                </div>
                <div class="summary-item">
                    <label>Total</label>
                    <span>Rs. {{ number_format($order->total_amount, 0) }}</span>
                </div>
            </div>
            <div class="order-actions">
                <a href="{{ route('orders.show', $order) }}" class="btn-view">
                    <i class="fas fa-eye"></i> View Details
                </a>
            </div>
        </div>
    @endforeach

    @if($orders->hasPages())
        <div style="margin-top: 20px;">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
@endif
