<style>
.offer-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #0A0F2C;
    color: white;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: bold;
    border-radius: 4px;
    z-index: 1;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    white-space: nowrap;
}
/* === Text & Price Styling === */
.product-title {
    font-size: 16px;
    font-weight: 600;
    margin: 2px 0;
    color: #111;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-sku {
    font-size: 14px;
    font-weight: 600;
    margin: 2px 0;
    color: #222;
}

.product-description {
    font-size: 13px;
    color: #555;
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 34px;
}

.price-section {
    margin: 4px 0 6px 0;
}

.old-price {
    text-decoration: line-through;
    color: #888;
    font-size: 14px;
    margin-right: 5px;
}

.new-price {
    color: #f39c12;
    font-weight: bold;
    font-size: 16px;
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
    background-color: #0a0f36;
    color: #f39c12;
}

.add-to-cart-btn:hover {
    background-color: #1c2250;
    color:white;
}

.product-img {
    width: 100%;
    height: 160px;
    object-fit: contain;
    margin-bottom: 6px;
}

.product-card {
    position: relative;
    background: #fff;
    padding: 10px 12px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunny</h5>
                <h4 class="product-title">Sugar</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 100</span>
                    <span class="new-price">Rs 80</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunny</h5>
                <h4 class="product-title">Sugar</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 100</span>
                    <span class="new-price">Rs 80</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            

            


    <!-- Duplicate product cards as needed -->
    <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunny</h5>
                <h4 class="product-title">Sugar</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 100</span>
                    <span class="new-price">Rs 80</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>

</div>
