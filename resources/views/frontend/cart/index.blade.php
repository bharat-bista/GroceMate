@extends('frontend.layouts.main')

@section('main-content')
{{-- ============================================= --}}
{{-- DARAZ-STYLE CART PAGE - MODERN DESIGN --}}
{{-- ============================================= --}}

<style>
/* ==========================================
   GROCEMATE CART - HOME THEME CONSISTENCY
   ========================================== */
:root {
    --gm-primary: #2E7D32;
    --gm-primary-dark: #1B5E20;
    --gm-primary-light: #4CAF50;
    --gm-accent: #FF6B35;
    --gm-accent-dark: #E55A2B;
    --gm-white: #FFFFFF;
    --gm-light: #F8FBF8;
    --gm-gray: #6B7280;
    --gm-gray-light: #E5E7EB;
    --gm-dark: #1F2937;
    --gm-star: #FACA51;
    --gm-shadow: 0 6px 24px rgba(27, 94, 32, 0.08);
    --gm-shadow-hover: 0 12px 30px rgba(27, 94, 32, 0.12);
    --gm-radius: 16px;
    --gm-border-soft: rgba(46, 125, 50, 0.18);
    --gm-surface: linear-gradient(135deg, #ffffff 0%, #f8fdf9 100%);
}

.cart-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px 16px;
    background:
        radial-gradient(circle at 12% 8%, rgba(46, 125, 50, 0.08), transparent 34%),
        radial-gradient(circle at 90% 22%, rgba(255, 107, 53, 0.07), transparent 30%),
        linear-gradient(180deg, #f6faf6 0%, #f9fcf9 100%);
    min-height: 100vh;
}

.cart-header,
.cart-items-section,
.order-summary,
.empty-cart {
    background: var(--gm-surface);
    border: 2px solid var(--gm-border-soft);
    border-radius: var(--gm-radius);
    box-shadow: var(--gm-shadow);
    position: relative;
    overflow: hidden;
}

.cart-header::before,
.cart-items-section::before,
.order-summary::before,
.empty-cart::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gm-primary), #1e7e34 50%, var(--gm-primary-light));
    background-size: 200% 100%;
    animation: cartGradientShift 4s ease infinite;
}

@keyframes cartGradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.cart-header {
    padding: 20px 24px;
    margin-bottom: 18px;
}

.cart-header h1 {
    margin: 0;
    font-size: clamp(1.35rem, 3vw, 1.95rem);
    color: var(--gm-dark);
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cart-header h1 i {
    color: var(--gm-primary);
}

.cart-breadcrumb {
    margin-top: 8px;
    font-size: 0.9rem;
    color: var(--gm-gray);
}

.cart-breadcrumb a {
    color: var(--gm-primary);
    text-decoration: none;
    font-weight: 600;
}

.cart-breadcrumb a:hover {
    color: var(--gm-primary-dark);
    text-decoration: underline;
}

.cart-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 18px;
}

.cart-items-section {
    padding: 20px;
}

.cart-items-header {
    border-bottom: 1px solid var(--gm-gray-light);
    padding-bottom: 14px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.cart-items-header h2 {
    margin: 0;
    font-size: 1.2rem;
    color: var(--gm-dark);
    font-weight: 800;
}

.cart-items-count {
    font-size: 0.9rem;
    color: var(--gm-gray);
    font-weight: 600;
}

.select-all-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    background: linear-gradient(135deg, #f1f8e9 0%, #ffffff 100%);
    border: 2px solid rgba(46, 125, 50, 0.2);
    border-radius: 12px;
    margin-bottom: 14px;
}

.select-all-bar input[type="checkbox"],
.item-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--gm-primary);
}

.select-all-bar label {
    flex: 1;
    margin: 0;
    font-size: 0.92rem;
    color: var(--gm-dark);
    font-weight: 600;
    cursor: pointer;
}

.delete-selected {
    background: transparent;
    border: none;
    color: var(--gm-gray);
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
}

.delete-selected:hover {
    color: var(--gm-accent);
}

.cart-item {
    display: grid;
    grid-template-areas: "check image details";
    grid-template-columns: 26px 110px minmax(0, 1fr);
    gap: 16px;
    padding: 14px;
    border: 1px solid rgba(46, 125, 50, 0.16);
    border-radius: 14px;
    background: linear-gradient(135deg, #ffffff 0%, #f8fbf8 100%);
    margin-bottom: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    transition: opacity 0.3s, transform 0.3s;
}

.cart-item:last-child {
    margin-bottom: 0;
}

.cart-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--gm-shadow-hover);
}

.item-checkbox {
    grid-area: check;
    display: flex;
    align-items: flex-start;
    padding-top: 8px;
}

.item-image {
    grid-area: image;
    width: 110px;
    height: 110px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(46, 125, 50, 0.15);
    background: #f9fafb;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    grid-area: details;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 0;
}

.item-brand {
    font-size: 0.78rem;
    color: var(--gm-primary);
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.item-title {
    margin: 0;
    font-size: 1.05rem;
    color: var(--gm-dark);
    font-weight: 700;
    line-height: 1.35;
}

.item-title:hover {
    color: var(--gm-primary);
    cursor: pointer;
}

.item-rating {
    display: flex;
    align-items: center;
    gap: 6px;
}

.item-rating .stars {
    display: inline-flex;
    gap: 2px;
}

.item-rating .stars i {
    color: var(--gm-star);
    font-size: 0.82rem;
}

.item-rating .count {
    color: var(--gm-gray);
    font-size: 0.85rem;
    font-weight: 600;
}

.item-price-section {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.item-price {
    font-size: 1.35rem;
    color: var(--gm-accent);
    font-weight: 800;
    line-height: 1;
}

.item-original-price {
    font-size: 0.95rem;
    color: #9ca3af;
    text-decoration: line-through;
}

.item-discount {
    font-size: 0.82rem;
    color: var(--gm-primary-dark);
    font-weight: 700;
    background: rgba(46, 125, 50, 0.12);
    padding: 3px 9px;
    border-radius: 999px;
}

.item-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.item-quantity {
    display: inline-flex;
    align-items: center;
    border: 1px solid rgba(46, 125, 50, 0.22);
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
}

.qty-btn {
    width: 34px;
    height: 34px;
    border: none;
    background: #fff;
    color: var(--gm-dark);
    cursor: pointer;
    font-size: 1.05rem;
    font-weight: 700;
}

.qty-btn:hover {
    background: var(--gm-light);
    color: var(--gm-primary-dark);
}

.qty-input {
    width: 52px;
    height: 34px;
    border: none;
    border-left: 1px solid rgba(46, 125, 50, 0.18);
    border-right: 1px solid rgba(46, 125, 50, 0.18);
    text-align: center;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--gm-dark);
}

.item-remove {
    background: transparent;
    border: none;
    color: var(--gm-gray);
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 2px;
}

.item-remove:hover {
    color: var(--gm-accent);
}

.order-summary {
    padding: 18px;
    height: fit-content;
    position: sticky;
    top: 22px;
    background: linear-gradient(160deg, #ffffff 0%, #f6fbf7 65%, #ecf8ef 100%);
}

.order-summary h3 {
    margin: 0 0 16px 0;
    color: var(--gm-dark);
    font-size: 1.1rem;
    font-weight: 800;
}

.summary-row,
.summary-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.summary-row {
    margin-bottom: 11px;
    font-size: 0.95rem;
}

.summary-row .label {
    color: var(--gm-gray);
}

.summary-row .value {
    color: var(--gm-dark);
    font-weight: 700;
}

.summary-divider {
    height: 1px;
    background: var(--gm-gray-light);
    margin: 14px 0;
}

.summary-total {
    margin-bottom: 16px;
}

.summary-total .label {
    color: var(--gm-dark);
    font-weight: 800;
    font-size: 1rem;
}

.summary-total .value {
    color: var(--gm-accent);
    font-size: 1.35rem;
    font-weight: 900;
}

.checkout-btn,
.continue-shopping,
.promo-input-group button {
    border: none;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s ease;
}

.checkout-btn {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, var(--gm-primary) 0%, #1e7e34 100%);
    color: #fff;
    font-size: 0.98rem;
}

.checkout-btn:hover {
    background: linear-gradient(135deg, var(--gm-primary-dark) 0%, #145a1b 100%);
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(27, 94, 32, 0.25);
}

.continue-shopping {
    width: 100%;
    padding: 11px;
    margin-top: 9px;
    background: #fff;
    color: var(--gm-accent);
    border: 1px solid rgba(255, 107, 53, 0.45);
    font-size: 0.92rem;
}

.continue-shopping:hover {
    background: rgba(255, 107, 53, 0.08);
    border-color: var(--gm-accent);
}

.promo-code {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--gm-gray-light);
}

.promo-code h4 {
    margin: 0 0 10px 0;
    color: var(--gm-dark);
    font-size: 0.93rem;
    font-weight: 700;
}

.promo-input-group {
    display: flex;
    gap: 8px;
}

.promo-input-group input {
    flex: 1;
    border: 1px solid rgba(46, 125, 50, 0.24);
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 0.9rem;
    outline: none;
}

.promo-input-group input:focus {
    border-color: var(--gm-primary);
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.14);
}

.promo-input-group button {
    padding: 10px 16px;
    background: linear-gradient(135deg, var(--gm-accent) 0%, var(--gm-accent-dark) 100%);
    color: #fff;
    font-size: 0.88rem;
}

.promo-input-group button:hover {
    filter: brightness(0.95);
}

.empty-cart {
    padding: 54px 20px;
    text-align: center;
}

.empty-cart-icon {
    font-size: 4.4rem;
    color: var(--gm-primary-light);
    margin-bottom: 16px;
}

.empty-cart h2 {
    margin-bottom: 8px;
    color: var(--gm-dark);
    font-size: 1.45rem;
    font-weight: 800;
}

.empty-cart p {
    color: var(--gm-gray);
    margin-bottom: 22px;
}

.empty-cart-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 30px;
    text-decoration: none;
    border-radius: 10px;
    background: var(--gm-primary);
    color: #fff;
    font-weight: 700;
    transition: all 0.25s ease;
}

.empty-cart-btn:hover {
    background: var(--gm-primary-dark);
    color: #fff;
}

/* Responsive */
@media (max-width: 1180px) {
    .cart-layout {
        grid-template-columns: minmax(0, 1fr) 300px;
    }

    .order-summary {
        top: 16px;
    }
}

@media (max-width: 980px) {
    .cart-layout {
        grid-template-columns: minmax(0, 1fr) 240px;
        gap: 12px;
    }
}

@media (max-width: 768px) {
    .cart-container {
        padding: 10px 8px 14px;
    }

    .cart-layout {
        grid-template-columns: minmax(0, 1fr) 190px;
        gap: 9px;
    }

    .cart-header,
    .cart-items-section,
    .order-summary {
        border-radius: 10px;
        padding: 10px;
    }

    .cart-items-header {
        align-items: center;
        flex-direction: row;
        gap: 8px;
        margin-bottom: 10px;
        padding-bottom: 10px;
    }

    .cart-items-header h2 {
        font-size: 0.95rem;
    }

    .cart-items-count {
        font-size: 0.78rem;
    }

    .select-all-bar {
        padding: 8px 10px;
        margin-bottom: 10px;
        gap: 8px;
    }

    .select-all-bar label {
        font-size: 0.78rem;
    }

    .delete-selected {
        font-size: 0.72rem;
    }

    .cart-item {
        grid-template-areas: "check image details";
        grid-template-columns: 20px 70px minmax(0, 1fr);
        gap: 8px;
        padding: 8px;
        margin-bottom: 8px;
    }

    .item-image {
        width: 70px;
        height: 70px;
        border-radius: 8px;
    }

    .item-brand {
        font-size: 0.64rem;
    }

    .item-title {
        font-size: 0.78rem;
        line-height: 1.25;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .item-rating {
        display: none;
    }

    .item-price {
        font-size: 0.98rem;
    }

    .item-original-price {
        font-size: 0.76rem;
    }

    .item-discount {
        font-size: 0.68rem;
        padding: 2px 6px;
    }

    .item-actions {
        justify-content: space-between;
        gap: 6px;
    }

    .item-quantity {
        border-radius: 8px;
    }

    .qty-btn {
        width: 24px;
        height: 24px;
        font-size: 0.86rem;
    }

    .qty-input {
        width: 34px;
        height: 24px;
        font-size: 0.76rem;
    }

    .item-remove {
        font-size: 0.72rem;
        gap: 4px;
        white-space: nowrap;
    }

    .order-summary h3 {
        font-size: 0.88rem;
        margin-bottom: 10px;
    }

    .summary-row {
        font-size: 0.76rem;
        margin-bottom: 7px;
    }

    .summary-total .label {
        font-size: 0.86rem;
    }

    .summary-total .value {
        font-size: 1.02rem;
    }

    .checkout-btn,
    .continue-shopping {
        padding: 9px;
        font-size: 0.74rem;
    }

    .promo-code h4 {
        font-size: 0.78rem;
    }

    .promo-input-group {
        flex-direction: column;
        gap: 6px;
    }

    .promo-input-group input,
    .promo-input-group button {
        width: 100%;
        padding: 8px 9px;
        font-size: 0.72rem;
    }
}

@media (max-width: 560px) {
    .cart-header h1 {
        font-size: 1rem;
    }

    .cart-breadcrumb {
        font-size: 0.74rem;
    }

    .cart-layout {
        grid-template-columns: minmax(0, 1fr) 150px;
        gap: 7px;
    }

    .select-all-bar {
        flex-wrap: nowrap;
        padding: 7px 8px;
    }

    .select-all-bar label {
        font-size: 0.7rem;
    }

    .cart-item {
        grid-template-areas: "check image details";
        grid-template-columns: 16px 56px minmax(0, 1fr);
        gap: 7px;
        padding: 7px;
    }

    .item-checkbox {
        padding-top: 2px;
    }

    .item-image {
        width: 56px;
        height: 56px;
    }

    .item-title {
        font-size: 0.7rem;
    }

    .item-price {
        font-size: 0.84rem;
    }

    .item-original-price,
    .item-discount {
        font-size: 0.64rem;
    }

    .item-actions {
        flex-direction: row;
        align-items: center;
        gap: 6px;
    }

    .qty-btn {
        width: 20px;
        height: 20px;
        font-size: 0.74rem;
    }

    .qty-input {
        width: 28px;
        height: 20px;
        font-size: 0.66rem;
    }

    .item-remove {
        font-size: 0.64rem;
    }

    .order-summary {
        padding: 8px;
    }

    .promo-code {
        display: none;
    }
}

@media (max-width: 420px) {
    .cart-layout {
        grid-template-columns: 1fr;
    }

    .order-summary {
        position: static;
    }
}
</style>

<div class="cart-container">
    <!-- Cart Header -->
    <div class="cart-header">
        <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
        <div class="cart-breadcrumb">
            <a href="{{ route('home') }}">Home</a> > Cart
        </div>
    </div>

    {{-- Check if cart has items --}}
    @php
        // Temporary cart data - Replace with actual cart session/database data
        $cartItems = [
            [
                'id' => 1,
                'name' => 'Sunflower Oil Premium Quality Cooking Oil',
                'brand' => 'Sunflow',
                'image' => 'assets/img/product/product1.jpg',
                'price' => 120,
                'original_price' => 180,
                'discount' => 18,
                'quantity' => 1,
                'rating' => 4.5,
                'reviews' => 128
            ],
            [
                'id' => 2,
                'name' => 'Sugar White Crystal Pure & Sweet',
                'brand' => 'Sunny',
                'image' => 'assets/img/product/product2.jpg',
                'price' => 80,
                'original_price' => 100,
                'discount' => 20,
                'quantity' => 2,
                'rating' => 4,
                'reviews' => 95
            ],
            [
                'id' => 3,
                'name' => 'Masoor Dal Red Lentils Premium',
                'brand' => 'Rani',
                'image' => 'assets/img/product/product3.jpg',
                'price' => 120,
                'original_price' => 180,
                'discount' => 15,
                'quantity' => 1,
                'rating' => 5,
                'reviews' => 156
            ],
        ];
        
        $hasItems = count($cartItems) > 0;
    @endphp

    @if($hasItems)
        <div class="cart-layout">
            <!-- Cart Items Section -->
            <div class="cart-items-section">
                <div class="cart-items-header">
                    <h2>Cart Items</h2>
                    <span class="cart-items-count">({{ count($cartItems) }} items)</span>
                </div>

                <!-- Select All Bar -->
                <div class="select-all-bar">
                    <input type="checkbox" id="select-all">
                    <label for="select-all">Select All</label>
                    <button class="delete-selected"><i class="fas fa-trash-alt"></i> Delete Selected</button>
                </div>

                <!-- Cart Items List -->
                <div class="cart-items-list">
                    @foreach($cartItems as $item)
                    <div class="cart-item" data-item-id="{{ $item['id'] }}" data-price="{{ $item['price'] }}">
                        <div class="item-checkbox">
                            <input type="checkbox" class="item-check" data-price="{{ $item['price'] }}" data-qty="{{ $item['quantity'] }}">
                        </div>
                        
                        <div class="item-image">
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}">
                        </div>
                        
                        <div class="item-details">
                            <div>
                                <div class="item-brand">{{ $item['brand'] }}</div>
                                <h3 class="item-title">{{ $item['name'] }}</h3>
                                <div class="item-rating">
                                    <div class="stars">
                                        @for($i = 0; $i < 5; $i++)
                                            @if($i < floor($item['rating']))
                                                <i class="fas fa-star"></i>
                                            @elseif($i < $item['rating'])
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="count">({{ $item['reviews'] }})</span>
                                </div>
                            </div>
                            
                            <div class="item-price-section">
                                <span class="item-price">Rs.{{ $item['price'] }}</span>
                                <span class="item-original-price">Rs.{{ $item['original_price'] }}</span>
                                <span class="item-discount">-{{ $item['discount'] }}%</span>
                            </div>
                            
                            <div class="item-actions">
                                <div class="item-quantity">
                                    <button class="qty-btn qty-minus" data-item-id="{{ $item['id'] }}">-</button>
                                    <input type="text" class="qty-input" value="{{ $item['quantity'] }}" readonly>
                                    <button class="qty-btn qty-plus" data-item-id="{{ $item['id'] }}">+</button>
                                </div>
                                <button class="item-remove" data-item-id="{{ $item['id'] }}">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="order-summary">
                <h3>Order Summary</h3>

                <div class="summary-total">
                    <span class="label">Total</span>
                    <span class="value" id="total">Rs.0</span>
                </div>
                
                <button class="checkout-btn" onclick="window.location.href='{{ route('checkout') }}'">
                    <i class="fas fa-lock"></i> Proceed to Checkout
                </button>
                
                <button class="continue-shopping" onclick="window.location.href='{{ route('home') }}'">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </button>
                
                <!-- Promo Code Section -->
                <div class="promo-code">
                    <h4>Have a promo code?</h4>
                    <div class="promo-input-group">
                        <input type="text" placeholder="Enter code" id="promo-code-input">
                        <button id="apply-promo">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="empty-cart">
            <div class="empty-cart-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h2>Your Cart is Empty</h2>
            <p>Looks like you haven't added anything to your cart yet</p>
            <a href="{{ route('home') }}" class="empty-cart-btn">
                <i class="fas fa-shopping-bag"></i> Start Shopping
            </a>
        </div>
    @endif
</div>

{{-- ==========================================
    CART FUNCTIONALITY JAVASCRIPT
    ========================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cartApi = window.GroceMateCart || null;

    function parsePrice(value) {
        if (typeof value === 'number') {
            return value;
        }
        return parseFloat(String(value || '').replace(/[^\d.]/g, '')) || 0;
    }

    function getRowQty(row) {
        const qtyInput = row.querySelector('.qty-input');
        return parseInt(qtyInput?.value || '1', 10) || 1;
    }

    function syncQtyToStorage(row) {
        if (!cartApi) {
            return;
        }
        const itemId = row?.dataset?.itemId;
        if (!itemId) {
            return;
        }
        cartApi.updateQty(itemId, getRowQty(row));
    }

    function syncStorageFromDomWhenDifferent() {
        if (!cartApi) {
            return;
        }

        const domItems = Array.from(document.querySelectorAll('.cart-item')).map((row) => ({
            id: String(row.dataset.itemId || ''),
            name: row.querySelector('.item-title')?.textContent?.trim() || 'Product',
            price: parsePrice(row.querySelector('.item-price')?.textContent || '0'),
            image: row.querySelector('.item-image img')?.getAttribute('src') || '',
            qty: getRowQty(row),
        })).filter((item) => item.id);

        if (domItems.length === 0) {
            return;
        }

        const storageItems = cartApi.getItems();
        const storageById = new Map(
            storageItems.map((item) => [String(item.id || ''), item])
        );
        let shouldSync = storageItems.length === 0 || domItems.length !== storageItems.length;

        if (!shouldSync) {
            for (const domItem of domItems) {
                const stored = storageById.get(String(domItem.id));

                if (!stored) {
                    shouldSync = true;
                    break;
                }

                const storedPrice = parsePrice(stored.price);
                const storedQty = Math.max(1, Number(stored.qty || 1));
                const priceDrift = Math.abs(storedPrice - domItem.price);

                if (priceDrift > 0.01 || storedQty !== domItem.qty) {
                    shouldSync = true;
                    break;
                }
            }
        }

        if (shouldSync) {
            cartApi.setItems(domItems);
        }
    }

    function updateEmptyCartState() {
        const cartRows = document.querySelectorAll('.cart-item');
        if (cartRows.length > 0) {
            return;
        }

        const list = document.querySelector('.cart-items-list');
        if (list) {
            list.innerHTML = '<div style="padding: 18px; text-align: center; color: var(--gm-gray);">Your cart is empty.</div>';
        }

        const selectAllBar = document.querySelector('.select-all-bar');
        if (selectAllBar) {
            selectAllBar.remove();
        }
    }

    // Calculate totals
    function calculateTotals() {
        let total = 0;
        
        document.querySelectorAll('.item-check:checked').forEach(checkbox => {
            const price = parseFloat(checkbox.dataset.price);
            const qty = parseInt(checkbox.dataset.qty);
            
            total += price * qty;
        });

        document.getElementById('total').textContent = `Rs.${total}`;
    }
    
    // Select All functionality
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.item-check').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            calculateTotals();
        });
    }
    
    // Individual item checkboxes
    document.querySelectorAll('.item-check').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            calculateTotals();
            
            // Update select all checkbox
            const allChecked = Array.from(document.querySelectorAll('.item-check')).every(cb => cb.checked);
            if (selectAllCheckbox) selectAllCheckbox.checked = allChecked;
        });
    });
    
    // Quantity controls
    document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.qty-input');
            const checkbox = this.closest('.cart-item').querySelector('.item-check');
            let value = parseInt(input.value);
            
            if (value > 1) {
                value--;
                input.value = value;
                checkbox.dataset.qty = value;
                syncQtyToStorage(this.closest('.cart-item'));
                calculateTotals();
            }
        });
    });
    
    document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.qty-input');
            const checkbox = this.closest('.cart-item').querySelector('.item-check');
            let value = parseInt(input.value);
            
            if (value < 99) {
                value++;
                input.value = value;
                checkbox.dataset.qty = value;
                syncQtyToStorage(this.closest('.cart-item'));
                calculateTotals();
            }
        });
    });
    
    // Remove item
    document.querySelectorAll('.item-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                const itemId = this.dataset.itemId;
                const cartItem = this.closest('.cart-item');
                
                // Animate removal
                cartItem.style.opacity = '0';
                cartItem.style.transform = 'translateX(20px)';
                
                setTimeout(() => {
                    cartItem.remove();
                    if (cartApi) {
                        cartApi.removeItem(itemId);
                    }
                    calculateTotals();
                    updateEmptyCartState();
                }, 300);
            }
        });
    });
    
    // Delete selected items
    const deleteSelectedBtn = document.querySelector('.delete-selected');
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const selectedItems = document.querySelectorAll('.item-check:checked');
            
            if (selectedItems.length === 0) {
                alert('Please select items to delete');
                return;
            }
            
            if (confirm(`Are you sure you want to remove ${selectedItems.length} item(s) from your cart?`)) {
                selectedItems.forEach(checkbox => {
                    const cartItem = checkbox.closest('.cart-item');
                    const itemId = cartItem?.dataset?.itemId;
                    cartItem.style.opacity = '0';
                    cartItem.style.transform = 'translateX(20px)';
                    
                    setTimeout(() => {
                        cartItem.remove();
                        if (cartApi && itemId) {
                            cartApi.removeItem(itemId);
                        }
                        calculateTotals();
                        updateEmptyCartState();
                    }, 300);
                });
            }
        });
    }
    
    // Promo code
    const applyPromoBtn = document.getElementById('apply-promo');
    if (applyPromoBtn) {
        applyPromoBtn.addEventListener('click', function() {
            const promoCode = document.getElementById('promo-code-input').value.trim();
            
            if (promoCode === '') {
                alert('Please enter a promo code');
                return;
            }
            
            // TODO: Validate promo code with backend
            alert('Promo code feature coming soon!');
        });
    }
    
    syncStorageFromDomWhenDifferent();
    document.querySelectorAll('.cart-item').forEach(syncQtyToStorage);
    if (cartApi) {
        cartApi.updateBadges();
    }

    // Initialize totals
    calculateTotals();
});
</script>

@endsection
