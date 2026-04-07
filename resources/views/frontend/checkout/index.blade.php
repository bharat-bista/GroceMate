@extends('frontend.layouts.main')

@section('main-content')
@php
  $authUser = auth()->user();
  $defaultName = old('full_name', $authUser->name ?? '');
  $defaultPhone = old('phone', $authUser->phone ?? '');
  $defaultAddress = old('address', $authUser->address ?? '');
  $isBuyNowPage = request()->query('mode') === 'buy-now';
@endphp

<style>
  .checkout-page {
    --gm-primary: #2e7d32;
    --gm-primary-dark: #1b5e20;
    --gm-primary-soft: #e9f5ea;
    --gm-accent: #ff6b35;
    --gm-text: #1f2937;
    --gm-muted: #6b7280;
    --gm-border: #dce8dd;
    --gm-white: #ffffff;
    --gm-surface: #f5faf6;
    --gm-shadow: 0 14px 35px rgba(46, 125, 50, 0.12);
    font-size: 17px;
    padding: 28px 0 46px;
    background: radial-gradient(circle at top right, #e4f3e6 0%, #f9fcf9 48%, #f3f9f4 100%);
  }

  .checkout-container {
    max-width: 1220px;
    padding: 0 18px;
  }

  .checkout-hero {
    background: linear-gradient(130deg, rgba(46, 125, 50, 0.96), rgba(27, 94, 32, 0.95));
    border-radius: 22px;
    color: #fff;
    padding: 22px 24px;
    margin-bottom: 22px;
    box-shadow: var(--gm-shadow);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
  }

  .checkout-hero h2 {
    margin: 0;
    font-size: clamp(1.45rem, 2.4vw, 1.95rem);
    font-weight: 700;
  }

  .checkout-hero p {
    margin: 6px 0 0;
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.88);
  }

  .checkout-mode-badge {
    background: rgba(255, 255, 255, 0.16);
    border: 1px solid rgba(255, 255, 255, 0.45);
    color: #fff;
    border-radius: 999px;
    padding: 8px 14px;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    white-space: nowrap;
  }

  .checkout-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
    gap: 22px;
    align-items: start;
  }

  .checkout-card {
    background: var(--gm-white);
    border: 1px solid var(--gm-border);
    border-radius: 18px;
    padding: 22px;
    box-shadow: 0 8px 28px rgba(28, 76, 45, 0.08);
  }

  .checkout-card h4 {
    margin-bottom: 18px;
    font-size: 1.45rem;
    font-weight: 700;
    color: var(--gm-text);
  }

  .checkout-card h4 i {
    color: var(--gm-primary);
    margin-right: 8px;
  }

  .checkout-card label {
    color: var(--gm-text);
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 7px;
  }

  .checkout-card .form-control {
    border: 1px solid var(--gm-border);
    border-radius: 12px;
    min-height: 46px;
    font-size: 1.12rem;
    padding: 10px 13px;
  }

  .checkout-card .form-control::placeholder {
    font-size: 1.04rem !important;
    opacity: 1;
  }

  .checkout-card .form-control::-webkit-input-placeholder {
    font-size: 1.04rem !important;
  }

  .checkout-card .form-control::-moz-placeholder {
    font-size: 1.04rem !important;
    opacity: 1;
  }

  .checkout-card .form-control:-ms-input-placeholder {
    font-size: 1.04rem !important;
  }

  .checkout-card .form-control::-ms-input-placeholder {
    font-size: 1.04rem !important;
  }

  .checkout-card .form-control:focus {
    border-color: var(--gm-primary);
    box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.15);
  }

  .delivery-options {
    display: grid;
    gap: 10px;
  }

  .delivery-option {
    display: flex;
    align-items: center;
    gap: 11px;
    border: 1px solid var(--gm-border);
    border-radius: 12px;
    padding: 11px 12px;
    background: #fbfefb;
    transition: 0.2s ease;
  }

  .delivery-option:hover {
    border-color: #b8d4bb;
    background: var(--gm-primary-soft);
  }

  .delivery-option .form-check-input {
    margin-top: 0;
    border-color: #b0cbb3;
  }

  .delivery-option .form-check-input:checked {
    background-color: var(--gm-primary);
    border-color: var(--gm-primary);
  }

  .delivery-option-text {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    gap: 10px;
    font-size: 1.08rem;
    color: var(--gm-muted);
  }

  .delivery-option-text strong {
    color: var(--gm-text);
    font-size: 1.18rem;
  }

  .checkout-map-wrap {
    height: 220px;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid var(--gm-border);
    background: #eef6ef;
    margin-top: 6px;
  }

  .checkout-map-wrap iframe {
    width: 100%;
    height: 100%;
    border: 0;
  }

  .checkout-side-stack {
    display: grid;
    gap: 18px;
  }

  .checkout-container .payment-methods {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
  }

  .checkout-container .payment-box {
    border: 1px solid var(--gm-border);
    border-radius: 14px;
    padding: 12px;
    background: #fafdfa;
    cursor: pointer;
    transition: all 0.25s ease;
    color: var(--gm-text);
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 62px;
  }

  .checkout-container .payment-box:hover {
    border-color: var(--gm-primary);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(46, 125, 50, 0.12);
  }

  .checkout-container .payment-box.selected {
    background: linear-gradient(135deg, var(--gm-primary) 0%, var(--gm-primary-dark) 100%);
    color: #fff;
    border-color: transparent;
  }

  .payment-box-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: rgba(46, 125, 50, 0.14);
    color: var(--gm-primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.08rem;
  }

  .payment-box.selected .payment-box-icon {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
  }

  .payment-box-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
  }

  .payment-box-text strong {
    font-size: 1.14rem;
    font-weight: 700;
  }

  .payment-box-text small {
    color: var(--gm-muted);
    font-size: 0.98rem;
  }

  .payment-box.selected .payment-box-text small {
    color: rgba(255, 255, 255, 0.86);
  }

  .checkout-container .fonepay-info {
    margin-top: 14px;
    border: 1px dashed #bfd8c1;
    background: var(--gm-surface);
    border-radius: 14px;
    padding: 14px;
    display: none;
    color: var(--gm-text);
  }

  .payment-note-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.08rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--gm-primary-dark);
  }

  .payment-note-title i {
    color: var(--gm-primary);
  }

  .checkout-container .fonepay-info p {
    margin: 0;
    font-size: 1.02rem;
    color: #4b5563;
    line-height: 1.5;
  }

  .checkout-container .form-check-label {
    font-size: 1.08rem;
    margin-bottom: 0;
  }

  .checkout-container .place-order-btn {
    width: 100%;
    border: none;
    border-radius: 13px;
    min-height: 58px;
    margin-top: 18px;
    background: linear-gradient(135deg, var(--gm-primary) 0%, var(--gm-primary-dark) 100%);
    color: #fff;
    font-size: 1.18rem;
    font-weight: 700;
    transition: all 0.25s ease;
  }

  .checkout-container .place-order-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 26px rgba(46, 125, 50, 0.25);
  }

  .checkout-container .place-order-btn:disabled {
    opacity: 0.72;
    cursor: not-allowed;
    background: #89a88e;
    box-shadow: none;
  }

  .checkout-order-items {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 14px;
    max-height: 290px;
    overflow-y: auto;
    padding-right: 4px;
  }

  .checkout-order-item {
    display: grid;
    grid-template-columns: 58px minmax(0, 1fr);
    gap: 10px;
    border: 1px solid var(--gm-border);
    border-radius: 12px;
    padding: 8px;
    background: #fbfefb;
  }

  .checkout-order-item-img {
    width: 58px;
    height: 58px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid var(--gm-border);
    background: #fff;
  }

  .checkout-order-item-title {
    margin: 0;
    font-size: 1.08rem;
    font-weight: 700;
    color: var(--gm-text);
    line-height: 1.35;
  }

  .checkout-order-item-meta {
    font-size: 0.99rem;
    color: var(--gm-muted);
    margin-top: 2px;
  }

  .checkout-summary-line {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
    font-size: 1.12rem;
  }

  .checkout-summary-line span:first-child {
    color: #4b5563;
  }

  .checkout-summary-line strong {
    color: #111827;
  }

  .checkout-summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-top: 13px;
    padding-top: 13px;
    border-top: 1px dashed #c9dacb;
  }

  .checkout-summary-total span {
    font-weight: 700;
    color: var(--gm-text);
    font-size: 1.16rem;
  }

  .checkout-summary-total strong {
    font-size: 1.62rem;
    color: var(--gm-accent);
  }

  .checkout-empty-order {
    margin-bottom: 12px;
    border-radius: 10px;
    border: 1px solid #ffe6b6;
    background: #fff9ec;
    color: #8a6118;
    font-size: 1.06rem;
  }

  @media (max-width: 1199px) {
    .checkout-layout {
      grid-template-columns: minmax(0, 1fr);
    }

    .checkout-side-stack {
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
    }
  }

  @media (max-width: 991px) {
    .checkout-page {
      font-size: 16px;
      padding-top: 20px;
    }

    .checkout-hero {
      border-radius: 16px;
      padding: 18px 16px;
      margin-bottom: 16px;
    }

    .checkout-card {
      padding: 16px;
      border-radius: 15px;
    }

    .checkout-side-stack {
      grid-template-columns: 1fr;
    }

    .checkout-container .payment-methods {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 575px) {
    .checkout-container {
      padding: 0 12px;
    }

    .checkout-mode-badge {
      width: 100%;
      text-align: center;
    }

    .delivery-option {
      padding: 10px;
    }

    .delivery-option-text {
      font-size: 1rem;
    }

    .checkout-card h4 {
      font-size: 1.25rem;
    }

    .checkout-card label,
    .checkout-card .form-control,
    .checkout-summary-line {
      font-size: 1rem;
    }

    .checkout-order-items {
      max-height: none;
    }

    .checkout-map-wrap {
      height: 185px;
    }
  }
</style>

<div class="checkout-page">
  <div class="container checkout-container">
    <div class="checkout-hero">
      <div>
        <h2><i class="fas fa-lock"></i> Secure Checkout</h2>
        <p>Review delivery details, select payment, and confirm your order in one step.</p>
      </div>
      <div class="checkout-mode-badge">
        {{ $isBuyNowPage ? 'Direct Buy Checkout' : 'Cart Checkout' }}
      </div>
    </div>

    <div class="checkout-layout">
      <div class="checkout-card">
        <h4><i class="fas fa-map-marker-alt"></i> Billing & Shipping Address</h4>
        <form id="checkout-address-form" novalidate>
          <div class="form-group mb-3">
            <label for="checkout-full-name">Full Name *</label>
            <input
              type="text"
              class="form-control"
              id="checkout-full-name"
              name="full_name"
              value="{{ $defaultName }}"
              placeholder="Enter your full name"
              required
            >
          </div>

          <div class="form-group mb-3">
            <label for="checkout-phone">Phone *</label>
            <input
              type="text"
              id="checkout-phone"
              name="phone"
              class="form-control"
              value="{{ $defaultPhone }}"
              placeholder="Enter your phone number"
              required
            >
          </div>

          <div class="form-group mb-3">
            <label for="checkout-address">Address / Landmark *</label>
            <input
              type="text"
              id="checkout-address"
              name="address"
              class="form-control"
              value="{{ $defaultAddress }}"
              placeholder="Enter complete address or landmark"
              required
            >
          </div>

          <div class="form-group mb-3">
            <label>Delivery Type *</label>
            <div class="delivery-options">
              <label class="delivery-option">
                <input class="form-check-input" type="radio" name="delivery" value="inside" data-charge="100" checked>
                <span class="delivery-option-text">
                  <strong>Inside Valley</strong>
                  <span>Rs. 100</span>
                </span>
              </label>

              <label class="delivery-option">
                <input class="form-check-input" type="radio" name="delivery" value="outside" data-charge="200">
                <span class="delivery-option-text">
                  <strong>Outside Valley</strong>
                  <span>Rs. 200</span>
                </span>
              </label>

              <label class="delivery-option">
                <input class="form-check-input" type="radio" name="delivery" value="pickup" data-charge="0">
                <span class="delivery-option-text">
                  <strong>Store Pickup</strong>
                  <span>Free</span>
                </span>
              </label>
            </div>
          </div>

          <div class="form-group">
            <label>Location Preview</label>
            <div class="checkout-map-wrap">
              <iframe
                src="https://maps.google.com/maps?q=Kathmandu&t=&z=13&ie=UTF8&iwloc=&output=embed"
                loading="lazy"
              ></iframe>
            </div>
          </div>
        </form>
      </div>

      <div class="checkout-side-stack">
        <div class="payment-section checkout-card">
          <h4><i class="fas fa-credit-card"></i> Choose Payment Option</h4>

          <div class="payment-methods">
            <div class="payment-box" data-method="fonepay">
              <span class="payment-box-icon"><i class="fas fa-qrcode"></i></span>
              <span class="payment-box-text">
                <strong>Fonepay</strong>
                <small>QR payment</small>
              </span>
            </div>

            <div class="payment-box" data-method="connectips">
              <span class="payment-box-icon"><i class="fas fa-building-columns"></i></span>
              <span class="payment-box-text">
                <strong>Connect IPS</strong>
                <small>Bank transfer</small>
              </span>
            </div>

            <div class="payment-box" data-method="cod">
              <span class="payment-box-icon"><i class="fas fa-money-bill-wave"></i></span>
              <span class="payment-box-text">
                <strong>Cash on Delivery</strong>
                <small>Pay at doorstep</small>
              </span>
            </div>
          </div>

          <div class="fonepay-info" id="fonepayInfo">
            <div class="payment-note-title"><i class="fas fa-info-circle"></i> Fonepay Instructions</div>
            <p>
              Add your <b>Name</b> and <b>Phone Number</b> in remarks while paying.
              Then share payment screenshot on WhatsApp: <b>+977 9849433139</b>
            </p>
          </div>

          <div class="fonepay-info" id="connectInfo">
            <div class="payment-note-title"><i class="fas fa-info-circle"></i> Connect IPS Instructions</div>
            <p>
              Add your <b>Name</b> and <b>Phone Number</b> in remarks while paying.
              Then share payment screenshot on WhatsApp: <b>+977 9849433139</b>
            </p>
          </div>

          <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" id="termsCheck">
            <label class="form-check-label" for="termsCheck">I agree to the terms and conditions *</label>
          </div>

          <button type="button" class="place-order-btn" id="place-order-btn">Place Order</button>
        </div>

        <div class="order-summary checkout-card">
          <h4><i class="fas fa-receipt"></i> Your Order</h4>

          <div id="checkout-empty-order" class="alert checkout-empty-order d-none">
            Your cart is empty. Please add items before checkout.
          </div>

          <div id="checkout-order-items" class="checkout-order-items"></div>

          <div class="checkout-summary-line">
            <span>Items:</span>
            <strong id="checkout-item-count">0</strong>
          </div>

          <div class="checkout-summary-line">
            <span>Subtotal:</span>
            <strong id="checkout-subtotal">Rs. 0.00</strong>
          </div>

          <div class="checkout-summary-line">
            <span>Delivery Charge:</span>
            <strong id="checkout-delivery">Rs. 0.00</strong>
          </div>

          <div class="checkout-summary-total">
            <span>Total:</span>
            <strong id="checkout-total">Rs. 0.00</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const CART_KEY = 'gm_cart_items';
  const BUY_NOW_KEY = 'gm_buy_now_item';
  const LEGACY_BUY_NOW_KEY = 'buynow';
  const CHECKOUT_DRAFT_KEY = 'gm_checkout_draft';
  const isBuyNowMode = new URLSearchParams(window.location.search).get('mode') === 'buy-now';

  const paymentBoxes = document.querySelectorAll('.payment-box');
  const fonepayInfo = document.getElementById('fonepayInfo');
  const connectInfo = document.getElementById('connectInfo');
  const placeOrderBtn = document.getElementById('place-order-btn');
  const orderItemsWrap = document.getElementById('checkout-order-items');
  const emptyOrderEl = document.getElementById('checkout-empty-order');
  const subtotalEl = document.getElementById('checkout-subtotal');
  const deliveryEl = document.getElementById('checkout-delivery');
  const totalEl = document.getElementById('checkout-total');
  const itemCountEl = document.getElementById('checkout-item-count');
  const deliveryInputs = document.querySelectorAll('input[name="delivery"]');
  const nameInput = document.getElementById('checkout-full-name');
  const phoneInput = document.getElementById('checkout-phone');
  const addressInput = document.getElementById('checkout-address');
  const fallbackProductImage = @json(asset('assets/img/product/product1.jpg'));

  let selectedPaymentMethod = null;

  function formatCurrency(value) {
    return 'Rs. ' + Number(value || 0).toLocaleString('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function readCartItems() {
    if (window.GroceMateCart && typeof window.GroceMateCart.getItems === 'function') {
      return window.GroceMateCart.getItems();
    }

    try {
      const parsed = JSON.parse(localStorage.getItem(CART_KEY) || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (_) {
      return [];
    }
  }

  function normalizeCheckoutItem(item) {
    return {
      id: String(item?.id ?? '').trim(),
      name: String(item?.name || 'Product'),
      price: Number(item?.price || 0),
      image: String(item?.image || ''),
      qty: Math.max(1, Number(item?.qty || 1)),
    };
  }

  function readBuyNowItem() {
    if (window.GroceMateCart && typeof window.GroceMateCart.getBuyNowItem === 'function') {
      return window.GroceMateCart.getBuyNowItem();
    }

    try {
      const directItem = JSON.parse(localStorage.getItem(BUY_NOW_KEY) || 'null');
      const legacyItem = JSON.parse(localStorage.getItem(LEGACY_BUY_NOW_KEY) || 'null');
      const selected = directItem?.id ? directItem : legacyItem;
      if (!selected || !selected.id) {
        return null;
      }
      return normalizeCheckoutItem(selected);
    } catch (_) {
      return null;
    }
  }

  function getCheckoutItems() {
    if (!isBuyNowMode) {
      return readCartItems();
    }

    const buyNowItem = readBuyNowItem();
    return buyNowItem ? [buyNowItem] : [];
  }

  function clearBuyNowItem() {
    if (window.GroceMateCart && typeof window.GroceMateCart.clearBuyNowItem === 'function') {
      window.GroceMateCart.clearBuyNowItem();
      return;
    }

    localStorage.removeItem(BUY_NOW_KEY);
    localStorage.removeItem(LEGACY_BUY_NOW_KEY);
  }

  function getSelectedDeliveryCharge() {
    const selectedDelivery = document.querySelector('input[name="delivery"]:checked');
    return Number(selectedDelivery?.dataset?.charge || 0);
  }

  function saveCheckoutDraft() {
    const selectedDelivery = document.querySelector('input[name="delivery"]:checked');

    const draft = {
      full_name: nameInput?.value?.trim() || '',
      phone: phoneInput?.value?.trim() || '',
      address: addressInput?.value?.trim() || '',
      delivery: selectedDelivery?.value || 'inside',
    };

    localStorage.setItem(CHECKOUT_DRAFT_KEY, JSON.stringify(draft));
  }

  function restoreCheckoutDraft() {
    try {
      const draft = JSON.parse(localStorage.getItem(CHECKOUT_DRAFT_KEY) || '{}');

      if (draft && typeof draft === 'object') {
        if (nameInput && !nameInput.value && draft.full_name) nameInput.value = draft.full_name;
        if (phoneInput && !phoneInput.value && draft.phone) phoneInput.value = draft.phone;
        if (addressInput && !addressInput.value && draft.address) addressInput.value = draft.address;

        if (draft.delivery) {
          const target = document.querySelector(`input[name="delivery"][value="${draft.delivery}"]`);
          if (target) target.checked = true;
        }
      }
    } catch (_) {
      // Ignore invalid draft data
    }
  }

  function renderOrderSummary() {
    const checkoutItems = getCheckoutItems();
    const hasItems = checkoutItems.length > 0;

    const subtotal = checkoutItems.reduce((sum, item) => {
      const price = Number(item?.price || 0);
      const qty = Math.max(1, Number(item?.qty || 1));
      return sum + (price * qty);
    }, 0);

    const deliveryCharge = getSelectedDeliveryCharge();
    const total = subtotal + deliveryCharge;

    itemCountEl.textContent = String(checkoutItems.length);
    subtotalEl.textContent = formatCurrency(subtotal);
    deliveryEl.textContent = formatCurrency(deliveryCharge);
    totalEl.textContent = formatCurrency(total);

    if (!hasItems) {
      emptyOrderEl.classList.remove('d-none');
      emptyOrderEl.textContent = isBuyNowMode
        ? 'No product selected for direct checkout. Please click Buy Now on a product.'
        : 'Your cart is empty. Please add items before checkout.';
      orderItemsWrap.innerHTML = '';
      placeOrderBtn.disabled = true;
      return;
    }

    emptyOrderEl.classList.add('d-none');
    placeOrderBtn.disabled = false;

    orderItemsWrap.innerHTML = checkoutItems.map((item) => {
      const safeName = escapeHtml(item?.name || 'Product');
      const safeImage = escapeHtml(item?.image || fallbackProductImage);
      const qty = Math.max(1, Number(item?.qty || 1));
      const unitPrice = Number(item?.price || 0);
      const lineTotal = unitPrice * qty;

      return `
        <div class="checkout-order-item">
          <img src="${safeImage}" class="checkout-order-item-img" alt="${safeName}">
          <div>
            <p class="checkout-order-item-title">${safeName}</p>
            <div class="checkout-order-item-meta">
              Qty: ${qty} x ${formatCurrency(unitPrice)} = <strong>${formatCurrency(lineTotal)}</strong>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  paymentBoxes.forEach((box) => {
    box.addEventListener('click', () => {
      paymentBoxes.forEach((item) => item.classList.remove('selected'));
      box.classList.add('selected');
      selectedPaymentMethod = box.dataset.method;

      fonepayInfo.style.display = selectedPaymentMethod === 'fonepay' ? 'block' : 'none';
      connectInfo.style.display = selectedPaymentMethod === 'connectips' ? 'block' : 'none';
    });
  });

  deliveryInputs.forEach((input) => {
    input.addEventListener('change', () => {
      saveCheckoutDraft();
      renderOrderSummary();
    });
  });

  [nameInput, phoneInput, addressInput].forEach((input) => {
    if (!input) return;
    input.addEventListener('input', saveCheckoutDraft);
  });

  placeOrderBtn.addEventListener('click', () => {
    const name = nameInput.value.trim();
    const phone = phoneInput.value.trim();
    const address = addressInput.value.trim();
    const checkoutItems = getCheckoutItems();

    if (!name || !phone || !address) {
      alert('Please fill all required fields.');
      return;
    }

    if (!selectedPaymentMethod) {
      alert('Please select a payment method.');
      return;
    }

    if (!document.getElementById('termsCheck').checked) {
      alert('Please agree to terms and conditions.');
      return;
    }

    if (checkoutItems.length === 0) {
      alert(
        isBuyNowMode
          ? 'No product selected for direct checkout.'
          : 'Your cart is empty. Please add products before checkout.'
      );
      return;
    }

    const subtotal = checkoutItems.reduce((sum, item) => {
      const price = Number(item?.price || 0);
      const qty = Math.max(1, Number(item?.qty || 1));
      return sum + (price * qty);
    }, 0);
    const total = subtotal + getSelectedDeliveryCharge();

    saveCheckoutDraft();
    if (isBuyNowMode) {
      clearBuyNowItem();
    }

    alert(
      `Order placed successfully! (No backend used)\n` +
      `Items: ${checkoutItems.length}\n` +
      `Total: ${formatCurrency(total)}`
    );
  });

  window.addEventListener('storage', (event) => {
    if (event.key === CART_KEY || event.key === BUY_NOW_KEY || event.key === LEGACY_BUY_NOW_KEY) {
      renderOrderSummary();
    }
  });

  window.addEventListener('gm-cart-updated', renderOrderSummary);

  restoreCheckoutDraft();
  renderOrderSummary();
});
</script>

@endsection
