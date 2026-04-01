@extends('frontend.layouts.main')

@section('main-content')
{{-- ============================================= --}}
{{-- DARAZ-STYLE CART PAGE - MODERN DESIGN --}}
{{-- ============================================= --}}

<style>
/* ==========================================
   DARAZ CART - CUSTOM STYLES
   ========================================== */
:root {
    --daraz-orange: #F85606;
    --daraz-orange-dark: #D94B05;
    --daraz-blue: #1A9CB7;
    --daraz-gray-bg: #F5F5F5;
    --daraz-text: #212121;
    --daraz-text-light: #757575;
    --daraz-border: #EEEEEE;
    --daraz-star: #FFC107;
}

/* Container */
.cart-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px 15px;
    background: var(--daraz-gray-bg);
    min-height: 100vh;
}

/* Page Header */
.cart-header {
    background: white;
    padding: 20px 25px;
    border-radius: 4px;
    margin-bottom: 20px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

.cart-header h1 {
    font-size: 1.75rem;
    color: var(--daraz-text);
    margin: 0;
    font-weight: 600;
}

.cart-breadcrumb {
    font-size: 0.875rem;
    color: var(--daraz-text-light);
    margin-top: 8px;
}

.cart-breadcrumb a {
    color: var(--daraz-blue);
    text-decoration: none;
}

.cart-breadcrumb a:hover {
    text-decoration: underline;
}

/* Layout */
.cart-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 20px;
}

/* Cart Items Section */
.cart-items-section {
    background: white;
    border-radius: 4px;
    padding: 20px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

.cart-items-header {
    border-bottom: 2px solid var(--daraz-border);
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.cart-items-header h2 {
    font-size: 1.25rem;
    color: var(--daraz-text);
    margin: 0;
    font-weight: 600;
}

.cart-items-count {
    color: var(--daraz-text-light);
    font-size: 0.9rem;
}

/* Cart Item Card */
.cart-item {
    display: flex;
    gap: 20px;
    padding: 25px 0;
    border-bottom: 1px solid var(--daraz-border);
    transition: opacity 0.3s, transform 0.3s;
}

.cart-item:last-child {
    border-bottom: none;
}

.item-checkbox {
    display: flex;
    align-items: flex-start;
    padding-top: 10px;
}

.item-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--daraz-orange);
}

.item-image {
    width: 100px;
    height: 100px;
    border: 1px solid var(--daraz-border);
    border-radius: 4px;
    overflow: hidden;
    flex-shrink: 0;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.item-title {
    font-size: 2.2rem;
    color: var(--daraz-text);
    font-weight: 600;
    line-height: 1.5;
    margin: 0;
}

.item-title:hover {
    color: var(--daraz-orange);
    cursor: pointer;
}

.item-brand {
    font-size: 0.9rem;
    color: var(--daraz-text-light);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.item-rating {
    display: flex;
    align-items: center;
    gap: 5px;
}

.item-rating .stars {
    display: flex;
    gap: 2px;
}

.item-rating .stars i {
    color: var(--daraz-star);
    font-size: 0.9rem;
}

.item-rating .count {
    font-size: 0.9rem;
    color: var(--daraz-text-light);
    font-weight: 500;
}

.item-price-section {
    display: flex;
    align-items: center;
    gap: 12px;
}

.item-price {
    font-size: 1.5rem;
    color: var(--daraz-orange);
    font-weight: 700;
}

.item-original-price {
    font-size: 1rem;
    color: var(--daraz-text-light);
    text-decoration: line-through;
}

.item-discount {
    font-size: 0.9rem;
    color: var(--daraz-orange);
    font-weight: 600;
    background: rgba(248, 86, 6, 0.1);
    padding: 2px 8px;
    border-radius: 4px;
}

.item-actions {
    display: flex;
    align-items: center;
    gap: 20px;
}

.item-quantity {
    display: flex;
    align-items: center;
    border: 1px solid var(--daraz-border);
    border-radius: 4px;
}

.qty-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: white;
    color: var(--daraz-text);
    cursor: pointer;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.qty-btn:hover {
    background: var(--daraz-gray-bg);
}

.qty-input {
    width: 60px;
    height: 36px;
    text-align: center;
    border: none;
    border-left: 1px solid var(--daraz-border);
    border-right: 1px solid var(--daraz-border);
    font-size: 1rem;
    font-weight: 600;
}

.item-remove {
    color: var(--daraz-text-light);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 0.95rem;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.item-remove:hover {
    color: var(--daraz-orange);
}

/* Order Summary Sidebar */
.order-summary {
    background: white;
    border-radius: 4px;
    padding: 20px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.order-summary h3 {
    font-size: 1.15rem;
    color: var(--daraz-text);
    margin: 0 0 20px 0;
    font-weight: 600;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 0.95rem;
}

.summary-row .label {
    color: var(--daraz-text-light);
}

.summary-row .value {
    color: var(--daraz-text);
    font-weight: 600;
}

.summary-divider {
    height: 1px;
    background: var(--daraz-border);
    margin: 20px 0;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    font-size: 1.1rem;
}

.summary-total .label {
    color: var(--daraz-text);
    font-weight: 600;
}

.summary-total .value {
    color: var(--daraz-orange);
    font-weight: 700;
    font-size: 1.3rem;
}

.checkout-btn {
    width: 100%;
    padding: 14px;
    background: var(--daraz-orange);
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.checkout-btn:hover {
    background: var(--daraz-orange-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(248, 86, 6, 0.3);
}

.continue-shopping {
    width: 100%;
    padding: 12px;
    background: white;
    color: var(--daraz-orange);
    border: 1px solid var(--daraz-orange);
    border-radius: 4px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.3s ease;
}

.continue-shopping:hover {
    background: var(--daraz-orange);
    color: white;
}

.promo-code {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--daraz-border);
}

.promo-code h4 {
    font-size: 0.95rem;
    color: var(--daraz-text);
    margin: 0 0 10px 0;
    font-weight: 600;
}

.promo-input-group {
    display: flex;
    gap: 10px;
}

.promo-input-group input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid var(--daraz-border);
    border-radius: 4px;
    font-size: 0.9rem;
}

.promo-input-group button {
    padding: 10px 20px;
    background: var(--daraz-blue);
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
}

.promo-input-group button:hover {
    background: #178ca1;
}

/* Empty Cart */
.empty-cart {
    background: white;
    border-radius: 4px;
    padding: 60px 20px;
    text-align: center;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

.empty-cart-icon {
    font-size: 5rem;
    color: var(--daraz-text-light);
    margin-bottom: 20px;
}

.empty-cart h2 {
    font-size: 1.5rem;
    color: var(--daraz-text);
    margin-bottom: 10px;
}

.empty-cart p {
    color: var(--daraz-text-light);
    margin-bottom: 30px;
}

.empty-cart-btn {
    display: inline-block;
    padding: 14px 40px;
    background: var(--daraz-orange);
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.empty-cart-btn:hover {
    background: var(--daraz-orange-dark);
    transform: translateY(-2px);
}

/* Select All */
.select-all-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background: var(--daraz-gray-bg);
    border-radius: 4px;
    margin-bottom: 20px;
}

.select-all-bar input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--daraz-orange);
}

.select-all-bar label {
    font-size: 0.95rem;
    color: var(--daraz-text);
    font-weight: 500;
    cursor: pointer;
    flex: 1;
}

.delete-selected {
    background: none;
    border: none;
    color: var(--daraz-text-light);
    cursor: pointer;
    font-size: 0.9rem;
}

.delete-selected:hover {
    color: var(--daraz-orange);
}

/* Responsive */
@media (max-width: 1024px) {
    .cart-layout {
        grid-template-columns: 1fr;
    }
    
    .order-summary {
        position: static;
    }
}

@media (max-width: 768px) {
    .cart-item {
        flex-direction: column;
    }
    
    .item-checkbox {
        order: -1;
    }
    
    .item-image {
        width: 100%;
        height: 200px;
    }
    
    .item-actions {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 480px) {
    .cart-container {
        padding: 10px;
    }
    
    .cart-header {
        padding: 15px;
    }
    
    .cart-items-section,
    .order-summary {
        padding: 15px;
    }
    
    .summary-row,
    .summary-total {
        font-size: 0.9rem;
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
                
                <div class="summary-row">
                    <span class="label">Subtotal</span>
                    <span class="value" id="subtotal">Rs.0</span>
                </div>
                
                <div class="summary-row">
                    <span class="label">Discount</span>
                    <span class="value" id="discount">- Rs.0</span>
                </div>
                
                <div class="summary-row">
                    <span class="label">Delivery Fee</span>
                    <span class="value" id="delivery-fee">Rs.50</span>
                </div>
                
                <div class="summary-divider"></div>
                
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
    const deliveryFee = 50;
    
    // Calculate totals
    function calculateTotals() {
        let subtotal = 0;
        let originalTotal = 0;
        
        document.querySelectorAll('.item-check:checked').forEach(checkbox => {
            const price = parseFloat(checkbox.dataset.price);
            const qty = parseInt(checkbox.dataset.qty);
            const cartItem = checkbox.closest('.cart-item');
            const originalPrice = parseFloat(cartItem.querySelector('.item-original-price').textContent.replace('Rs.', ''));
            
            subtotal += price * qty;
            originalTotal += originalPrice * qty;
        });
        
        const discount = originalTotal - subtotal;
        const total = subtotal + (subtotal > 0 ? deliveryFee : 0);
        
        document.getElementById('subtotal').textContent = `Rs.${subtotal}`;
        document.getElementById('discount').textContent = `- Rs.${discount}`;
        document.getElementById('delivery-fee').textContent = subtotal > 0 ? `Rs.${deliveryFee}` : 'Rs.0';
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
                calculateTotals();
                // TODO: Update quantity in backend/session
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
                calculateTotals();
                // TODO: Update quantity in backend/session
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
                    calculateTotals();
                    
                    // Check if cart is empty
                    if (document.querySelectorAll('.cart-item').length === 0) {
                        location.reload(); // Reload to show empty cart
                    }
                }, 300);
                
                // TODO: Remove item from backend/session
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
                    cartItem.style.opacity = '0';
                    cartItem.style.transform = 'translateX(20px)';
                    
                    setTimeout(() => {
                        cartItem.remove();
                        
                        if (document.querySelectorAll('.cart-item').length === 0) {
                            location.reload();
                        } else {
                            calculateTotals();
                        }
                    }, 300);
                });
                
                // TODO: Remove items from backend/session
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
    
    // Initialize totals
    calculateTotals();
});
</script>

@endsection
