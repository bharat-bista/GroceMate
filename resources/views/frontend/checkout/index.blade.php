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

  .checkout-container .esewa-info {
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

  .checkout-container .esewa-info p {
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

  .checkout-order-item-meta-row {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
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

  .checkout-calc-note {
    margin-top: 8px;
    border: 1px dashed #c9dacb;
    border-radius: 10px;
    background: #f8fcf8;
    color: #4b5563;
    font-size: 0.97rem;
    line-height: 1.4;
    padding: 9px 10px;
  }

  .checkout-calc-note strong {
    color: #111827;
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
    @if(session('success'))
    <div style="background: #f0fdf4; border: 1px solid #22c55e; color: #15803d; padding: 16px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
      <i class="fas fa-check-circle" style="font-size: 20px;"></i>
      <div>{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #ef4444; color: #dc2626; padding: 16px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
      <i class="fas fa-exclamation-circle" style="font-size: 20px;"></i>
      <div>{{ session('error') }}</div>
    </div>
    @endif

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
            <label for="checkout-email">Email (for order notifications)</label>
            <input
              type="email"
              id="checkout-email"
              name="email"
              class="form-control"
              value="{{ old('email', $authUser->email ?? '') }}"
              placeholder="Enter your email address"
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

          <div class="form-group" id="store-pickup-map-group" style="display: none;">
            <label>Store Location</label>
            <div class="checkout-map-wrap">
              <iframe
                src="https://maps.google.com/maps?q=27.700462,85.318181&t=&z=15&ie=UTF8&iwloc=&output=embed"
                loading="lazy"
                style="border: 0; width: 100%; height: 300px;"
                allowfullscreen
              ></iframe>
              <div style="margin-top: 12px; padding: 12px; background: #f0fdf4; border-radius: 8px; border-left: 4px solid #22c55e;">
                <small style="color: #15803d; font-weight: 600;">
                  <i class="fas fa-store"></i> Visit our store to pick up your order
                </small>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="checkout-side-stack">
        <div class="payment-section checkout-card">
          <h4><i class="fas fa-credit-card"></i> Choose Payment Option</h4>

          <div class="payment-methods">
            <div class="payment-box" data-method="esewa">
              <span class="payment-box-icon"><i class="fas fa-qrcode"></i></span>
              <span class="payment-box-text">
                <strong>eSewa</strong>
                <small>Mobile wallet payment</small>
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

          <div class="esewa-info" id="esewaInfo">
            <div class="payment-note-title"><i class="fas fa-info-circle"></i> eSewa Payment</div>
            <p>
              Click "Place Order" to be redirected to eSewa for secure payment processing.
              You will be able to complete the payment using your eSewa wallet.
            </p>
            <div style="margin-top: 12px; padding: 12px; background: #f0fdf4; border-radius: 8px; border-left: 4px solid #22c55e;">
              <small style="color: #15803d; font-weight: 600;">
                <i class="fas fa-shield-alt"></i> Secure Payment Gateway
              </small>
            </div>
          </div>

          <div class="esewa-info" id="connectInfo">
            <div class="payment-note-title"><i class="fas fa-info-circle"></i> Connect IPS Instructions</div>
            <p>
              Add your <b>Name</b> and <b>Phone Number</b> in remarks while paying.
              Then share payment screenshot on WhatsApp: <b>+977 9849433139</b>
            </p>
            <div style="margin-top: 15px; text-align: center;">
              <img src="{{ asset('assets/img/business/Bank.jpeg') }}" alt="Bank QR Code" style="max-width: 200px; border-radius: 10px; border: 2px solid #dce8dd;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
              <div style="display: none; padding: 20px; background: #f5faf6; border-radius: 10px; border: 2px dashed #bfd8c1;">
                <i class="fas fa-university" style="font-size: 36px; color: #2e7d32;"></i>
                <p style="color: #4b5563; font-size: 0.9rem; margin-top: 8px;">Bank QR Code</p>
                <p style="color: #6b7280; font-size: 0.8rem;">Scan to pay via Connect IPS</p>
              </div>
            </div>
          </div>

          <!-- Connect IPS Payment Slip Upload Section -->
          <div class="connect-ips-upload" id="connectIpsUpload" style="display: none; margin-top: 14px;">
            <div class="payment-note-title"><i class="fas fa-file-upload"></i> Upload Payment Slip</div>
            <div style="border: 2px dashed #bfd8c1; border-radius: 14px; padding: 20px; text-align: center; background: #f5faf6;">
              <input type="file" id="payment-slip-input" accept="image/*,.pdf" style="display: none;">
              <label for="payment-slip-input" style="cursor: pointer; display: block;">
                <i class="fas fa-cloud-upload-alt" style="font-size: 36px; color: #2e7d32; margin-bottom: 10px;"></i>
                <div style="color: #4b5563; font-size: 1rem;">
                  Click to upload payment screenshot<br>
                  <small style="color: #6b7280;">(JPG, PNG, PDF - Max 5MB)</small>
                </div>
              </label>
              <div id="payment-slip-preview" style="margin-top: 15px; display: none;">
                <img id="payment-slip-img" style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 1px solid #dce8dd;">
                <button type="button" id="remove-slip-btn" style="display: block; margin: 10px auto 0; padding: 6px 16px; background: #dc2626; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
                  <i class="fas fa-times"></i> Remove
                </button>
              </div>
            </div>
            <input type="hidden" id="payment-slip-data" name="payment_slip">
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
            <span>Products:</span>
            <strong id="checkout-product-count">0</strong>
          </div>

          <div class="checkout-summary-line">
            <span>Total Qty:</span>
            <strong id="checkout-item-count">0</strong>
          </div>

          <div class="checkout-summary-line">
            <span>Subtotal:</span>
            <strong id="checkout-subtotal">Rs. 0</strong>
          </div>

          <div class="checkout-summary-line">
            <span>Delivery Charge (flat):</span>
            <strong id="checkout-delivery">Rs. 0</strong>
          </div>

          <div class="checkout-summary-total">
            <span>Total:</span>
            <strong id="checkout-total">Rs. 0</strong>
          </div>

          <div class="checkout-calc-note" id="checkout-calc-note">
            Calculation: <strong>Rs. 0 + Rs. 0 = Rs. 0</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const CART_KEY = 'gm_cart_items';
  const BUY_NOW_KEY = 'gm_buy_now_item';
  const LEGACY_BUY_NOW_KEY = 'buynow';
  const CHECKOUT_DRAFT_KEY = 'gm_checkout_draft';
  const SELECTED_ITEMS_KEY = 'gm_checkout_selected_items';
  const isBuyNowMode = new URLSearchParams(window.location.search).get('mode') === 'buy-now';

  const paymentBoxes = document.querySelectorAll('.payment-box');
  const esewaInfo = document.getElementById('esewaInfo');
  const connectInfo = document.getElementById('connectInfo');
  const placeOrderBtn = document.getElementById('place-order-btn');
  const orderItemsWrap = document.getElementById('checkout-order-items');
  const emptyOrderEl = document.getElementById('checkout-empty-order');
  const subtotalEl = document.getElementById('checkout-subtotal');
  const deliveryEl = document.getElementById('checkout-delivery');
  const totalEl = document.getElementById('checkout-total');
  const productCountEl = document.getElementById('checkout-product-count');
  const itemCountEl = document.getElementById('checkout-item-count');
  const calcNoteEl = document.getElementById('checkout-calc-note');
  const deliveryInputs = document.querySelectorAll('input[name="delivery"]');
  const nameInput = document.getElementById('checkout-full-name');
  const phoneInput = document.getElementById('checkout-phone');
  const emailInput = document.getElementById('checkout-email');
  const addressInput = document.getElementById('checkout-address');
  const fallbackProductImage = @json(asset('assets/img/product/product1.jpg'));

  let selectedPaymentMethod = null;

  function formatCurrency(value) {
    const amount = Number(value || 0);
    const hasFraction = Math.abs(amount - Math.trunc(amount)) > 0.000001;

    return 'Rs. ' + amount.toLocaleString('en-US', {
      minimumFractionDigits: hasFraction ? 2 : 0,
      maximumFractionDigits: 2
    });
  }

  function parsePrice(value) {
    if (typeof value === 'number') {
      return Number.isFinite(value) ? value : 0;
    }
    const numeric = String(value ?? '').replace(/[^0-9.-]/g, '');
    const parsed = Number.parseFloat(numeric);
    return Number.isFinite(parsed) ? parsed : 0;
  }

  function toPaise(value) {
    return Math.round((parsePrice(value) + Number.EPSILON) * 100);
  }

  function fromPaise(value) {
    return Number(value || 0) / 100;
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

  function readSelectedItems() {
    try {
      var raw = localStorage.getItem(SELECTED_ITEMS_KEY);
      


      var parsed = (raw && raw !== 'null') ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch (_) {
      return [];
    }
  }

  function normalizeCheckoutItem(item) {
    const price = parsePrice(item?.price);

    return {
        id: String(item?.id ?? '').trim(),
        name: String(item?.name || 'Product'),
        price: price,
        image: String(item?.image || ''),
        qty: Math.max(1, Math.floor(Number(item?.qty || 1) || 1)),
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
      const selectedItems = readSelectedItems();

      if (selectedItems.length > 0) {
        return selectedItems.map(normalizeCheckoutItem);
      }

      return readCartItems().map(normalizeCheckoutItem);
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

  function clearSelectedItems() {
    localStorage.removeItem(SELECTED_ITEMS_KEY);
  }

  function getSelectedDeliveryCharge() {
    const selectedDelivery = document.querySelector('input[name="delivery"]:checked');
    return parsePrice(selectedDelivery?.dataset?.charge || 0);
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

    const summary = checkoutItems.reduce((acc, item) => {
      const unitPricePaise = toPaise(item?.price);
      const qty = Math.max(1, Math.floor(Number(item?.qty || 1) || 1));
      const lineTotalPaise = unitPricePaise * qty;

      acc.products += 1;
      acc.qty += qty;
      acc.subtotalPaise += lineTotalPaise;

      return acc;
    }, { products: 0, qty: 0, subtotalPaise: 0 });

    const deliveryPaise = toPaise(getSelectedDeliveryCharge());
    const subtotal = fromPaise(summary.subtotalPaise);
    const deliveryCharge = fromPaise(deliveryPaise);
    const total = fromPaise(summary.subtotalPaise + deliveryPaise);

    productCountEl.textContent = String(summary.products);
    itemCountEl.textContent = String(summary.qty);
    subtotalEl.textContent = formatCurrency(subtotal);
    deliveryEl.textContent = formatCurrency(deliveryCharge);
    totalEl.textContent = formatCurrency(total);
    calcNoteEl.innerHTML = `Calculation: <strong>${formatCurrency(subtotal)} + ${formatCurrency(deliveryCharge)} = ${formatCurrency(total)}</strong>`;

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
      const qty = Math.max(1, Math.floor(Number(item?.qty || 1) || 1));
      const unitPricePaise = toPaise(item?.price);
      const lineTotalPaise = unitPricePaise * qty;
      const unitPrice = fromPaise(unitPricePaise);
      const lineTotal = fromPaise(lineTotalPaise);

      return `
        <div class="checkout-order-item">
          <img src="${safeImage}" class="checkout-order-item-img" alt="${safeName}">
          <div>
            <p class="checkout-order-item-title">${safeName}</p>
            <div class="checkout-order-item-meta">
              Unit Price: <strong>${formatCurrency(unitPrice)}</strong>
            </div>
            <div class="checkout-order-item-meta checkout-order-item-meta-row">
              <span>Qty: ${qty}</span>
              <span>Line Total: <strong>${formatCurrency(lineTotal)}</strong></span>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  paymentBoxes.forEach(function(box) {
    box.addEventListener('click', function() {
      paymentBoxes.forEach(function(item) { item.classList.remove('selected'); });
      box.classList.add('selected');
      selectedPaymentMethod = box.dataset.method;

      esewaInfo.style.display = selectedPaymentMethod === 'esewa' ? 'block' : 'none';
      connectInfo.style.display = selectedPaymentMethod === 'connectips' ? 'block' : 'none';
      
      // Show/hide payment slip upload for Connect IPS
      var connectIpsUpload = document.getElementById('connectIpsUpload');
      if (connectIpsUpload) {
        connectIpsUpload.style.display = selectedPaymentMethod === 'connectips' ? 'block' : 'none';
      }
    });
  });

  deliveryInputs.forEach(function(input) {
    input.addEventListener('change', function() {
      saveCheckoutDraft();
      renderOrderSummary();
      
      // Toggle map visibility based on delivery selection
      var storePickupMap = document.getElementById('store-pickup-map-group');
      
      if (input.value === 'pickup') {
        // Show map and hide address field for store pickup
        storePickupMap.style.display = 'block';
        if (addressInput) {
          addressInput.parentElement.style.display = 'none';
          addressInput.required = false;
        }
      } else {
        // Hide map and show address field for delivery
        storePickupMap.style.display = 'none';
        if (addressInput) {
          addressInput.parentElement.style.display = 'block';
          addressInput.required = true;
        }
      }
    });
  });

  // Payment slip upload handling
  var paymentSlipInput = document.getElementById('payment-slip-input');
  var paymentSlipPreview = document.getElementById('payment-slip-preview');
  var paymentSlipImg = document.getElementById('payment-slip-img');
  var paymentSlipData = document.getElementById('payment-slip-data');
  var removeSlipBtn = document.getElementById('remove-slip-btn');

  if (paymentSlipInput) {
    paymentSlipInput.addEventListener('change', function(e) {
      var file = e.target.files[0];
      if (!file) return;

      // Validate file type
      var validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
      if (!validTypes.includes(file.type)) {
        alert('Please upload a valid image (JPG, PNG) or PDF file.');
        paymentSlipInput.value = '';
        return;
      }

      // Validate file size (5MB max)
      if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB.');
        paymentSlipInput.value = '';
        return;
      }

      var reader = new FileReader();
      reader.onload = function(e) {
        var result = e.target.result;
        paymentSlipData.value = result;
        
        if (file.type === 'application/pdf') {
          paymentSlipImg.src = '{{ asset("assets/img/pdf-icon.png") }}';
        } else {
          paymentSlipImg.src = result;
        }
        paymentSlipPreview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    });
  }

  if (removeSlipBtn) {
    removeSlipBtn.addEventListener('click', function() {
      paymentSlipInput.value = '';
      paymentSlipData.value = '';
      paymentSlipPreview.style.display = 'none';
      paymentSlipImg.src = '';
    });
  }

  [nameInput, phoneInput, addressInput].forEach(function(input) {
    if (!input) return;
    input.addEventListener('input', saveCheckoutDraft);
  });

  placeOrderBtn.addEventListener('click', function() {
    var name = nameInput.value.trim();
    var phone = phoneInput.value.trim();
    var email = emailInput ? emailInput.value.trim() : '';
    var address = addressInput.value.trim();
    const checkoutItems = getCheckoutItems();

    const selectedDelivery = document.querySelector('input[name="delivery"]:checked');
    const isStorePickup = selectedDelivery && selectedDelivery.value === 'pickup';
    
    if (!name || !phone || (!address && !isStorePickup)) {
      alert('Please fill all required fields.');
      return;
    }

    if (!selectedPaymentMethod) {
      alert('Please select a payment method.');
      return;
    }

    // Validate payment slip for Connect IPS
    if (selectedPaymentMethod === 'connectips') {
      var paymentSlipData = document.getElementById('payment-slip-data');
      if (!paymentSlipData || !paymentSlipData.value) {
        alert('Please upload your payment slip before placing the order.');
        return;
      }
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
      const unitPricePaise = toPaise(item?.price);
      const qty = Math.max(1, Math.floor(Number(item?.qty || 1) || 1));
      return sum + (unitPricePaise * qty);
    }, 0);
    const deliveryPaise = toPaise(getSelectedDeliveryCharge());
    const deliveryCharge = fromPaise(deliveryPaise);
    const total = fromPaise(subtotal + deliveryPaise);

    // Handle different payment methods
    if (selectedPaymentMethod === 'esewa') {
      // Create form data for eSewa payment
      const formData = new FormData();
      formData.append('full_name', name);
      formData.append('phone', phone);
      formData.append('email', email);
      formData.append('address', isStorePickup ? 'Store Pickup' : address);
      formData.append('delivery', document.querySelector('input[name="delivery"]:checked').value);
      formData.append('amount', total);
      formData.append('items', JSON.stringify(checkoutItems));

      // Show loading state
      placeOrderBtn.disabled = true;
      placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Redirecting to eSewa...';

      // Submit to eSewa initiation endpoint
      fetch('{{ route("frontend.checkout.esewa.initiate") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.text();
      })
      .then(html => {
        // Create a new window and write the HTML response
        const newWindow = window.open('', '_blank');
        if (newWindow) {
          newWindow.document.write(html);
          newWindow.document.close();
        } else {
          // Popup blocked: continue in same tab
          document.open();
          document.write(html);
          document.close();
        }
      })
      .catch(error => {
        placeOrderBtn.disabled = false;
        placeOrderBtn.innerHTML = 'Place Order';
        alert('Error initiating payment. Please try again.');
      });
    } else {
      // Handle other payment methods (COD, Connect IPS)
      saveCheckoutDraft();
      
      // Prepare order data
      var orderData = {
        full_name: name,
        phone: phone,
        email: email,
        address: isStorePickup ? 'Store Pickup' : address,
        delivery: document.querySelector('input[name="delivery"]:checked').value,
        payment_method: selectedPaymentMethod,
        amount: total,
        items: checkoutItems,
        payment_slip: selectedPaymentMethod === 'connectips' ? document.getElementById('payment-slip-data').value : null,
        _token: '{{ csrf_token() }}'
      };

      // Show loading
      placeOrderBtn.disabled = true;
      placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Order...';

      // Save order via AJAX
      fetch('{{ route("frontend.order.store") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(orderData)
      })
      .then(async function(response) {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
          const message = data.message || 'Unable to place order.';
          throw new Error(message);
        }
        return data;
      })
      .then(function(data) {
        if (data.success) {
          // Clear cart after successful order
          if (isBuyNowMode) {
            clearBuyNowItem();
          } else {
            clearSelectedItems();
          }

          // Show success and redirect to orders page
          alert(
            'Order placed successfully!\n' +
            'Order #: ' + data.order_number + '\n' +
            'Products: ' + checkoutItems.length + '\n' +
            'Delivery: ' + formatCurrency(deliveryCharge) + '\n' +
            'Total: ' + formatCurrency(total) + '\n' +
            'Payment: ' + (selectedPaymentMethod === 'cod' ? 'Cash on Delivery' : 'Connect IPS')
          );

          window.location.href = '{{ route("orders") }}';
        } else {
          alert('Error: ' + (data.message || 'Something went wrong'));
          placeOrderBtn.disabled = false;
          placeOrderBtn.innerHTML = 'Place Order';
        }
      })
      .catch(function(error) {
        alert(error.message || 'Error placing order. Please try again.');
        placeOrderBtn.disabled = false;
        placeOrderBtn.innerHTML = 'Place Order';
      });
    }
  });

  window.addEventListener('storage', function(event) {
    if (event.key === CART_KEY || event.key === BUY_NOW_KEY || event.key === LEGACY_BUY_NOW_KEY) {
      renderOrderSummary();
    }
  });

  window.addEventListener('gm-cart-updated', renderOrderSummary);

  restoreCheckoutDraft();
  renderOrderSummary();
  
  
  // Initialize map visibility state based on current delivery selection
  var currentDelivery = document.querySelector('input[name="delivery"]:checked');
  var storePickupMap = document.getElementById('store-pickup-map-group');
  
  if (currentDelivery && currentDelivery.value === 'pickup') {
    // Show map and hide address field for store pickup
    storePickupMap.style.display = 'block';
    if (addressInput) {
      addressInput.parentElement.style.display = 'none';
      addressInput.required = false;
    }
  } else {
    // Hide map and show address field for delivery
    storePickupMap.style.display = 'none';
    if (addressInput) {
      addressInput.parentElement.style.display = 'block';
      addressInput.required = true;
    }
  }
});

</script>

@endsection


