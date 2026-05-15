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
    .orders-results.is-loading {
        opacity: 0.65;
        pointer-events: none;
        transition: opacity 0.2s ease;
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

        <div id="orders-results" class="orders-results">
            @include('frontend.order.partials.list', ['orders' => $orders])
        </div>
    </div>
</div>

<script>
const orderConfirmation = @json(session('order_confirmation'));
const orderShowUrlTemplate = "{{ route('orders.show', ':id') }}";
const continueShoppingUrl = "{{ route('home') }}";

function formatCurrency(value) {
    const amount = Number(value || 0);
    const hasFraction = Math.abs(amount - Math.trunc(amount)) > 0.000001;

    return 'Rs. ' + amount.toLocaleString('en-US', {
        minimumFractionDigits: hasFraction ? 2 : 0,
        maximumFractionDigits: 2
    });
}

function getDeliveryLabel(value) {
    const map = {
        inside: 'Inside Valley',
        outside: 'Outside Valley',
        pickup: 'Store Pickup'
    };
    return map[value] || 'Standard Delivery';
}

function getPaymentMethodLabel(value) {
    if (value === 'cod') return 'Cash on Delivery';
    if (value === 'connectips') return 'Connect IPS';
    if (value === 'esewa') return 'eSewa';
    return value ? String(value) : 'Payment';
}

function showOrderConfirmation(details) {
    const orderUrl = orderShowUrlTemplate.replace(':id', details.orderId);
    const deliveryLabel = getDeliveryLabel(details.deliveryType);
    const paymentLabel = getPaymentMethodLabel(details.paymentMethod);
    const deliveryChargeLabel = formatCurrency(details.deliveryCharge);
    const totalLabel = formatCurrency(details.totalAmount);

    const summaryHtml = `
        <div style="text-align: left; font-size: 0.95rem; line-height: 1.55;">
            <div><strong>Order #:</strong> ${details.orderNumber}</div>
            <div><strong>Payment:</strong> ${paymentLabel}</div>
            <div><strong>Delivery:</strong> ${deliveryLabel}</div>
            <div><strong>Delivery Charge:</strong> ${deliveryChargeLabel}</div>
            <div><strong>Total:</strong> ${totalLabel}</div>
        </div>
    `;

    if (window.Swal && typeof window.Swal.fire === 'function') {
        return window.Swal.fire({
            icon: 'success',
            title: 'Order confirmed!',
            html: summaryHtml,
            showCancelButton: true,
            confirmButtonText: 'View Order',
            cancelButtonText: 'Continue Shopping',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = orderUrl;
                return;
            }

            window.location.href = continueShoppingUrl;
        });
    }

    alert(
        `Order confirmed!\nOrder #: ${details.orderNumber}\nPayment: ${paymentLabel}\nDelivery: ${deliveryLabel}\nDelivery Charge: ${deliveryChargeLabel}\nTotal: ${totalLabel}`
    );
    window.location.href = orderUrl;
}

if (orderConfirmation && orderConfirmation.order_id) {
    showOrderConfirmation({
        orderId: orderConfirmation.order_id,
        orderNumber: orderConfirmation.order_number,
        deliveryType: orderConfirmation.delivery_type || 'inside',
        deliveryCharge: Number(orderConfirmation.delivery_charge || 0),
        totalAmount: Number(orderConfirmation.total_amount || 0),
        paymentMethod: orderConfirmation.payment_method || 'esewa',
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const results = document.getElementById('orders-results');

    if (!results) {
        return;
    }

    let activeRequest = null;

    async function fetchOrders(url, shouldPushState) {
        if (activeRequest) {
            activeRequest.abort();
        }

        activeRequest = new AbortController();
        results.classList.add('is-loading');

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: activeRequest.signal,
            });

            if (!response.ok) {
                throw new Error('Unable to load orders.');
            }

            const payload = await response.json();
            results.innerHTML = payload.html || '';

            if (shouldPushState) {
                window.history.pushState({}, '', url);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.href = url;
            }
        } finally {
            results.classList.remove('is-loading');
        }
    }

    results.addEventListener('click', function (event) {
        const pageLink = event.target.closest('.pagination a');
        if (!pageLink) {
            return;
        }

        event.preventDefault();
        fetchOrders(pageLink.href, true);
    });

    window.addEventListener('popstate', function () {
        fetchOrders(window.location.href, false);
    });
});
</script>
@endsection
