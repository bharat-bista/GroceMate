@extends('frontend.layouts.main')

@section('main-content')

<style>
    /* Existing styles for description page */
    .product-detail-section {
        background: #f8f9fa;
    }

    .image-zoom-container {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .main-product-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .thumbnail-container {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        padding-bottom: 4px;
    }

    .thumbnail-container.thumbs-fit {
        justify-content: center;
    }

    .thumbnail-container.thumbs-scroll {
        justify-content: flex-start;
        overflow-x: auto;
        overflow-y: hidden;
        scrollbar-width: thin;
        scrollbar-color: var(--gm-primary) #dfe5df;
        scroll-behavior: smooth;
        scroll-snap-type: x proximity;
    }

    .thumbnail-container.thumbs-scroll::-webkit-scrollbar {
        height: 6px;
    }

    .thumbnail-container.thumbs-scroll::-webkit-scrollbar-track {
        background: #dfe5df;
        border-radius: 999px;
    }

    .thumbnail-container.thumbs-scroll::-webkit-scrollbar-thumb {
        background: var(--gm-primary);
        border-radius: 999px;
    }

    .thumb-btn {
        flex: 0 0 82px;
        width: 82px;
        height: 82px;
        padding: 0;
        border: 2px solid transparent;
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .thumbnail-container.thumbs-scroll .thumb-btn {
        scroll-snap-align: start;
    }

    .product-thumb {
        object-fit: cover;
        width: 100%;
        height: 100%;
        border-radius: 8px;
        display: block;
    }

    .thumb-btn:hover {
        border-color: var(--gm-primary);
        transform: scale(1.05);
    }

    .thumb-btn.active-thumb {
        border-color: var(--gm-primary);
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);
    }

    .image-zoom-container.single-image-gallery .main-product-image {
        object-fit: contain;
        background: #fff;
    }

    .qty-control {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
        flex-wrap: nowrap;
    }

    .qty-control .qty-label {
        display: inline-block;
        margin: 0;
        font-size: 1.35rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        text-transform: none;
        color: #1f2937;
        line-height: 1;
    }

    .qty-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 2px solid #cfd8cf;
        border-radius: 14px;
        overflow: hidden;
        width: 190px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(27, 94, 32, 0.08);
    }

    .qty-box .qty-btn {
        background: #ffffff;
        color: var(--gm-primary-dark);
        border: none;
        width: 54px;
        height: 50px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 26px;
        font-weight: 700;
        line-height: 1;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .qty-box .qty-btn:first-child {
        border-right: 1px solid #d9e1d9;
    }

    .qty-box .qty-btn:last-child {
        border-left: 1px solid #d9e1d9;
    }

    .qty-box .qty-btn:hover,
    .qty-box .qty-btn:active,
    .qty-box .qty-btn:focus-visible {
        background: var(--gm-primary);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: inset 0 0 0 1px rgba(27, 94, 32, 0.1);
        outline: none;
    }

    #qtyInput {
        border: none;
        text-align: center;
        width: 82px;
        height: 50px;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gm-dark);
        background: #f8fbf8;
        outline: none;
    }

    .btn-group-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 18px;
    }

    .btn-group-wrapper .add-to-cart-btn,
    .btn-group-wrapper .buy-now-btn {
        flex: 1;
        border: none;
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 1.08rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .btn-group-wrapper .add-to-cart-btn {
        background: var(--gm-primary);
        color: #fff;
        box-shadow: 0 10px 20px rgba(46, 125, 50, 0.25);
    }

    .btn-group-wrapper .add-to-cart-btn:hover {
        background: var(--gm-primary-dark);
        transform: translateY(-2px);
    }

    .btn-group-wrapper .buy-now-btn {
        background: #0A0F2C;
        color: #fff;
        box-shadow: 0 10px 20px rgba(10, 15, 44, 0.25);
    }

    .btn-group-wrapper .buy-now-btn:hover {
        background: #1a2340;
        transform: translateY(-2px);
    }

    

    /* Responsive */
    @media (max-width: 768px) {
        .gm-products-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            padding: 10px;
        }
        
        .gm-product-img-wrap {
            height: 170px;
        }
        
        .gm-price-new {
            font-size: 1.45rem;
        }

        .main-product-image {
            height: 300px;
        }

        .thumbnail-container {
            gap: 8px;
        }

        .thumb-btn {
            flex-basis: 68px;
            width: 68px;
            height: 68px;
        }

        .btn-group-wrapper {
            gap: 10px;
        }

        .btn-group-wrapper .add-to-cart-btn,
        .btn-group-wrapper .buy-now-btn {
            padding: 12px 14px;
            font-size: 0.98rem;
        }

        .qty-box {
            width: 160px;
        }

        .featured-label {
            font-size: 1.5rem;
        }
    }

/* ==========================================
   PROMO BANNERS
   ========================================== */
.gm-promo-section {
    padding: 40px 5%;
    background: var(--gm-light);
}

.gm-promo-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    max-width: 1400px;
    margin: 0 auto;
}

.gm-promo-card {
    position: relative;
    border-radius: var(--gm-radius);
    overflow: hidden;
    height: 250px;
    cursor: pointer;
}

.gm-promo-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.gm-promo-card:hover img {
    transform: scale(1.05);
}

.gm-promo-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(46, 125, 50, 0.9) 0%, rgba(46, 125, 50, 0.6) 100%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 30px;
    color: var(--gm-white);
}

.gm-promo-card:nth-child(2) .gm-promo-overlay {
    background: linear-gradient(135deg, rgba(255, 107, 53, 0.9) 0%, rgba(255, 107, 53, 0.6) 100%);
}

.gm-promo-tag {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 10px;
    opacity: 0.9;
}

.gm-promo-title {
    font-size: 1.8rem;
    font-weight: 800;
    margin-bottom: 15px;
}

.gm-promo-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--gm-white);
    font-weight: 600;
    text-decoration: none;
    transition: var(--gm-transition);
}

.gm-promo-btn:hover {
    gap: 15px;
}

</style>

@php
    $product = $selectedProduct->product;
    $displayPrice = (float) ($selectedProduct->display_price ?? 0);
    $mrp = (float) ($selectedProduct->mrp ?? 0);
    $previousPrice = !is_null($selectedProduct->previous_price) && (float) $selectedProduct->previous_price > 0
        ? (float) $selectedProduct->previous_price
        : $mrp;
    $discountPercent = (float) ($selectedProduct->discount_percent ?? 0);
    $brandName = $product?->brandRelation?->name ?? 'N/A';
    $mainImagePath = $galleryPaths->first();
    $mainImageUrl = $mainImagePath ? asset('storage/' . $mainImagePath) : asset('assets/img/product/product1.jpg');
    $galleryImageUrls = $galleryPaths->map(fn ($path) => asset('storage/' . $path));

    if ($galleryImageUrls->isEmpty()) {
        $galleryImageUrls = collect([$mainImageUrl]);
    }

    $galleryImageCount = $galleryImageUrls->count();
    $isSingleGalleryImage = $galleryImageCount === 1;
    $thumbLayoutClass = $galleryImageCount > 3 ? 'thumbs-scroll' : 'thumbs-fit';
@endphp

<section class="product-detail-section py-5">
    <div class="container">
        <div class="row">

            <div class="col-lg-4 mb-4">
                <div class="text-center mb-3">
                    <div class="image-zoom-container {{ $isSingleGalleryImage ? 'single-image-gallery' : '' }}">
                        <img id="mainProductImage"
                             src="{{ $mainImageUrl }}"
                             class="img-fluid rounded shadow-sm main-product-image"
                             alt="{{ $product?->name ?? 'Product' }}">

                        <div class="thumbnail-container {{ $thumbLayoutClass }} mt-3">
                            @foreach($galleryImageUrls as $galleryIndex => $galleryImageUrl)
                                <button type="button"
                                        class="thumb-btn {{ $galleryIndex === 0 ? 'active-thumb' : '' }}"
                                        data-image-src="{{ $galleryImageUrl }}"
                                        aria-label="View image {{ $galleryIndex + 1 }}">
                                    <img src="{{ $galleryImageUrl }}"
                                         loading="lazy"
                                         decoding="async"
                                         class="product-thumb"
                                         alt="{{ $product?->name ?? 'Product image' }}">
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="my-3 qty-control">
                    <label class="form-label qty-label">Quantity</label>
                    <div class="qty-box">
                        <button class="qty-btn" onclick="adjustQty(-1)">-</button>
                        <input type="text" id="qtyInput" value="1" readonly>
                        <button class="qty-btn" onclick="adjustQty(1)">+</button>
                    </div>
                </div>

                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn frontend-add-cart"
                            data-id="{{ $selectedProduct->id }}"
                            data-name="{{ $product?->name }}"
                            data-price="{{ $displayPrice }}"
                            data-image="{{ $mainImageUrl }}">
                        Add To Cart
                    </button>
                    <button class="buy-now-btn frontend-buy-now"
                            data-id="{{ $selectedProduct->id }}"
                            data-name="{{ $product?->name }}"
                            data-price="{{ $displayPrice }}"
                            data-image="{{ $mainImageUrl }}">
                        Buy Now
                    </button>
                </div>
            </div>

            <div class="col-lg-8">
                <h2 class="fw-bold text-dark">{{ $product?->name ?? 'Product' }}</h2>

                <div class="mb-2">
                    <span class="text-danger h2 fw-bold">Rs. {{ number_format($displayPrice, 2) }}</span>
                    @if($previousPrice > 0)
                        <del class="text-muted">Rs. {{ number_format($previousPrice, 2) }}</del>
                    @endif
                    @if($discountPercent > 0)
                        <span class="badge text-white ms-2" style="background:#0A0F2C;">-{{ rtrim(rtrim(number_format($discountPercent, 2, '.', ''), '0'), '.') }}% OFF</span>
                    @endif
                </div>

                <h4 class="fw-bold mt-3">Brand: {{ $brandName }}</h4>

                <div class="product-description-wrapper">
                    @if(!empty(trim((string) $selectedProduct->description)))
                        <div id="productDescription" class="clamp-text">{!! $selectedProduct->description !!}</div>
                    @else
                        <p id="productDescription" class="clamp-text text-muted mb-0">
                            No product description available right now.
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ==========================================
    TOP SALE SECTION
    ========================================== --}}
<section class="gm-section gm-fade-in">
    <div class="gm-section-header">
        <div class="gm-top-sale-title-wrap">
            <div class="gm-top-sale-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="gm-top-sale-heading">
                <span class="gm-top-sale-label">Trending Now</span>
                <h2 class="gm-top-sale-title">Flash Sale</h2>
                <p class="gm-top-sale-subtitle">Best-selling products loved by our shoppers this week.</p>
            </div>
        </div>
        <a href="#" class="gm-view-all">View All <i class="fas fa-arrow-right"></i></a>
    </div>

    <div class="gm-products-scroll">
        @forelse($topSaleProducts as $topSaleProduct)
            @php
                $topSaleItem = $topSaleProduct->product;
                $topSaleDiscount = (float) ($topSaleProduct->discount_percent ?? 0);
                $topSaleOldPrice = !is_null($topSaleProduct->previous_price) && (float) $topSaleProduct->previous_price > 0
                    ? (float) $topSaleProduct->previous_price
                    : (float) ($topSaleProduct->mrp ?? 0);
            @endphp

            @if($topSaleItem)
                <div class="gm-product-card gm-ecom-card">
                    <span class="gm-product-badge">{{ rtrim(rtrim(number_format($topSaleDiscount, 2, '.', ''), '0'), '.') }}% OFF</span>
                    <span class="gm-cart-icon-badge"
                          data-product-id="{{ $topSaleProduct->id }}"
                          data-product-name="{{ $topSaleItem->name }}"
                          data-product-price="{{ (float) ($topSaleProduct->display_price ?: $topSaleProduct->mrp) }}"
                          data-product-image="{{ $topSaleProduct->thumbnail ? asset('storage/' . $topSaleProduct->thumbnail) : asset('assets/img/product/product1.jpg') }}"
                          title="Add to cart">
                        <i class="fas fa-shopping-cart"></i>
                    </span>
                    <a href="{{ route('description', $topSaleProduct->id) }}" class="gm-product-card-link">
                        <div class="gm-product-img-wrap">
                            @if($topSaleProduct->thumbnail)
                                <img src="{{ asset('storage/' . $topSaleProduct->thumbnail) }}" alt="{{ $topSaleItem->name }}" loading="lazy" decoding="async">
                            @else
                                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="{{ $topSaleItem->name }}" loading="lazy" decoding="async">
                            @endif
                        </div>
                        <div class="gm-product-info">
                            <h3 class="gm-product-name">{{ $topSaleItem->name }}</h3>
                            <div class="gm-product-price">
                                <span class="gm-price-new">Rs.{{ number_format((float) $topSaleProduct->display_price, 2) }}</span>
                            </div>
                            <div class="gm-ecom-discount">
                                <span class="gm-price-old">Rs.{{ number_format($topSaleOldPrice, 2) }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
        @empty
            <div class="gm-product-card gm-ecom-card" style="min-width: 100%; text-align: center; padding: 24px;">
                <p style="margin: 0; color: var(--gm-gray);">No related products available right now.</p>
            </div>
        @endforelse
    </div>
</section>

<script>
/* ----------------------------- */
/* Thumbnail Switcher            */
/* ----------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    const mainImage = document.getElementById('mainProductImage');
    const thumbButtons = document.querySelectorAll('.thumb-btn');

    thumbButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const imageSrc = this.dataset.imageSrc;
            if (imageSrc) {
                mainImage.src = imageSrc;
            }

            thumbButtons.forEach((item) => item.classList.remove('active-thumb'));
            this.classList.add('active-thumb');

            if (this.parentElement?.classList.contains('thumbs-scroll')) {
                this.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }
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
    if (window.GroceMateCart && typeof window.GroceMateCart.addItem === 'function') {
        const result = window.GroceMateCart.addItem({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            qty: product.qty || 1
        });

        if (result.added) {
            window.GroceMateCart.showToast('Product added to cart', 'success');
        } else if (result.reason === 'exists') {
            window.GroceMateCart.showToast('This product is already in cart', 'warning');
        } else {
            window.GroceMateCart.showToast('Unable to add product to cart', 'error');
        }

        window.GroceMateCart.updateBadges();
        return;
    }

    alert("Cart service unavailable");
}

/* ----------------------------- */
/* ADD TO CART BUTTON            */
/* ----------------------------- */
document.addEventListener("click", e => {
    const addCartBtn = e.target.closest('.frontend-add-cart');
    if (addCartBtn) {

        const qty = parseInt(document.getElementById("qtyInput").value);

        const product = {
            id: addCartBtn.dataset.id || "999",
            name: addCartBtn.dataset.name || "Sample Product",
            price: parseFloat(addCartBtn.dataset.price || 499),
            image: addCartBtn.dataset.image || "https://via.placeholder.com/500x500?text=Product",
            qty: qty
        };

        addToLocalCart(product);
    }
});

/* ----------------------------- */
/* BUY NOW (Frontend Only)       */
/* ----------------------------- */
document.addEventListener("click", e => {
    const buyNowBtn = e.target.closest('.frontend-buy-now');
    if (buyNowBtn) {

        const qty = parseInt(document.getElementById("qtyInput").value);

        const product = {
            id: buyNowBtn.dataset.id || "999",
            name: buyNowBtn.dataset.name || "Sample Product",
            price: parseFloat(buyNowBtn.dataset.price || 499),
            image: buyNowBtn.dataset.image || "https://via.placeholder.com/500x500?text=Product",
            qty: qty
        };

        if (window.GroceMateCart && typeof window.GroceMateCart.setBuyNowItem === 'function') {
            const result = window.GroceMateCart.setBuyNowItem(product);
            if (!result.saved) {
                window.GroceMateCart.showToast('Unable to start direct checkout', 'error');
                return;
            }
        } else {
            localStorage.setItem('gm_buy_now_item', JSON.stringify(product));
            localStorage.setItem('buynow', JSON.stringify(product));
        }

        window.location.href = "{{ route('checkout') }}?mode=buy-now";
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
