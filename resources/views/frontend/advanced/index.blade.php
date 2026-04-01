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
       PRODUCT CARDS - HOMEPAGE STYLE
       ========================================== */
    .gm-product-card {
        background: var(--gm-white);
        border: 1px solid #f0f0f0;
        border-radius: var(--gm-radius);
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: var(--gm-transition);
        position: relative;
        margin-bottom: 20px;
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

    /* Product Grid Layout */
    .gm-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 14px;
        background: #f5f5f5;
        padding: 14px;
        border-radius: 12px;
    }

    /* Container for the filters */
    .search-filters {
        display: flex;
        gap: 15px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    /* Input + Select */
    .search-filters .form-control,
    .search-filters .form-select {
        flex: 1;
        height: 45px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
        padding: 0 12px;
        transition: all 0.3s ease;
    }

    /* Hover / Focus effect */
    .search-filters .form-control:focus,
    .search-filters .form-select:focus {
        border-color: var(--gm-primary);
        box-shadow: 0 0 0 2px rgba(46, 125, 50, 0.2);
        outline: none;
    }

    /* Optional Apply Button */
    .search-filters .btn-apply-filter {
        background: var(--gm-primary);
        color: var(--gm-white);
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .search-filters .btn-apply-filter:hover {
        background: var(--gm-accent);
        color: var(--gm-white);
    }

    .category-title {
        color: var(--gm-white);
        font-weight: bold;
        padding: 10px;
        margin-bottom: 0;
        text-align: center;
        background: var(--gm-primary);
    }

    .category-list {
        max-height: 500px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .category-list label {
        display: block;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
        cursor: pointer;
    }

    .search-icon-advanced {
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gm-primary);
        color: var(--gm-white);
        padding: 0 15px;
        border-radius: 5px;
        cursor: pointer;
    }

    .price-range-section {
        padding: 15px;
        background: var(--gm-white);
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .price-min, .price-max {
        font-size: 14px;
        color: var(--gm-primary);
        font-weight: 500;
    }
    
    .form-range {
        width: 100%;
        height: 6px;
        background: #f0f0f0;
        border-radius: 3px;
    }
    
    .form-range::-webkit-slider-thumb {
        width: 18px;
        height: 18px;
        background: var(--gm-primary);
        border: none;
    }
    
    .form-range::-moz-range-thumb {
        width: 18px;
        height: 18px;
        background: var(--gm-primary);
        border: none;
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
    }

    @media (max-width: 576px) {
        .search-filters {
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;
        }

        .search-filters .form-control,
        .search-filters .form-select {
            flex: 1 1 48%;
            min-width: 120px;
        }

        .search-filters .btn-apply-filter {
            width: 100%;
            margin-top: 10px;
        }
        
        .category-list {
            max-height: calc(3 * 40px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .category-list label {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
        }
    }
</style>

@extends('frontend.layouts.main')
@section('main-content')

<!-- ✅ Styles stay the same, keeping your design -->

<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4">
            <h3 class="category-title">Categories</h3>

            <!-- Price Filter -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold">Price Range</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span id="price-min">Rs. 200</span>
                <span id="price-max">Rs. 1000</span>
            </div>
            <div id="price-slider" class="mt-2"></div>

            <!-- Category Filter -->
            <div class="category-list mt-4">
    <label>
        <input type="checkbox" name="categories[]" value="1" class="category-checkbox">
        COSMETICS
    </label>

    <label>
        <input type="checkbox" name="categories[]" value="2" class="category-checkbox">
        AYURVEDIC
    </label>

    <label>
        <input type="checkbox" name="categories[]" value="3" class="category-checkbox">
        BABY CARE
    </label>

    <label>
        <input type="checkbox" name="categories[]" value="4" class="category-checkbox">
        PERSONAL CARE
    </label>

    <label>
        <input type="checkbox" name="categories[]" value="5" class="category-checkbox">
        MEDICAL DEVICES
    </label>
</div>


        </div>

       <!-- Products -->
<div class="col-lg-9 col-md-8">
    <!-- Top Filters -->
    <div class="search-filters mb-4 p-3 bg-light rounded">
        
        <input type="text" id="product_name" class="form-control" placeholder="Product Name">

        <select id="company_name" class="form-select">
            <option value="">Select Any Company</option>
            <option value="1">Company A</option>
            <option value="2">Company B</option>
            <option value="3">Company C</option>
            <option value="4">Company D</option>
        </select>

    </div>
</div>


            <!-- Product Listing -->
            <div id="product-listing" class="gm-products-grid"></div>
        </div>
    </div>
</div>


<!-- ✅ Scripts -->
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.5.1/dist/nouislider.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/nouislider@15.5.1/dist/nouislider.min.css" rel="stylesheet">

<script>
    let sliderMin = 0;
    let sliderMax = 50000;
    let priceSlider;

    $(document).ready(function () {

        

        // 🔵 Price slider initialization
        priceSlider = document.getElementById('price-slider');
        noUiSlider.create(priceSlider, {
            start: [sliderMin, sliderMax],
            connect: true,
            range: {
                'min': sliderMin,
                'max': sliderMax
            },
            step: 100
        });

        const priceMin = document.getElementById('price-min');
        const priceMax = document.getElementById('price-max');

        priceSlider.noUiSlider.on('update', function(values) {
            sliderMin = Math.round(values[0]);
            sliderMax = Math.round(values[1]);
            priceMin.textContent = 'Rs. ' + sliderMin.toLocaleString();
            priceMax.textContent = 'Rs. ' + sliderMax.toLocaleString();
        });

        priceSlider.noUiSlider.on('change', fetchProducts);

        // 🔵 Initial display
        fetchProducts();
        
        // 🔵 fetchProducts function
        function fetchProducts() {
            const productListing = document.getElementById('product-listing');
            
            // Sample products data - replace with actual API call
            const products = [
                {
                    id: 1,
                    name: 'Sunflower Oil Premium Cooking Pack',
                    image: '{{ asset("assets/img/product/product1.jpg") }}',
                    price: 80,
                    oldPrice: 100,
                    discount: '20%',
                    badge: '20% OFF',
                    rating: 4,
                    reviews: 95,
                    alt: 'Sugar'
                },
                {
                    id: 2,
                    name: 'Masoor Dal Healthy Family Pack',
                    image: '{{ asset("assets/img/product/product3.jpg") }}',
                    price: 120,
                    oldPrice: 180,
                    discount: '33%',
                    badge: '15% OFF',
                    rating: 5,
                    reviews: 156,
                    alt: 'Masoor Dal'
                },
                {
                    id: 3,
                    name: 'Olive Oil Healthy Everyday Choice',
                    image: '{{ asset("assets/img/product/product1.jpg") }}',
                    price: 350,
                    oldPrice: 450,
                    discount: '22%',
                    badge: '25% OFF',
                    rating: 4.5,
                    reviews: 78,
                    alt: 'Olive Oil'
                },
                {
                    id: 4,
                    name: 'Basmati Rice Premium Long Grain Pack',
                    image: '{{ asset("assets/img/product/product2.jpg") }}',
                    price: 680,
                    oldPrice: 750,
                    discount: '9%',
                    badge: '10% OFF',
                    rating: 4,
                    reviews: 203,
                    alt: 'Basmati Rice'
                },
                {
                    id: 5,
                    name: 'Chana Dal Nutritious Everyday Pack',
                    image: '{{ asset("assets/img/product/product3.jpg") }}',
                    price: 95,
                    oldPrice: 135,
                    discount: '30%',
                    badge: '30% OFF',
                    rating: 5,
                    reviews: 89,
                    alt: 'Chana Dal'
                },
                {
                    id: 6,
                    name: 'Refined Crystal Sugar for Daily Kitchen Use',
                    image: '{{ asset("assets/img/product/product2.jpg") }}',
                    price: 80,
                    oldPrice: 100,
                    discount: '20%',
                    badge: '20% OFF',
                    rating: 4,
                    reviews: 95,
                    alt: 'Sugar'
                }
            ];
            
            let html = '';
            products.forEach(product => {
                const stars = generateStars(product.rating);
                html += `
                    <div class="gm-product-card gm-ecom-card">
                        <span class="gm-product-badge">${product.badge}</span>
                        <span class="gm-cart-icon-badge"><i class="fas fa-shopping-cart"></i></span>
                        <a href="{{ route('description') }}" class="gm-product-card-link">
                            <div class="gm-product-img-wrap">
                                <img src="${product.image}" alt="${product.alt}">
                            </div>
                            <div class="gm-product-info">
                                <h3 class="gm-product-name">${product.name}</h3>
                                <div class="gm-product-price">
                                    <span class="gm-price-new">Rs.${product.price}</span>
                                </div>
                                <div class="gm-ecom-discount"><span class="gm-price-old">Rs.${product.oldPrice}</span> -${product.discount}</div>
                                <div class="gm-product-rating">
                                    ${stars}
                                    <span>(${product.reviews})</span>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
            });
            
            productListing.innerHTML = html;
        }
        
        // Helper function to generate star rating
        function generateStars(rating) {
            let stars = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 !== 0;
            
            for (let i = 0; i < fullStars; i++) {
                stars += '<i class="fas fa-star"></i>';
            }
            
            if (hasHalfStar) {
                stars += '<i class="fas fa-star-half-alt"></i>';
            }
            
            const emptyStars = 5 - Math.ceil(rating);
            for (let i = 0; i < emptyStars; i++) {
                stars += '<i class="far fa-star"></i>';
            }
            
            return stars;
        }
    });
</script>

@endsection
