@extends('frontend.layouts.main')

@section('main-content')
{{-- ============================================= --}}
{{-- MODERN GROCEMATE HOME PAGE - GREEN THEME --}}
{{-- ============================================= --}}

<style>
/* ==========================================
   CSS CUSTOM PROPERTIES - GREEN THEME
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
   HERO SLIDER - MODERN DESIGN
   ========================================== */
.gm-hero {
    position: relative;
    width: 100%;
    height: 70vh;
    min-height: 500px;
    max-height: 700px;
    overflow: hidden;
    background: linear-gradient(135deg, var(--gm-primary) 0%, var(--gm-primary-dark) 100%);
}

.gm-hero-slider {
    position: relative;
    width: 100%;
    height: 100%;
}

.gm-hero-track {
    display: flex;
    height: 100%;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.gm-hero-slide {
    min-width: 100%;
    height: 100%;
    position: relative;
}

.gm-hero-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gm-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(46, 125, 50, 0.85) 0%, rgba(27, 94, 32, 0.7) 50%, transparent 100%);
    display: flex;
    align-items: center;
    padding: 0 8%;
}

.gm-hero-content {
    max-width: 600px;
    color: var(--gm-white);
    animation: slideUp 0.8s ease-out;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.gm-hero-badge {
    display: inline-block;
    background: var(--gm-accent);
    color: var(--gm-white);
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.gm-hero-title {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.gm-hero-text {
    font-size: 1.1rem;
    opacity: 0.95;
    margin-bottom: 30px;
    line-height: 1.6;
}

.gm-hero-btns {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.gm-btn {
    padding: 14px 32px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: var(--gm-transition);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: none;
    cursor: pointer;
}

.gm-btn-primary {
    background: var(--gm-white);
    color: var(--gm-primary);
}

.gm-btn-primary:hover {
    background: var(--gm-accent);
    color: var(--gm-white);
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 107, 53, 0.4);
}

.gm-btn-outline {
    background: transparent;
    color: var(--gm-white);
    border: 2px solid var(--gm-white);
}

.gm-btn-outline:hover {
    background: var(--gm-white);
    color: var(--gm-primary);
    transform: translateY(-3px);
}

/* Hero Navigation */
.gm-hero-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border: none;
    color: var(--gm-white);
    font-size: 1.2rem;
    cursor: pointer;
    transition: var(--gm-transition);
    z-index: 10;
}

.gm-hero-nav:hover {
    background: var(--gm-white);
    color: var(--gm-primary);
    transform: translateY(-50%) scale(1.1);
}

.gm-hero-prev { left: 20px; }
.gm-hero-next { right: 20px; }

.gm-hero-dots {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 12px;
    z-index: 10;
}

.gm-hero-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    cursor: pointer;
    transition: var(--gm-transition);
}

.gm-hero-dot.active {
    background: var(--gm-white);
    transform: scale(1.3);
}

/* ==========================================
   SERVICE BAR
   ========================================== */
.gm-services {
    background: var(--gm-white);
    padding: 30px 5%;
    box-shadow: var(--gm-shadow);
    position: relative;
    z-index: 5;
}

.gm-services-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    max-width: 1400px;
    margin: 0 auto;
}

.gm-service-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-radius: var(--gm-radius-sm);
    transition: var(--gm-transition);
}

.gm-service-item:hover {
    background: var(--gm-light);
    transform: translateY(-3px);
}

.gm-service-icon {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gm-primary-light), var(--gm-primary));
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gm-white);
    font-size: 1.3rem;
    flex-shrink: 0;
}

.gm-service-text h4 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--gm-dark);
    margin-bottom: 4px;
}

.gm-service-text p {
    font-size: 0.85rem;
    color: var(--gm-gray);
    margin: 0;
}

/* ==========================================
   SECTION HEADERS
   ========================================== */
.gm-section {
    padding: 40px 5%;
    max-width: 1600px;
    margin: 0 auto;
}

.gm-section:first-of-type {
    padding-top: 20px;
}

.gm-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 20px;
}

.gm-section-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--gm-dark);
    display: flex;
    align-items: center;
    gap: 12px;
}

.gm-section-title i {
    color: var(--gm-primary);
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
   PRODUCT CARDS - MODERN DESIGN
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

.gm-products-scroll.auto-scroll {
    overflow-x: hidden;
}

.gm-products-scroll.auto-scroll .gm-product-card {
    flex-shrink: 0;
}


.gm-product-card {
    min-width: 260px;
    max-width: 260px;
    background: var(--gm-white);
    border-radius: var(--gm-radius);
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: var(--gm-transition);
    position: relative;
}

.gm-product-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--gm-shadow-lg);
}

.gm-product-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: var(--gm-accent);
    color: var(--gm-white);
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 700;
    z-index: 2;
}

.gm-product-img-wrap {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: var(--gm-light);
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

.gm-product-info {
    padding: 20px;
}

.gm-product-brand {
    font-size: 0.8rem;
    color: var(--gm-gray);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 6px;
}

.gm-product-name {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--gm-dark);
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.gm-product-weight {
    font-size: 0.85rem;
    color: var(--gm-gray);
    margin-bottom: 10px;
}

.gm-product-rating {
    display: none !important;
}

.gm-product-price {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.gm-price-new {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--gm-primary);
}

.gm-price-old {
    font-size: 0.95rem;
    color: var(--gm-gray);
    text-decoration: line-through;
}

.gm-product-btns {
    display: flex;
    gap: 10px;
}

.gm-product-btns .gm-btn {
    flex: 1;
    padding: 12px 10px;
    font-size: 0.85rem;
    justify-content: center;
}

.gm-btn-cart {
    background: var(--gm-primary);
    color: var(--gm-white);
}

.gm-btn-cart:hover {
    background: var(--gm-primary-dark);
}

.gm-btn-buy {
    background: var(--gm-accent);
    color: var(--gm-white);
}

.gm-btn-buy:hover {
    background: var(--gm-accent-dark);
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

/* ==========================================
   CATEGORIES SECTION
   ========================================== */
.gm-categories {
    background: var(--gm-light);
    padding: 60px 5%;
}

.gm-categories-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 25px;
    max-width: 1400px;
    margin: 0 auto;
}

.gm-category-card {
    background: var(--gm-white);
    border-radius: var(--gm-radius);
    padding: 30px 20px;
    text-align: center;
    transition: var(--gm-transition);
    cursor: pointer;
    text-decoration: none;
    display: block;
}

.gm-category-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--gm-shadow-lg);
}

.gm-category-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
    border: 3px solid var(--gm-gray-light);
    transition: var(--gm-transition);
}

.gm-category-card:hover .gm-category-img {
    border-color: var(--gm-primary);
    transform: scale(1.1);
}

.gm-category-name {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--gm-dark);
    margin-bottom: 5px;
}

.gm-category-count {
    font-size: 0.8rem;
    color: var(--gm-gray);
}

/* ==========================================
   WHY CHOOSE US
   ========================================== */
.gm-why-us {
    background: linear-gradient(135deg, var(--gm-primary) 0%, var(--gm-primary-dark) 100%);
    padding: 80px 5%;
    color: var(--gm-white);
}

.gm-why-us .gm-section-title {
    color: var(--gm-white);
    justify-content: center;
    margin-bottom: 50px;
}

.gm-why-us .gm-section-title i {
    color: var(--gm-accent);
}

.gm-why-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.gm-why-card {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border-radius: var(--gm-radius);
    padding: 40px 25px;
    text-align: center;
    transition: var(--gm-transition);
    border: 1px solid rgba(255,255,255,0.2);
}

.gm-why-card:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-10px);
}

.gm-why-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: var(--gm-white);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 1.8rem;
    color: var(--gm-primary);
}

.gm-why-title {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 12px;
}

.gm-why-text {
    font-size: 0.9rem;
    opacity: 0.9;
    line-height: 1.6;
}


/* ==========================================
   ANIMATIONS
   ========================================== */
.gm-fade-in {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.gm-fade-in.visible {
    opacity: 1;
    transform: translateY(0);
}

/* ==========================================
   RESPONSIVE DESIGN
   ========================================== */
@media (max-width: 1200px) {
    .gm-categories-grid {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .gm-why-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .gm-hero {
        height: 60vh;
        min-height: 400px;
    }
    
    .gm-services-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .gm-promo-grid {
        grid-template-columns: 1fr;
    }
    
    .gm-promo-card {
        height: 200px;
    }
    
    .gm-categories-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .gm-hero {
        height: 55vh;
        min-height: 350px;
    }
    
    .gm-hero-overlay {
        padding: 0 5%;
    }
    
    .gm-hero-badge {
        font-size: 0.75rem;
        padding: 6px 15px;
    }
    
    .gm-hero-btns {
        flex-direction: column;
    }
    
    .gm-btn {
        width: 100%;
        justify-content: center;
    }
    
    .gm-hero-nav {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .gm-hero-prev { left: 10px; }
    .gm-hero-next { right: 10px; }
    
    .gm-services-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .gm-top-sale-title-wrap {
        align-items: flex-start;
    }
    
    .gm-top-sale-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
    }
    
    .gm-top-sale-title {
        font-size: 1.6rem;
    }
    
    .gm-top-sale-subtitle {
        font-size: 0.88rem;
    }
    
    .gm-section {
        padding: 40px 4%;
    }
    
    .gm-section-title {
        font-size: 1.4rem;
    }
    
    .gm-product-card {
        min-width: 220px;
        max-width: 220px;
    }
    
    .gm-products-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        padding: 10px;
    }
    
    .gm-ecom-card .gm-product-img-wrap {
        height: 170px;
    }
    
    .gm-ecom-card .gm-price-new {
        font-size: 1.45rem;
    }
    
    .gm-categories-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .gm-why-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
}

@media (max-width: 480px) {
    .gm-hero {
        height: 50vh;
        min-height: 300px;
    }
    
    .gm-hero-content {
        max-width: 100%;
    }
    
    .gm-section-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .gm-product-card {
        min-width: 200px;
        max-width: 200px;
    }
    
    .gm-products-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .gm-products-scroll {
        display: flex;
        gap: 10px;
        padding: 10px;
    }
    
    .gm-product-img-wrap {
        height: 160px;
    }
    
    .gm-product-info {
        padding: 15px;
    }
    
    .gm-product-btns {
        flex-direction: column;
    }
    
    .gm-categories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .gm-category-card {
        padding: 20px 15px;
    }
    
    .gm-category-img {
        width: 60px;
        height: 60px;
    }
}
</style>

{{-- ==========================================
    HERO SLIDER SECTION
    ========================================== --}}
<section class="gm-hero">
    <div class="gm-hero-slider">
        <div class="gm-hero-track">
            <!-- Slide 1 -->
            <div class="gm-hero-slide">
                <img src="{{ asset('assets/img/slide/slide1.jpg') }}" alt="Fresh Groceries">
                <div class="gm-hero-overlay">
                    <div class="gm-hero-content">
                        <span class="gm-hero-badge">🌿 Fresh Daily</span>
                        <h1 class="gm-hero-title">Fresh Groceries Delivered to Your Door</h1>
                        <p class="gm-hero-text">Get the freshest produce, dairy, and pantry essentials delivered within hours. Quality you can trust!</p>
                        <div class="gm-hero-btns">
                            <a href="#" class="gm-btn gm-btn-primary"><i class="fas fa-shopping-cart"></i> Shop Now</a>
                            <a href="#" class="gm-btn gm-btn-outline"><i class="fas fa-tag"></i> View Deals</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="gm-hero-slide">
                <img src="{{ asset('assets/img/slide/slide2.jpg') }}" alt="Special Offers">
                <div class="gm-hero-overlay">
                    <div class="gm-hero-content">
                        <span class="gm-hero-badge">🔥 Hot Deals</span>
                        <h1 class="gm-hero-title">Up to 40% Off on Fresh Produce</h1>
                        <p class="gm-hero-text">Limited time offer! Stock up on fruits, vegetables, and organic products at unbeatable prices.</p>
                        <div class="gm-hero-btns">
                            <a href="#" class="gm-btn gm-btn-primary"><i class="fas fa-percent"></i> Grab Deals</a>
                            <a href="#" class="gm-btn gm-btn-outline"><i class="fas fa-info-circle"></i> Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="gm-hero-slide">
                <img src="{{ asset('assets/img/slide/slide3.png') }}" alt="Organic Products">
                <div class="gm-hero-overlay">
                    <div class="gm-hero-content">
                        <span class="gm-hero-badge">🥬 100% Organic</span>
                        <h1 class="gm-hero-title">Farm Fresh Organic Products</h1>
                        <p class="gm-hero-text">Naturally grown, pesticide-free produce sourced directly from local farms. Healthy living starts here!</p>
                        <div class="gm-hero-btns">
                            <a href="#" class="gm-btn gm-btn-primary"><i class="fas fa-leaf"></i> Explore Organic</a>
                            <a href="#" class="gm-btn gm-btn-outline"><i class="fas fa-seedling"></i> Our Farms</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 4 -->
            <div class="gm-hero-slide">
                <img src="{{ asset('assets/img/slide/slide4.webp') }}" alt="Quick Delivery">
                <div class="gm-hero-overlay">
                    <div class="gm-hero-content">
                        <span class="gm-hero-badge">⚡ Express Delivery</span>
                        <h1 class="gm-hero-title">Same Day Delivery Available</h1>
                        <p class="gm-hero-text">Order before 2 PM and get your groceries delivered the same day. Fast, reliable, always on time!</p>
                        <div class="gm-hero-btns">
                            <a href="#" class="gm-btn gm-btn-primary"><i class="fas fa-truck"></i> Order Now</a>
                            <a href="#" class="gm-btn gm-btn-outline"><i class="fas fa-map-marker-alt"></i> Delivery Areas</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Arrows -->
        <button class="gm-hero-nav gm-hero-prev" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
        <button class="gm-hero-nav gm-hero-next" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>
        
        <!-- Dots Navigation -->
        <div class="gm-hero-dots">
            <div class="gm-hero-dot active" data-slide="0"></div>
            <div class="gm-hero-dot" data-slide="1"></div>
            <div class="gm-hero-dot" data-slide="2"></div>
            <div class="gm-hero-dot" data-slide="3"></div>
        </div>
    </div>
</section>

{{-- ==========================================
    HERO SLIDER JAVASCRIPT
    ========================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hero Slider
    const heroSlider = document.querySelector('.gm-hero-slider');
    if (heroSlider) {
        const track = heroSlider.querySelector('.gm-hero-track');
        const slides = heroSlider.querySelectorAll('.gm-hero-slide');
        const dots = heroSlider.querySelectorAll('.gm-hero-dot');
        const prevBtn = heroSlider.querySelector('.gm-hero-prev');
        const nextBtn = heroSlider.querySelector('.gm-hero-next');
        
        let currentIndex = 0;
        let slideInterval;
        const slideCount = slides.length;
        
        function updateSlider() {
            track.style.transform = `translateX(-${currentIndex * 100}%)`;
            dots.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
        }
        
        function goToSlide(index) {
            currentIndex = index < 0 ? slideCount - 1 : index >= slideCount ? 0 : index;
            updateSlider();
        }
        
        function startAutoSlide() {
            clearInterval(slideInterval);
            slideInterval = setInterval(() => goToSlide(currentIndex + 1), 5000);
        }
        
        function pauseAutoSlide() {
            clearInterval(slideInterval);
        }
        
        if (prevBtn) prevBtn.addEventListener('click', () => { pauseAutoSlide(); goToSlide(currentIndex - 1); startAutoSlide(); });
        if (nextBtn) nextBtn.addEventListener('click', () => { pauseAutoSlide(); goToSlide(currentIndex + 1); startAutoSlide(); });
        
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                const idx = parseInt(dot.getAttribute('data-slide'), 10);
                pauseAutoSlide();
                goToSlide(idx);
                startAutoSlide();
            });
        });
        
        heroSlider.addEventListener('mouseenter', pauseAutoSlide);
        heroSlider.addEventListener('mouseleave', startAutoSlide);
        
        updateSlider();
        startAutoSlide();
    }
});
</script>


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
        <!-- Top Sale Card 1 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">20% OFF</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Sunflower Oil">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Sunflower Oil Premium Cooking Pack</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.120</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.180</span> -33%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        <span>(128)</span>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Top Sale Card 2 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">20% OFF</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Sugar">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Refined Crystal Sugar for Daily Kitchen Use</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.80</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.100</span> -20%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                        <span>(95)</span>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Top Sale Card 3 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">15% OFF</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Masoor Dal">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Masoor Dal Healthy Family Pack</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.120</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.180</span> -33%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <span>(156)</span>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Top Sale Card 4 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">25% OFF</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Olive Oil">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Olive Oil Healthy Everyday Choice</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.350</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.450</span> -22%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        <span>(78)</span>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Top Sale Card 5 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">10% OFF</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Basmati Rice">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Basmati Rice Premium Long Grain Pack</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.680</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.750</span> -9%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                        <span>(203)</span>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Top Sale Card 6 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">30% OFF</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Chana Dal">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Chana Dal Nutritious Everyday Pack</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.95</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.135</span> -30%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <span>(89)</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- ==========================================
    PROMO BANNERS
    ========================================== --}}
<section class="gm-promo-section gm-fade-in">
    <div class="gm-promo-grid">
        <div class="gm-promo-card">
            <img src="{{ asset('assets/img/slide/slide1.jpg') }}" alt="Fresh Produce">
            <div class="gm-promo-overlay">
                <span class="gm-promo-tag">Fresh Arrivals</span>
                <h3 class="gm-promo-title">Farm Fresh Vegetables</h3>
                <a href="#" class="gm-promo-btn">Shop Now <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="gm-promo-card">
            <img src="{{ asset('assets/img/slide/slide2.jpg') }}" alt="Special Offers">
            <div class="gm-promo-overlay">
                <span class="gm-promo-tag">Limited Time</span>
                <h3 class="gm-promo-title">Up to 40% Off on Dairy</h3>
                <a href="#" class="gm-promo-btn">Grab Deal <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

{{-- ==========================================
    FEATURED PRODUCTS
    ========================================== --}}
<section class="gm-section gm-fade-in">
    <div class="gm-section-header">
        <h2 class="gm-section-title"><i class="fas fa-star"></i> Featured Products</h2>
        <a href="#" class="gm-view-all">View All <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div class="gm-products-grid">
        <!-- Ecommerce Card 1 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">20% OFF</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Sunflower Oil">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Sunflower Oil Premium Cooking Pack</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.120</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.180</span> -33%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        <span>(128)</span>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Ecommerce Card 2 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">20% OFF</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Sugar">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Refined Crystal Sugar for Daily Kitchen Use</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.80</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.100</span> -20%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                        <span>(95)</span>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Ecommerce Card 3 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">15% OFF</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Masoor Dal">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Masoor Dal Healthy Family Pack</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.120</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.180</span> -33%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <span>(156)</span>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Ecommerce Card 4 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">NEW</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Mustard Oil">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Mustard Oil Fresh Pressed Bottle</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.190</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.220</span> -14%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        <span>(67)</span>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Ecommerce Card 5 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">25% OFF</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Brown Sugar">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Organic Brown Sugar Natural Sweetener</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.85</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.110</span> -23%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                        <span>(45)</span>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Ecommerce Card 6 -->
        <div class="gm-product-card gm-ecom-card">
            <span class="gm-product-badge">10% OFF</span>
            <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
            <a href="{{ route('description') }}" class="gm-product-card-link">
                <div class="gm-product-img-wrap">
                    <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Toor Dal">
                </div>
                <div class="gm-product-info">
                    <h3 class="gm-product-name">Toor Dal Premium Quality Pack</h3>
                    <div class="gm-product-price">
                        <span class="gm-price-new">Rs.145</span>
                    </div>
                    <div class="gm-ecom-discount"><span class="gm-price-old">Rs.160</span> -9%</div>
                    <div class="gm-product-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <span>(198)</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- ==========================================
    TOP CATEGORIES
    ========================================== --}}
<section class="gm-categories gm-fade-in">
    <div style="max-width: 1400px; margin: 0 auto;">
        <div class="gm-section-header">
            <h2 class="gm-section-title"><i class="fas fa-th-large"></i> Top Categories</h2>
            <a href="#" class="gm-view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="gm-categories-grid">
            <a href="#" class="gm-category-card">
                <img src="{{ asset('assets/img/catagory/catagory5.jpg') }}" alt="Staples & Grains" class="gm-category-img">
                <h4 class="gm-category-name">Staples & Grains</h4>
                <p class="gm-category-count">250+ Products</p>
            </a>
            <a href="#" class="gm-category-card">
                <img src="{{ asset('assets/img/catagory/catagory2.avif') }}" alt="Snacks" class="gm-category-img">
                <h4 class="gm-category-name">Snacks & Confectionery</h4>
                <p class="gm-category-count">180+ Products</p>
            </a>
            <a href="#" class="gm-category-card">
                <img src="{{ asset('assets/img/catagory/catagory3.jpg') }}" alt="Beverages" class="gm-category-img">
                <h4 class="gm-category-name">Beverages</h4>
                <p class="gm-category-count">120+ Products</p>
            </a>
            <a href="#" class="gm-category-card">
                <img src="{{ asset('assets/img/catagory/catagory5.jpg') }}" alt="Dairy" class="gm-category-img">
                <h4 class="gm-category-name">Dairy & Eggs</h4>
                <p class="gm-category-count">95+ Products</p>
            </a>
            <a href="#" class="gm-category-card">
                <img src="{{ asset('assets/img/catagory/catagory6.webp') }}" alt="Personal Care" class="gm-category-img">
                <h4 class="gm-category-name">Personal Care</h4>
                <p class="gm-category-count">200+ Products</p>
            </a>
            <a href="#" class="gm-category-card">
                <img src="{{ asset('assets/img/catagory/catagory7.webp') }}" alt="Household" class="gm-category-img">
                <h4 class="gm-category-name">Household Items</h4>
                <p class="gm-category-count">150+ Products</p>
            </a>
        </div>
    </div>
</section>

{{-- ==========================================
    WHY CHOOSE US
    ========================================== --}}
<section class="gm-why-us gm-fade-in">
    <h2 class="gm-section-title"><i class="fas fa-award"></i> Why Choose GroceMate?</h2>
    <div class="gm-why-grid">
        <div class="gm-why-card">
            <div class="gm-why-icon"><i class="fas fa-leaf"></i></div>
            <h4 class="gm-why-title">100% Fresh Products</h4>
            <p class="gm-why-text">We source directly from farms to ensure the freshest produce reaches your doorstep.</p>
        </div>
        <div class="gm-why-card">
            <div class="gm-why-icon"><i class="fas fa-truck"></i></div>
            <h4 class="gm-why-title">Express Delivery</h4>
            <p class="gm-why-text">Same-day delivery available for orders placed before 2 PM. Fast and reliable!</p>
        </div>
        <div class="gm-why-card">
            <div class="gm-why-icon"><i class="fas fa-tags"></i></div>
            <h4 class="gm-why-title">Best Prices</h4>
            <p class="gm-why-text">Competitive pricing with regular discounts and offers to save on every order.</p>
        </div>
        <div class="gm-why-card">
            <div class="gm-why-icon"><i class="fas fa-headset"></i></div>
            <h4 class="gm-why-title">24/7 Support</h4>
            <p class="gm-why-text">Our dedicated support team is always ready to help with your queries.</p>
        </div>
    </div>
</section>


{{-- ==========================================
    ANIMATIONS & INTERACTIONS JAVASCRIPT
    ========================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fade-in animation on scroll
    const fadeElements = document.querySelectorAll('.gm-fade-in');
    
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    fadeElements.forEach(el => observer.observe(el));
    
    // Auto-scroll functionality for product scrollers
    const autoScrollContainers = document.querySelectorAll('.gm-products-scroll');
    autoScrollContainers.forEach(container => {
        // Add auto-scroll class
        container.classList.add('auto-scroll');
        
        let scrollInterval;
        let scrollDirection = 1;
        let isPaused = false;
        
        function startAutoScroll() {
            if (isPaused) return;
            
            scrollInterval = setInterval(() => {
                if (!isPaused && container.scrollWidth > container.clientWidth) {
                    container.scrollLeft += scrollDirection * 1;
                    
                    // Check if we've reached the end
                    if (container.scrollLeft >= container.scrollWidth - container.clientWidth) {
                        scrollDirection = -1;
                    } else if (container.scrollLeft <= 0) {
                        scrollDirection = 1;
                    }
                }
            }, 30);
        }
        
        function stopAutoScroll() {
            clearInterval(scrollInterval);
        }
        
        // Pause on hover
        container.addEventListener('mouseenter', () => {
            isPaused = true;
            container.classList.remove('auto-scroll');
        });
        
        container.addEventListener('mouseleave', () => {
            isPaused = false;
            container.classList.add('auto-scroll');
        });
        
        // Start auto-scroll
        startAutoScroll();
        
        // Handle touch devices
        container.addEventListener('touchstart', () => {
            isPaused = true;
            container.classList.remove('auto-scroll');
        });
        
        container.addEventListener('touchend', () => {
            isPaused = false;
            container.classList.add('auto-scroll');
        });
    });
    
    // Smooth scroll for horizontal product scrollers
    const productScrollers = document.querySelectorAll('.gm-products-scroll');
    productScrollers.forEach(scroller => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        scroller.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - scroller.offsetLeft;
            scrollLeft = scroller.scrollLeft;
        });
        
        scroller.addEventListener('mouseleave', () => isDown = false);
        scroller.addEventListener('mouseup', () => isDown = false);
        
        scroller.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scroller.offsetLeft;
            const walk = (x - startX) * 2;
            scroller.scrollLeft = scrollLeft - walk;
        });
    });
});
</script>

@endsection
