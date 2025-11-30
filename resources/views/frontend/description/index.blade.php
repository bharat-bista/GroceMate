@extends('frontend.layouts.main')

@section('main-content')

<section class="product-detail-section py-5">
    <div class="container">
        <div class="row">

            <!-- Left Image Gallery -->
            <div class="col-lg-4 mb-4">

                <div class="text-center mb-3">
                    <div class="image-zoom-container">
                        <img id="mainProductImage"
                             src="{{ asset('assets/img/product/product1.jpg') }}"
                             class="img-fluid rounded shadow-sm main-product-image"
                             alt="Product">

                        <div class="thumbnail-container mt-3">
                            <img src="{{ asset('assets/img/product/product1.jpg') }}"

                                 class="product-thumb active-thumb">
                            <img src="{{ asset('assets/img/product/product2.jpg') }}"
                                 class="product-thumb">
                            <img src="{{ asset('assets/img/product/product3.jpg') }}"
                                 class="product-thumb">
                        </div>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="my-3">
                    <label class="form-label fw-bold">QUANTITY:</label>
                    <div class="qty-box">
                        <button class="qty-btn" onclick="adjustQty(-1)">−</button>
                        <input type="text" id="qtyInput" value="1" readonly>
                        <button class="qty-btn" onclick="adjustQty(1)">+</button>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn frontend-add-cart">Add To Cart</button>
                    <button class="buy-now-btn frontend-buy-now">Buy Now</button>
                </div>

            </div>

            <!-- Right Product Info -->
            <div class="col-lg-8">
                <h2 class="fw-bold text-dark">Sunflower Oil</h2>

                <div class="mb-2">
                    <span class="text-danger h4 fw-bold">Rs. 499</span>
                    <del class="text-muted">Rs. 650</del>
                    <span class="badge text-white ms-2" style="background:#0A0F2C;">-25% OFF</span>
                </div>

                <h4 class="fw-bold mt-3">Brand: Sunflow</h4>

                <!-- Description -->
                <div class="product-description-wrapper">
                    <p id="productDescription" class="clamp-text">
                        1 litre sunflower oil 1 litre sunflower oil 1 litre sunflower oil1 litre sunflower oil
                    </p>
                </div>

            </div>

        </div>
    </div>
</section>

<!-- Similar Products -->
<section class="featured-product-section mt-4">
    <h1 class="featured-label">Similar Products</h1>

    <div class="product-grid">

        <!-- STATIC CARD 1 -->
        <div class="product-card">
            <div class="offer-badge">20% OFF</div>
            <img src="{{ asset('assets/img/product/product2.jpg') }}" class="product-img">
            <h4 class="product-title">Similar Product 1</h4>
            <p class="product-description">1 litre sunflower oil</p>

            <div class="price-section">
                <span class="old-price">Rs 500</span>
                <span class="new-price">Rs 400</span>
            </div>

            <div class="btn-group-wrapper">
    <a href="{{ route('checkout') }}" 
       class="add-to-cart-btn frontend-add-cart"
       data-id="101"
       data-name="Similar Product 1"
       data-price="400"
       data-image="https://via.placeholder.com/300x300?text=Similar+1">
        Add To Cart
    </a>




                <button class="buy-now-btn frontend-buy-now"
                        data-id="101"
                        data-name="Similar Product 1"
                        data-price="400"
                        data-image="https://via.placeholder.com/300x300?text=Similar+1">
                    Buy Now
                </button>
            </div>
        </div>

    </div>
</section>

<script>
/* ----------------------------- */
/* Thumbnail Switcher            */
/* ----------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    const mainImage = document.getElementById('mainProductImage');
    const thumbs = document.querySelectorAll('.product-thumb');

    thumbs.forEach(t => {
        t.addEventListener('click', function () {
            mainImage.src = this.src;
            thumbs.forEach(x => x.classList.remove('active-thumb'));
            this.classList.add('active-thumb');
        });
    });
});

/* ----------------------------- */
/* Quantity Adjust               */
/* ----------------------------- */
function adjustQty(change) {
    const input = document.getElementById('qtyInput');
    let qty = parseInt(input.value) + change;
    if (qty < 1) qty = 1;
    input.value = qty;
}

/* ----------------------------- */
/* Add to Cart (Frontend Only)   */
/* ----------------------------- */
function addToLocalCart(product) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    let exist = cart.find(i => i.id === product.id);
    if (exist) {
        exist.qty += product.qty;
    } else {
        cart.push(product);
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    alert("Added to cart (Local only)");
}

/* ----------------------------- */
/* ADD TO CART BUTTON            */
/* ----------------------------- */
document.addEventListener("click", e => {
    if (e.target.classList.contains("frontend-add-cart")) {

        const qty = parseInt(document.getElementById("qtyInput").value);

        const product = {
            id: e.target.dataset.id || "999",
            name: e.target.dataset.name || "Sample Product",
            price: parseFloat(e.target.dataset.price || 499),
            image: e.target.dataset.image || "https://via.placeholder.com/500x500?text=Product",
            qty: qty
        };

        addToLocalCart(product);
    }
});

/* ----------------------------- */
/* BUY NOW (Frontend Only)       */
/* ----------------------------- */
document.addEventListener("click", e => {
    if (e.target.classList.contains("frontend-buy-now")) {

        const qty = parseInt(document.getElementById("qtyInput").value);

        const product = {
            id: e.target.dataset.id || "999",
            name: e.target.dataset.name || "Sample Product",
            price: parseFloat(e.target.dataset.price || 499),
            image: e.target.dataset.image || "https://via.placeholder.com/500x500?text=Product",
            qty: qty
        };

        localStorage.setItem("buynow", JSON.stringify(product));

        window.location.href = "/checkout.html";  // front-end only
    }
});

/* ----------------------------- */
/* Image Zoom Effect             */
/* ----------------------------- */
document.addEventListener("DOMContentLoaded", function () {
    const zoomImg = document.getElementById("mainProductImage");

    zoomImg.addEventListener("mousemove", function (e) {
        const rect = zoomImg.getBoundingClientRect();
        let x = ((e.pageX - rect.left) / rect.width) * 100;
        let y = ((e.pageY - rect.top) / rect.height) * 100;

        zoomImg.style.transformOrigin = `${x}% ${y}%`;
        zoomImg.style.transform = "scale(2)";
    });

    zoomImg.addEventListener("mouseleave", function () {
        zoomImg.style.transform = "scale(1)";
    });
});
</script>

@endsection
