@extends('frontend.layouts.main')

@section('main-content')
@php
  $authUser = auth()->user();
  $defaultName = old('full_name', $authUser->name ?? '');
  $defaultPhone = old('phone', $authUser->phone ?? '');
  $defaultAddress = old('address', $authUser->address ?? '');
@endphp

<style>
  .checkout-container {
    background-color: #fff;
    padding: 20px;
    font-size: 16px;
  }

  .checkout-container .order-summary,
  .checkout-container .payment-section {
    border: 1px solid #ddd;
    padding: 20px;
    margin-bottom: 20px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    border-radius: 10px;
  }

  .checkout-container h4 {
    margin-bottom: 20px;
    font-weight: bold;
    font-size: 20px;
  }

  .checkout-container .form-control,
  .checkout-container .form-check-input {
    border-radius: 8px;
  }

  .checkout-container .form-check-label {
    font-size: 15px;
  }

  .checkout-container .payment-methods {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
  }

  .checkout-container .payment-box {
    flex: 1 1 45%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
    border: 2px solid #ccc;
    border-radius: 10px;
    background-color: #fafafa;
    cursor: pointer;
    transition: background-color 0.4s ease, color 0.4s ease, border-color 0.3s;
    color: #000;
    text-align: center;
  }

  .checkout-container .payment-box:hover {
    background-color: #0A0F2C;
    color: #fff;
  }

  .checkout-container .payment-box img {
    height: 30px;
    margin-right: 10px;
  }

  .checkout-container .payment-box.selected {
    background-color: #0A0F2C;
    color: #fff;
    border-color: #0A0F2C;
  }

  .checkout-container .payment-box.selected img {
    filter: brightness(0) invert(1);
  }

  .checkout-container .place-order-btn {
    width: 100%;
    padding: 15px;
    background-color: #0A0F2C;
    color: white;
    font-weight: bold;
    font-size: 18px;
    border: none;
    margin-top: 20px;
    transition: background-color 0.3s ease;
    border-radius: 10px;
  }

  .checkout-container .place-order-btn:hover {
    color: #0A0F2C;
    background: goldenrod;
  }

  .checkout-container .place-order-btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    background: #6c757d;
    color: #fff;
  }

  .checkout-container .fonepay-info {
    margin-top: 20px;
    padding: 15px;
    border: 1px dashed #0A0F2C;
    border-radius: 10px;
    text-align: center;
    display: none;
  }

  .checkout-container .fonepay-info img {
    max-width: 200px;
    margin-bottom: 10px;
  }

  .checkout-order-items {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 14px;
    max-height: 280px;
    overflow-y: auto;
    padding-right: 4px;
  }

  .checkout-order-item {
    display: grid;
    grid-template-columns: 52px minmax(0, 1fr);
    gap: 10px;
    border: 1px solid #e8ecef;
    border-radius: 10px;
    padding: 8px;
    background: #fcfdfd;
  }

  .checkout-order-item-img {
    width: 52px;
    height: 52px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e8ecef;
    background: #fff;
  }

  .checkout-order-item-title {
    margin: 0;
    font-size: 0.93rem;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.3;
  }

  .checkout-order-item-meta {
    font-size: 0.84rem;
    color: #6b7280;
    margin-top: 2px;
  }

  .checkout-summary-line {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
    font-size: 0.98rem;
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
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
  }

  .checkout-summary-total span {
    font-weight: 700;
    color: #111827;
  }

  .checkout-summary-total strong {
    font-size: 1.35rem;
    color: #e55a2b;
  }

  .checkout-empty-order {
    margin-bottom: 12px;
    border-radius: 8px;
  }

  @media (max-width: 768px) {
    .checkout-container .payment-box {
      flex: 1 1 100%;
    }

    .checkout-order-items {
      max-height: none;
    }
  }
</style>

<div class="container checkout-container">
  <div class="row">
    <div class="col-lg-6">
      <div class="order-summary">
        <h4>Billing & Shipping Address</h4>
        <form id="checkout-address-form" novalidate>
          <div class="form-group mb-3">
            <label for="checkout-full-name">Full Name *</label>
            <input
              type="text"
              class="form-control"
              id="checkout-full-name"
              name="full_name"
              value="{{ $defaultName }}"
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
              required
            >
          </div>

          <div class="form-group mb-3">
            <label for="checkout-address">Enter your Address/Landmark here *</label>
            <input
              type="text"
              id="checkout-address"
              name="address"
              class="form-control"
              value="{{ $defaultAddress }}"
              required
            >
          </div>

          <div class="form-group mb-3">
            <label>Delivery *</label>
            <div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="delivery" value="inside" data-charge="100" checked>
                <label class="form-check-label">Inside Valley: Rs. 100</label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="radio" name="delivery" value="outside" data-charge="200">
                <label class="form-check-label">Outside Valley: Rs. 200</label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="radio" name="delivery" value="pickup" data-charge="0">
                <label class="form-check-label">Store Pickup: Free</label>
              </div>
            </div>
          </div>

          <div class="form-group mt-3" style="height:200px;">
            <iframe
              src="https://maps.google.com/maps?q=Kathmandu&t=&z=13&ie=UTF8&iwloc=&output=embed"
              style="width:100%; height:100%; border:0;"
              loading="lazy"
            ></iframe>
          </div>
        </form>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="payment-section">
        <h4>Choose Payment Options</h4>

        <div class="payment-methods">
          <div class="payment-box" data-method="fonepay">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRVm2cDdqOxx_Y_7HzvD6sh_QYx3Nrp-xi07Q&s" alt="Fonepay">
            <span>Fonepay</span>
          </div>

          <div class="payment-box" data-method="connectips">
            <img src="https://via.placeholder.com/80x40?text=ConnectIPS" alt="Connect IPS">
            <span>Connect IPS</span>
          </div>

          <div class="payment-box" data-method="cod">
            <span>Cash on Delivery</span>
          </div>
        </div>

        <div class="fonepay-info" id="fonepayInfo">
          <img src="https://via.placeholder.com/200x200?text=Fonepay+QR" alt="Fonepay QR">
          <p>
            Please put your <b>Name</b> and <b>Phone Number</b> in remarks.<br>
            Send screenshot on WhatsApp: <b>+977 9849433139</b>
          </p>
        </div>

        <div class="fonepay-info" id="connectInfo">
          <img src="https://via.placeholder.com/200x200?text=ConnectIPS+QR" alt="Connect IPS QR">
          <p>
            Please put your <b>Name</b> and <b>Phone Number</b> in remarks.<br>
            Send screenshot on WhatsApp: <b>+977 9849433139</b>
          </p>
        </div>

        <div class="form-check mt-4">
          <input class="form-check-input" type="checkbox" id="termsCheck">
          <label class="form-check-label" for="termsCheck">I agree to the terms and conditions *</label>
        </div>

        <button type="button" class="place-order-btn" id="place-order-btn">Place Order</button>
      </div>

      <div class="order-summary mt-3">
        <h4>Your Order</h4>

        <div id="checkout-empty-order" class="alert alert-warning checkout-empty-order d-none">
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
