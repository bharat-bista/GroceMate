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
    .cancel-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
    }
    .cancel-pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .cancel-rejected {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    .btn-cancel-request {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: #fff5f5;
        color: #dc2626;
        border: 1px solid #fca5a5;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-cancel-request:hover {
        background: #fee2e2;
    }
    .cancel-expired-text {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.85rem;
        color: #9ca3af;
        padding: 7px 0;
    }
    .cancel-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .cancel-modal-overlay.open {
        display: flex;
    }
    .cancel-modal-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        max-width: 480px;
        width: 100%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .cancel-modal-card h3 {
        margin: 0 0 6px;
        font-size: 1.15rem;
        font-weight: 700;
        color: #1f2937;
    }
    .cancel-modal-card p {
        margin: 0 0 16px;
        font-size: 0.9rem;
        color: #6b7280;
    }
    .cancel-modal-card textarea {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.95rem;
        resize: vertical;
        min-height: 100px;
        box-sizing: border-box;
        font-family: inherit;
        margin-bottom: 4px;
    }
    .cancel-modal-card textarea:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
    .cancel-modal-error {
        font-size: 0.85rem;
        color: #dc2626;
        margin-bottom: 12px;
        display: none;
    }
    .cancel-modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }
    .cancel-modal-actions .btn-cancel-confirm {
        flex: 1;
        padding: 10px;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .cancel-modal-actions .btn-cancel-confirm:hover {
        background: #b91c1c;
    }
    .cancel-modal-actions .btn-cancel-dismiss {
        flex: 1;
        padding: 10px;
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .cancel-modal-actions .btn-cancel-dismiss:hover {
        background: #e5e7eb;
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

{{-- Cancellation Request Modal --}}
<div class="cancel-modal-overlay" id="cancelModal">
    <div class="cancel-modal-card">
        <h3><i class="fas fa-times-circle" style="color:#dc2626;margin-right:6px;"></i> Request Cancellation</h3>
        <p id="cancelModalSubtitle">Please provide a reason for your cancellation request.</p>
        <textarea id="cancelReasonInput" placeholder="Reason for cancellation..."></textarea>
        <div class="cancel-modal-error" id="cancelModalError">Please provide a reason.</div>
        <div id="cancelModalFeedback" style="display:none;font-size:0.9rem;margin-bottom:8px;"></div>
        <div class="cancel-modal-actions">
            <button class="btn-cancel-confirm" id="cancelConfirmBtn" onclick="submitCancelRequest()">
                <i class="fas fa-check"></i> Confirm Request
            </button>
            <button class="btn-cancel-dismiss" onclick="closeCancelModal()">Close</button>
        </div>
    </div>
</div>

<script>
const cancelRequestUrlTemplate = "{{ route('orders.cancel-request', ':id') }}";
const csrfToken = "{{ csrf_token() }}";
let activeCancelOrderId = null;

function openCancelModal(orderId, orderNumber) {
    activeCancelOrderId = orderId;
    document.getElementById('cancelModalSubtitle').textContent = 'Request cancellation for Order #' + orderNumber;
    document.getElementById('cancelReasonInput').value = '';
    document.getElementById('cancelModalError').style.display = 'none';
    document.getElementById('cancelModalFeedback').style.display = 'none';
    document.getElementById('cancelConfirmBtn').disabled = false;
    document.getElementById('cancelConfirmBtn').innerHTML = '<i class="fas fa-check"></i> Confirm Request';
    document.getElementById('cancelModal').classList.add('open');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.remove('open');
    activeCancelOrderId = null;
}

function submitCancelRequest() {
    const reason = document.getElementById('cancelReasonInput').value.trim();
    const errorEl = document.getElementById('cancelModalError');
    const feedbackEl = document.getElementById('cancelModalFeedback');
    const confirmBtn = document.getElementById('cancelConfirmBtn');

    if (!reason) {
        errorEl.style.display = 'block';
        return;
    }
    errorEl.style.display = 'none';

    const url = cancelRequestUrlTemplate.replace(':id', activeCancelOrderId);
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ reason }),
    })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                feedbackEl.style.color = '#065f46';
                feedbackEl.style.background = '#d1fae5';
                feedbackEl.style.padding = '8px 12px';
                feedbackEl.style.borderRadius = '8px';
                feedbackEl.textContent = data.message;
                feedbackEl.style.display = 'block';
                confirmBtn.style.display = 'none';
                setTimeout(function () {
                    closeCancelModal();
                    window.location.reload();
                }, 1500);
            } else {
                feedbackEl.style.color = '#991b1b';
                feedbackEl.style.background = '#fee2e2';
                feedbackEl.style.padding = '8px 12px';
                feedbackEl.style.borderRadius = '8px';
                feedbackEl.textContent = data.message || 'Unable to submit request.';
                feedbackEl.style.display = 'block';
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm Request';
            }
        })
        .catch(function () {
            feedbackEl.style.color = '#991b1b';
            feedbackEl.style.background = '#fee2e2';
            feedbackEl.style.padding = '8px 12px';
            feedbackEl.style.borderRadius = '8px';
            feedbackEl.textContent = 'Something went wrong. Please try again.';
            feedbackEl.style.display = 'block';
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm Request';
        });
}

document.getElementById('cancelModal')?.addEventListener('click', function (e) {
    if (e.target === this) closeCancelModal();
});

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
