<style>
.offer-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #28a745;
    color: white;
    padding: 8px 16px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 4px;
    z-index: 10;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    white-space: nowrap;
}

.cart-icon-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #28a745;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    z-index: 10;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

.cart-icon-badge:hover {
    background: #218838;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.5);
}
/* === Text & Price Styling === */
.product-title {
    font-size: 1.5rem;
    font-weight: 500;
    margin: 10px 0;
    color: #111;
    white-space: normal;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.4;
}

.product-sku {
    display: none;
}

.product-description {
    display: none;
}

.price-section {
    margin: 0 0 8px 0;
    display: inline-block;
}

.old-price {
    text-decoration: line-through;
    color: #9ca3af;
    font-size: 1.4rem;
    margin-left: 10px;
}

.new-price {
    color: #f57224;
    font-weight: 700;
    font-size: 1.9rem;
    line-height: 1;
}

.btn-group-wrapper {
    display: flex;
    flex-direction: row;
    gap: 12px;
    width: 100%;
    margin-top: 8px;
}

.btn-group-wrapper form {
    flex: 1;
    display: flex;
}

.add-to-cart-btn,
.buy-now-btn {
    width: 100%;
    text-align: center;
    font-size: 13px;
    padding: 3px 18px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    transition: 0.3s ease;
}

.buy-now-btn {
    background-color: #f39c12;
    color: #fff;
}

.buy-now-btn:hover {
    background-color: #e08e0b;
}

.add-to-cart-btn {
    background-color: #28a745;
    color: white;
}

.add-to-cart-btn:hover {
    background-color: #218838;
    color: white;
}

.product-img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    margin-bottom: 0;
    background: #fafafa;
}

.product-card {
    position: relative;
    background: #fff;
    padding: 0;
    border-radius: 0;
    border: 1px solid #f0f0f0;
    box-shadow: none;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.product-card > * {
    padding: 0 12px;
}

.product-card .product-img {
    padding: 0;
    margin: 0;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
    }
    .product-title,
    .product-sku,
    .product-description,
    .old-price,
    .new-price {
        font-size: 13px;
    }
    .btn-group-wrapper .add-to-cart-btn,
    .btn-group-wrapper .buy-now-btn {
        font-size: 12px;
        padding: 6px 10px;
    }
    .offer-badge {
        font-size: 11px;
        padding: 3px 8px;
    }
}

@media (max-width: 576px) {
    .product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
    .product-card {
        padding: 12px;
    }
    .product-img {
        height: 120px;
    }
    .offer-badge {
        font-size: 10px;
        top: 6px;
        right: 6px;
        padding: 2px 6px;
    }
    .product-title,
    .product-sku {
        font-size: 12px;
    }
    .product-description {
        font-size: 11px;
        min-height: 36px;
        margin-bottom: 8px;
    }
    .old-price,
    .new-price {
        font-size: 12px;
    }
    .btn-group-wrapper {
        flex-direction: column;
        gap: 4px;
        margin-top: 6px;
    }
    .add-to-cart-btn,
    .buy-now-btn {
        padding: 5px;
        font-size: 11px;
    }
}
</style>

<div class="product-grid" style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px;">

    <!-- Example Product Card -->
    <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <div class="cart-icon-badge">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunny</h5>
                <h4 class="product-title">Sugar</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="new-price">Rs 80</span>
                    <span class="old-price">Rs 100</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <div class="cart-icon-badge">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunny</h5>
                <h4 class="product-title">Sugar</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="new-price">Rs 80</span>
                    <span class="old-price">Rs 100</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            

            


    <!-- Duplicate product cards as needed -->
    <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <div class="cart-icon-badge">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunny</h5>
                <h4 class="product-title">Sugar</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="new-price">Rs 80</span>
                    <span class="old-price">Rs 100</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>

</div>
