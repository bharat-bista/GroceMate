@extends('frontend.layouts.main')

@section('main-content')
<style>
    .order-detail-page {
        padding: 30px 0 50px;
        background: #f9faf9;
        min-height: 80vh;
    }
    .order-detail-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .order-detail-header {
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: white;
        margin-bottom: 24px;
        box-shadow: 0 8px 25px rgba(46, 125, 50, 0.15);
    }
    .order-detail-header h1 {
        margin: 0;
        font-size: 1.6rem;
        font-weight: 700;
    }
    .order-detail-header p {
        margin: 6px 0 0;
        opacity: 0.9;
        font-size: 1rem;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: white;
        text-decoration: none;
        font-size: 0.95rem;
        margin-bottom: 16px;
        opacity: 0.9;
    }
    .back-link:hover {
        opacity: 1;
    }
    .detail-card {
        background: white;
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        border: 1px solid #e8f0e8;
    }
    .detail-card h3 {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e8f0e8;
    }
    .detail-card h3 i {
        color: #2e7d32;
        margin-right: 8px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    .info-item label {
        display: block;
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 4px;
    }
    .info-item span {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
    }
    .status-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-processing { background: #dbeafe; color: #1e40af; }
    .status-shipped { background: #e0e7ff; color: #3730a3; }
    .status-delivered { background: #d1fae5; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
    .payment-pending { background: #fef3c7; color: #92400e; }
    .payment-verified { background: #d1fae5; color: #065f46; }
    .payment-failed { background: #fee2e2; color: #991b1b; }
    .payment-cod { background: #e0e7ff; color: #3730a3; }
    .order-items-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .order-item {
        display: grid;
        grid-template-columns: 70px 1fr auto;
        gap: 16px;
        align-items: center;
        padding: 12px;
        background: #fafdfa;
        border-radius: 10px;
        border: 1px solid #e8f0e8;
    }
    .order-item img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e8f0e8;
    }
    .order-item-info h4 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
    }
    .order-item-info p {
        margin: 4px 0 0;
        font-size: 0.9rem;
        color: #6b7280;
    }
    .order-item-price {
        text-align: right;
    }
    .order-item-price .price {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
    }
    .order-item-price .qty {
        font-size: 0.85rem;
        color: #6b7280;
    }
    .order-total-section {
        display: flex;
        justify-content: flex-end;
    }
    .total-table {
        width: 280px;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 1rem;
    }
    .total-row:not(:last-child) {
        border-bottom: 1px solid #e8f0e8;
    }
    .total-row span:first-child {
        color: #6b7280;
    }
    .total-row span:last-child {
        font-weight: 600;
        color: #1f2937;
    }
    .total-row.final span {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2e7d32;
    }
    .payment-slip-section {
        margin-top: 16px;
    }
    .payment-slip-section img {
        max-width: 200px;
        border-radius: 8px;
        border: 2px solid #e8f0e8;
    }
</style>

<div class="order-detail-page">
    <div class="order-detail-container">
        <a href="{{ route('orders') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>

        <div class="order-detail-header">
            <h1>Order {{ $order->order_number }}</h1>
            <p>Placed on {{ $order->created_at->format('M d, Y at h:i A') }}</p>
        </div>

        <!-- Order Status -->
        <div class="detail-card">
            <h3><i class="fas fa-info-circle"></i> Order Status</h3>
            <div class="status-row">
                <span class="status-badge status-{{ $order->delivery_status }}">
                    <i class="fas fa-truck"></i> {{ ucfirst($order->delivery_status) }}
                </span>
                <span class="status-badge payment-{{ $order->payment_status }}">
                    <i class="fas fa-credit-card"></i> {{ $order->payment_status === 'cod' ? 'Cash on Delivery' : ucfirst($order->payment_status) }}
                </span>
            </div>
        </div>

        <!-- Delivery Details -->
        <div class="detail-card">
            <h3><i class="fas fa-map-marker-alt"></i> Delivery Details</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Customer Name</label>
                    <span>{{ $order->customer_name }}</span>
                </div>
                <div class="info-item">
                    <label>Phone</label>
                    <span>{{ $order->customer_phone }}</span>
                </div>
                @if($order->customer_email)
                <div class="info-item">
                    <label>Email</label>
                    <span>{{ $order->customer_email }}</span>
                </div>
                @endif
                <div class="info-item">
                    <label>Delivery Type</label>
                    <span>{{ ucfirst($order->delivery_type) }}</span>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <label>Delivery Address</label>
                    <span>{{ $order->delivery_address }}</span>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="detail-card">
            <h3><i class="fas fa-box"></i> Order Items</h3>
            <div class="order-items-list">
                @foreach($order->items as $item)
                    <div class="order-item">
                        <img src="{{ $item->image ?? asset('assets/img/product/product1.jpg') }}" alt="{{ $item->product_name }}">
                        <div class="order-item-info">
                            <h4>{{ $item->product_name }}</h4>
                            <p>Unit Price: Rs. {{ number_format($item->price, 0) }}</p>
                        </div>
                        <div class="order-item-price">
                            <div class="price">Rs. {{ number_format($item->subtotal, 0) }}</div>
                            <div class="qty">Qty: {{ $item->quantity }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="order-total-section">
                <div class="total-table">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span>Rs. {{ number_format($order->subtotal, 0) }}</span>
                    </div>
                    <div class="total-row">
                        <span>Delivery Charge</span>
                        <span>Rs. {{ number_format($order->delivery_charge, 0) }}</span>
                    </div>
                    <div class="total-row final">
                        <span>Total</span>
                        <span>Rs. {{ number_format($order->total_amount, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="detail-card">
            <h3><i class="fas fa-credit-card"></i> Payment Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Payment Method</label>
                    <span>
                        @if($order->payment_method === 'esewa')
                            <i class="fas fa-mobile-alt"></i> eSewa
                        @elseif($order->payment_method === 'connectips')
                            <i class="fas fa-university"></i> Connect IPS
                        @else
                            <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <label>Payment Status</label>
                    <span class="status-badge payment-{{ $order->payment_status }}">
                        {{ $order->payment_status === 'cod' ? 'Cash on Delivery' : ucfirst($order->payment_status) }}
                    </span>
                </div>
                @if($order->transaction_id)
                <div class="info-item">
                    <label>Transaction ID</label>
                    <span>{{ $order->transaction_id }}</span>
                </div>
                @endif
                @if($order->payment_slip)
                <div class="info-item payment-slip-section">
                    <label>Payment Slip</label>
                    @if($order->payment_slip_is_pdf)
                        <a href="{{ $order->payment_slip_url }}" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none;">
                            <i class="fas fa-file-pdf" style="font-size: 22px; color: #dc2626;"></i>
                            <span>View Payment Slip (PDF)</span>
                        </a>
                    @else
                        <img src="{{ $order->payment_slip_url }}" alt="Payment Slip">
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
