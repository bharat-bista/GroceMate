@extends('frontend.layouts.main')

@section('main-content')

<style>
    /* ==========================================
       CSS CUSTOM PROPERTIES - GREEN THEME (from homepage)
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
        --gm-shadow: 0 4px 20px rgba(46, 125, 50, 0.15);
        --gm-shadow-lg: 0 10px 40px rgba(46, 125, 50, 0.2);
        --gm-radius: 16px;
        --gm-radius-sm: 8px;
        --gm-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

/* ==========================================
   SECTION HEADERS (from homepage)
   ========================================== */
.gm-section {
    padding: 40px 5%;
    max-width: 1600px;
    margin: 0 auto;
}

.gm-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 20px;
}

.gm-top-sale-title-wrap {
    display: flex;
    align-items: center;
    gap: 14px;
}

.gm-top-sale-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: linear-gradient(135deg, #ff8a00, #ff4d4f);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 25px rgba(255, 122, 0, 0.25);
    font-size: 1.25rem;
}

.gm-top-sale-heading {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.gm-top-sale-label {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    padding: 5px 12px;
    border-radius: 999px;
    background: rgba(255, 107, 53, 0.12);
    color: var(--gm-accent);
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.gm-top-sale-title {
    margin: 0;
    font-size: clamp(1.8rem, 3vw, 2.4rem);
    font-weight: 800;
    line-height: 1.1;
    color: var(--gm-dark);
}

.gm-top-sale-subtitle {
    margin: 0;
    color: var(--gm-gray);
    font-size: 0.95rem;
}

.gm-view-all {
    color: var(--gm-primary);
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: var(--gm-transition);
}

.gm-view-all:hover {
    color: var(--gm-accent);
    gap: 12px;
}

/* ==========================================
   TOP SALE HORIZONTAL SCROLLER (from homepage)
   ========================================== */
.gm-products-scroll {
    display: flex;
    gap: 14px;
    background: #f5f5f5;
    padding: 10px;
    border-radius: 12px;
    overflow-x: auto;
    scroll-behavior: smooth;
    scrollbar-width: thin;
    scrollbar-color: var(--gm-primary) var(--gm-gray-light);
    position: relative;
}

.gm-products-scroll::-webkit-scrollbar {
    height: 6px;
}

.gm-products-scroll::-webkit-scrollbar-track {
    background: var(--gm-gray-light);
    border-radius: 3px;
}

.gm-products-scroll::-webkit-scrollbar-thumb {
    background: var(--gm-primary);
    border-radius: 3px;
}

.gm-products-scroll::-webkit-scrollbar-thumb:hover {
    background: var(--gm-primary-dark);
}

.gm-products-scroll .gm-product-card {
    min-width: 260px;
    max-width: 260px;
    flex-shrink: 0;
}

/* ==========================================
   PRODUCT CARDS - HOMEPAGE STYLE (matching home page)
   ========================================== */
.gm-product-card {
    background: var(--gm-white);
    border: 1px solid #f0f0f0;
    border-radius: var(--gm-radius);
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: var(--gm-transition);
    position: relative;
}

.gm-product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.gm-product-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: var(--gm-accent);
    color: var(--gm-white);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 700;
    z-index: 2;
}

.gm-product-img-wrap {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: #fafafa;
}

.gm-product-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.gm-product-card:hover .gm-product-img-wrap img {
    transform: scale(1.1);
}

.gm-product-card-link {
    color: inherit;
    text-decoration: none;
    display: block;
}

.gm-product-info {
    padding: 12px 12px 14px;
}

.gm-product-name {
    white-space: normal;
    overflow: hidden;
    text-overflow: unset;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.35;
    min-height: 2.8em;
    margin-bottom: 10px;
    font-size: 1rem;
    font-weight: 500;
    color: var(--gm-dark);
}

.gm-product-price {
    margin-bottom: 6px;
    gap: 8px;
    flex-wrap: wrap;
    display: flex;
    align-items: center;
}

.gm-price-new {
    font-size: 1.9rem;
    font-weight: 700;
    line-height: 1;
    color: #f57224;
}

.gm-price-old {
    font-size: 0.95rem;
    color: #9ca3af;
    text-decoration: line-through;
}

.gm-product-rating {
    display: flex;
    align-items: center;
    gap: 3px;
    margin-bottom: 0;
}

.gm-product-rating i {
    color: #faca51;
    font-size: 0.8rem;
}

.gm-product-rating span {
    color: #9ca3af;
    font-size: 0.85rem;
}

.gm-product-btns {
    display: none;
}

.gm-cart-icon-badge {
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

.gm-cart-icon-badge:hover {
    background: #218838;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.5);
}

/* More specific selector for ecom cards to override any conflicts */
.gm-ecom-card .gm-cart-icon-badge {
    position: absolute !important;
    top: 10px !important;
    right: 10px !important;
    background: #28a745 !important;
    color: white !important;
    width: 35px !important;
    height: 35px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 1rem !important;
    z-index: 10 !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3) !important;
}

.gm-ecom-card .gm-cart-icon-badge:hover {
    background: #218838 !important;
    transform: scale(1.1) !important;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.5) !important;
}

/* Product Grid Layout */
    .gm-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 14px;
        background: #f5f5f5;
        padding: 14px;
        border-radius: 12px;
    }

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

/* ==========================================
   PRODUCT GRID / ECOMMERCE SECTION
   ========================================== */
.gm-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 14px;
    background: #f5f5f5;
    padding: 14px;
    border-radius: 12px;
}

.gm-products-grid .gm-product-card {
    min-width: unset;
    max-width: unset;
}

.gm-ecom-card {
    background: #fff;
    border: 1px solid #f0f0f0;
    border-radius: 0;
    box-shadow: none;
    overflow: hidden;
    padding: 0;
    position: relative;
}

.gm-ecom-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.gm-ecom-card .gm-product-badge {
    top: 10px;
    left: 10px;
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 1rem;
    z-index: 10;
    background: #28a745;
    color: white;
    font-weight: 600;
}


.gm-ecom-card .gm-product-img-wrap {
    height: 180px;
    background: #fafafa;
    width: 100%;
    padding: 0;
    margin: 0;
}

.gm-ecom-card .gm-product-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gm-ecom-card .gm-product-card-link {
    color: inherit;
    text-decoration: none;
    display: block;
}

.gm-ecom-card .gm-product-info {
    padding: 12px 12px 14px;
}

.gm-ecom-card .gm-product-brand {
    display: none;
}

.gm-ecom-card .gm-product-name {
    white-space: normal;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.4;
    min-height: auto;
    margin-bottom: 10px;
    font-size: 1.5rem;
    font-weight: 500;
    color: #111;
}

.gm-ecom-card .gm-product-weight {
    display: none;
}

.gm-ecom-card .gm-product-price {
    margin-bottom: 0px;
    gap: 8px;
    flex-wrap: wrap;
    display: inline-block;
}

.gm-ecom-card .gm-price-new {
    font-size: 1.9rem;
    font-weight: 700;
    line-height: 1;
    color: #f57224;
}

.gm-ecom-card .gm-price-old {
    font-size: 1.4rem;
    color: #9ca3af;
}

.gm-ecom-discount {
    font-size: 1.4rem;
    color: #9ca3af;
    display: inline-block;
    margin-left: 10px;
}

/* Hide only the percentage text, not the old price */
.gm-ecom-discount {
    font-size: 0;
}

.gm-ecom-discount .gm-price-old {
    font-size: 1.4rem;
}

.gm-ecom-card .gm-product-rating {
    display: none !important;
}

.gm-ecom-card .gm-product-btns {
    display: none;
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
                                <img src="{{ asset('storage/' . $topSaleProduct->thumbnail) }}" alt="{{ $topSaleItem->name }}">
                            @else
                                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="{{ $topSaleItem->name }}">
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

        localStorage.setItem("buynow", JSON.stringify(product));

        window.location.href = "{{ route('checkout') }}";
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

