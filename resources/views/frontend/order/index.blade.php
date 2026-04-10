@extends('frontend.layouts.main')

@section('main-content')
<style>
    .orders-page {
        padding: 30px 0 50px;
        background: #f9faf9;
        min-height: 80vh;
    }
    .orders-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .orders-header {
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: white;
        margin-bottom: 24px;
        box-shadow: 0 8px 25px rgba(46, 125, 50, 0.15);
    }
    .orders-header h1 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
    }
    .orders-header p {
        margin: 6px 0 0;
        opacity: 0.9;
        font-size: 1rem;
    }
    .order-card {
        background: white;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        border: 1px solid #e8f0e8;
        transition: all 0.25s ease;
    }
    .order-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding-bottom: 14px;
        border-bottom: 1px solid #e8f0e8;
        margin-bottom: 14px;
    }
    .order-number {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
    }
    .order-date {
        font-size: 0.9rem;
        color: #6b7280;
    }
    .order-status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-processing { background: #dbeafe; color: #1e40af; }
    .status-shipped { background: #e0e7ff; color: #3730a3; }
    .status-delivered { background: #d1fae5; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
    .payment-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .payment-pending { background: #fef3c7; color: #92400e; }
    .payment-verified { background: #d1fae5; color: #065f46; }
    .payment-failed { background: #fee2e2; color: #991b1b; }
    .payment-cod { background: #e0e7ff; color: #3730a3; }
    .order-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
        margin-bottom: 14px;
    }
    .summary-item label {
        display: block;
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 4px;
    }
    .summary-item span {
        font-size: 1.05rem;
        font-weight: 600;
        color: #1f2937;
    }
    .order-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .btn-view {
        padding: 8px 18px;
        background: #2e7d32;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-view:hover {
        background: #1b5e20;
        color: white;
    }
    .empty-orders {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-orders i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 16px;
    }
    .empty-orders h3 {
        color: #374151;
        margin-bottom: 8px;
    }
    .empty-orders p {
        color: #6b7280;
    }
    .empty-orders a {
        display: inline-block;
        margin-top: 16px;
        padding: 12px 24px;
        background: #2e7d32;
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
    }
    @media (max-width: 640px) {
        .order-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="orders-page">
    <div class="orders-container">
        <div class="orders-header">
            <h1><i class="fas fa-box-open"></i> My Orders</h1>
            <p>Track and manage your order history</p>
        </div>

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
                            <span class="payment-badge payment-{{ $order->payment_status }}">
                                {{ $order->payment_status === 'cod' ? 'COD' : ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                    <div class="order-summary">
                        <div class="summary-item">
                            <label>Items</label>
                            <span>{{ $order->items->count() }}</span>
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

            <div style="margin-top: 20px;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection