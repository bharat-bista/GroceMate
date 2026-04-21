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
       PRODUCT GRID / ECOMMERCE SECTION
       ========================================== */
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

    .gm-ecom-card .gm-product-rating {
        display: none !important;
    }

    .gm-ecom-card .gm-product-btns {
        display: none;
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
        background: linear-gradient(135deg, #ffffff 0%, #f8fdf9 100%);
        border: 2px solid #e8f5e9;
        border-radius: 16px;
        padding: 24px;
        align-items: stretch;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .search-filters::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--gm-primary) 0%, #1e7e34 50%, var(--gm-primary) 100%);
        background-size: 200% 100%;
        animation: gradientShift 3s ease infinite;
    }
    
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .search-filters:hover {
        box-shadow: 0 12px 35px rgba(40, 167, 69, 0.2);
        transform: translateY(-3px);
        border-color: var(--gm-primary);
    }

    /* Input + Select */
    .search-filters .form-control,
    .search-filters .form-select {
        flex: 1;
        height: 52px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        font-size: 15px;
        padding: 0 18px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        font-weight: 500;
        color: #2c3e50;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    
    .search-filters .form-control::placeholder {
        color: #95a5a6;
        font-weight: 400;
    }
    
    .search-filters .form-select {
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2328a745' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px 12px;
        padding-right: 40px;
    }

    /* Hover / Focus effect */
    .search-filters .form-control:hover,
    .search-filters .form-select:hover {
        border-color: #c8e6c9;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.08);
    }
    
    .search-filters .form-control:focus,
    .search-filters .form-select:focus {
        border-color: var(--gm-primary);
        box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.15);
        outline: none;
        background: #f1f8e9;
        transform: translateY(-2px);
    }

    /* Search Button */
    .search-filters .btn-search {
        background: linear-gradient(135deg, var(--gm-primary) 0%, #1e7e34 100%);
        color: var(--gm-white);
        padding: 0 32px;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        display: flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }
    
    .search-filters .btn-search::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .search-filters .btn-search:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .search-filters .btn-search:hover {
        background: linear-gradient(135deg, #1e7e34 0%, #155d27 100%);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        transform: translateY(-3px) scale(1.05);
    }
    
    .search-filters .btn-search:active {
        transform: translateY(-1px) scale(1.02);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    
    .search-filters .btn-search i {
        font-size: 18px;
        position: relative;
        z-index: 1;
    }
    
    .search-filters .btn-search span {
        position: relative;
        z-index: 1;
    }

    .ajax-results-wrap {
        position: relative;
    }

    .ajax-results-wrap.is-loading {
        opacity: 0.65;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    .result-summary {
        margin-bottom: 14px;
        color: #4b5563;
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .price-filter-section {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid #e8f5e9 !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
        position: relative;
        overflow: hidden;
    }
    
    .price-filter-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--gm-primary) 0%, #1e7e34 50%, var(--gm-primary) 100%);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }
    
    .price-filter-section:hover::before {
        transform: translateX(0);
    }
    
    .price-filter-section:hover {
        box-shadow: 0 8px 30px rgba(40, 167, 69, 0.25) !important;
        border-color: var(--gm-primary) !important;
        transform: translateY(-3px);
    }

    .category-title {
        color: var(--gm-white);
        font-weight: 800;
        font-size: 1.6rem;
        padding: 22px 20px;
        margin-bottom: 0;
        text-align: center;
        background: linear-gradient(135deg, var(--gm-primary) 0%, #1e7e34 80%, #155d27 100%);
        border-radius: 12px 12px 0 0;
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
        letter-spacing: 1.5px;
        text-transform: uppercase;
        position: relative;
        overflow: hidden;
    }
    
    .category-title::before {
        content: '🏷️';
        position: absolute;
        left: 20px;
        font-size: 1.8rem;
        opacity: 0.9;
    }
    
    .category-title::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: shimmer 3s infinite linear;
    }
    
    @keyframes shimmer {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .category-list {
        max-height: 500px;
        overflow-y: auto;
        padding: 20px;
        background: linear-gradient(to bottom, #ffffff 0%, #fafafa 100%);
        border: 2px solid #e8f5e9;
        border-top: none;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .category-list label {
        display: flex;
        align-items: center;
        padding: 16px 18px;
        margin-bottom: 10px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(to right, #ffffff 0%, #f8f9fa 100%);
        font-size: 0.95rem;
        font-weight: 500;
        color: #333;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    
    .category-list label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 5px;
        background: linear-gradient(to bottom, var(--gm-primary) 0%, #1e7e34 100%);
        transform: scaleY(0);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 10px 0 0 10px;
    }
    
    .category-list label:hover::before {
        transform: scaleY(1);
    }

    .category-list label:hover {
        background: linear-gradient(135deg, #f1f8e9 0%, #e8f5e9 100%);
        border-color: var(--gm-primary);
        transform: translateX(8px) scale(1.02);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.2);
    }
    
    .category-list label:has(input:checked) {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 50%, #e8f5e9 100%);
        border-color: var(--gm-primary);
        border-width: 2.5px;
        font-weight: 600;
        color: var(--gm-primary);
        box-shadow: 0 8px 24px rgba(40, 167, 69, 0.25);
        transform: translateX(8px);
    }
    
    .category-list label:has(input:checked)::before {
        transform: scaleY(1);
    }

    .category-list input[type="checkbox"] {
        margin-right: 14px;
        cursor: pointer;
        width: 22px;
        height: 22px;
        flex-shrink: 0;
        appearance: none;
        -webkit-appearance: none;
        border: 2.5px solid #c8e6c9;
        border-radius: 6px;
        background: #ffffff;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .category-list input[type="checkbox"]:hover {
        border-color: var(--gm-primary);
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
    }
    
    .category-list input[type="checkbox"]:checked {
        background: linear-gradient(135deg, var(--gm-primary) 0%, #1e7e34 100%);
        border-color: var(--gm-primary);
        animation: checkboxPulse 0.5s ease;
    }
    
    @keyframes checkboxPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.15); }
        100% { transform: scale(1); }
    }
    
    .category-list input[type="checkbox"]:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 14px;
        font-weight: bold;
    }
    
    .category-list label span {
        flex-grow: 1;
        font-size: 1rem;
        font-weight: 500;
        color: #2c3e50;
        letter-spacing: 0.3px;
    }
    
    /* Custom Scrollbar for Category List */
    .category-list::-webkit-scrollbar {
        width: 8px;
    }

    .category-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .category-list::-webkit-scrollbar-thumb {
        background: var(--gm-primary);
        border-radius: 10px;
        transition: background 0.3s ease;
    }

    .category-list::-webkit-scrollbar-thumb:hover {
        background: #1e7e34;
    }
    
    /* Custom NoUiSlider Styling */
    #price-slider .noUi-connect {
        background: linear-gradient(90deg, var(--gm-primary) 0%, #1e7e34 100%);
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }
    
    #price-slider .noUi-handle {
        border: 3px solid var(--gm-primary);
        border-radius: 50%;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        cursor: pointer;
        transition: all 0.3s ease;
        width: 22px;
        height: 22px;
    }
    
    #price-slider .noUi-handle:hover {
        transform: scale(1.2);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.5);
    }
    
    #price-slider .noUi-handle::before,
    #price-slider .noUi-handle::after {
        display: none;
    }
    
    #price-slider .noUi-target {
        background: #e8f5e9;
        border: none;
        box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        height: 8px;
    }
    
    #price-min, #price-max {
        font-weight: 600;
        font-size: 0.95rem !important;
        padding: 10px 16px !important;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(40, 167, 69, 0.2);
        letter-spacing: 0.5px;
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
    @media (max-width: 992px) {
        .search-filters {
            padding: 18px;
            gap: 12px;
        }
        
        .search-filters .form-control,
        .search-filters .form-select {
            font-size: 14px;
            height: 48px;
        }
        
        .search-filters .btn-search {
            padding: 0 24px;
            font-size: 14px;
        }
    }
    
    @media (max-width: 768px) {
        .search-filters {
            flex-wrap: wrap;
        }
        
        .search-filters .form-control,
        .search-filters .form-select {
            flex: 1 1 calc(50% - 8px);
            min-width: 200px;
        }
        
        .search-filters .btn-search {
            flex: 1 1 100%;
        }
        
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
            flex-direction: column;
            gap: 12px;
            padding: 20px;
        }

        .search-filters .form-control,
        .search-filters .form-select {
            width: 100%;
        }

        .search-filters .btn-search {
            width: 100%;
            justify-content: center;
            padding: 14px 20px;
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

<div class="container my-5">
    <form id="advanced-filter-form" method="GET" action="{{ route('advanced') }}">
        <input type="hidden" name="min_price" id="minPriceInput" value="{{ number_format($selectedMinPrice, 2, '.', '') }}">
        <input type="hidden" name="max_price" id="maxPriceInput" value="{{ number_format($selectedMaxPrice, 2, '.', '') }}">

        <div class="row">
            <div class="col-lg-3 col-md-4">
                <h3 class="category-title">Categories</h3>

                <div class="price-filter-section bg-white p-4 rounded-3 shadow-sm mb-4 border border-light">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold text-dark" style="font-size: 1.1rem;">
                            <i class="bi bi-tag-fill text-success me-2"></i>Price Range
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span id="price-min" class="badge bg-success bg-gradient px-3 py-2" style="font-size: 0.9rem;">
                            Rs. {{ number_format($selectedMinPrice, 2) }}
                        </span>
                        <span id="price-max" class="badge bg-success bg-gradient px-3 py-2" style="font-size: 0.9rem;">
                            Rs. {{ number_format($selectedMaxPrice, 2) }}
                        </span>
                    </div>
                    <div id="price-slider" class="mt-3"></div>
                </div>

                <div class="category-list mt-4">
                    @forelse($categories as $category)
                        <label>
                            <input
                                type="checkbox"
                                name="categories[]"
                                value="{{ $category->id }}"
                                class="category-checkbox"
                                {{ in_array($category->id, $categoryIds, true) ? 'checked' : '' }}
                            >
                            <span>{{ strtoupper($category->name) }}</span>
                        </label>
                    @empty
                        <p class="text-muted mb-0">No categories available.</p>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="search-filters mb-4">
                    <input
                        type="text"
                        id="product_name"
                        name="q"
                        class="form-control"
                        value="{{ $q }}"
                        placeholder="Search Product Name..."
                        autocomplete="off"
                    >

                    <select id="company_name" name="brand_id" class="form-select">
                        <option value="">Select Any Company</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $brandId === $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="button" class="btn-search" id="search-btn">
                        <i class="bi bi-search"></i>
                        <span>Search</span>
                    </button>

                    <a href="{{ route('advanced') }}" class="btn-search" style="text-decoration: none; background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Reset</span>
                    </a>
                </div>

                <p class="result-summary">Showing <span id="result-count">{{ $ecommerceProducts->total() }}</span> products</p>

                <div id="advanced-results" class="ajax-results-wrap">
                    @include('frontend.advanced.partials.product-results', ['ecommerceProducts' => $ecommerceProducts])
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/nouislider@15.5.1/dist/nouislider.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/nouislider@15.5.1/dist/nouislider.min.css" rel="stylesheet">

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Main filter elements.
    const form = document.getElementById('advanced-filter-form');
    const searchBtn = document.getElementById('search-btn');
    const productInput = document.getElementById('product_name');
    const companySelect = document.getElementById('company_name');
    const categoryChecks = document.querySelectorAll('.category-checkbox');
    const minInput = document.getElementById('minPriceInput');
    const maxInput = document.getElementById('maxPriceInput');
    const minBadge = document.getElementById('price-min');
    const maxBadge = document.getElementById('price-max');
    const sliderEl = document.getElementById('price-slider');
    const resultsWrap = document.getElementById('advanced-results');
    const resultCountEl = document.getElementById('result-count');

    // Important URLs for AJAX calls.
    const advancedUrl = @json(route('advanced'));

    const rangeMin = Number(@json($availableMinPrice));
    const rangeMax = Number(@json($availableMaxPrice));
    const selectedMin = Number(@json($selectedMinPrice));
    const selectedMax = Number(@json($selectedMaxPrice));

    const safeMin = Number.isFinite(rangeMin) ? rangeMin : 0;
    const safeMax = Number.isFinite(rangeMax) ? rangeMax : safeMin;

    // Keep references so we can cancel stale requests.
    let activeFilterRequest = null;
    let filterDebounceTimer = null;

    function setLoadingState(isLoading) {
        if (!resultsWrap) {
            return;
        }

        resultsWrap.classList.toggle('is-loading', isLoading);
    }

    function formatPrice(value) {
        return 'Rs. ' + Number(value).toLocaleString('en-US', { maximumFractionDigits: 2 });
    }

    // Convert form state to URL query while preserving repeated fields.
    function buildFilterUrl() {
        const params = new URLSearchParams(new FormData(form));

        // Remove blank values so URL stays clean and shareable.
        Array.from(params.entries()).forEach(function ([key, value]) {
            if (String(value).trim() === '') {
                params.delete(key);
            }
        });

        const query = params.toString();
        return query ? (advancedUrl + '?' + query) : advancedUrl;
    }

    // Read selected category values from all possible query key formats.
    function getCategoryValuesFromParams(params) {
        const values = [];
        params.forEach(function (value, key) {
            if (key === 'categories' || key === 'categories[]' || key.startsWith('categories[')) {
                values.push(String(value));
            }
        });
        return values;
    }

    // Keep filter inputs synced when navigating with browser history.
    function syncFormWithUrl(urlString) {
        const parsed = new URL(urlString, window.location.origin);
        const params = parsed.searchParams;

        if (productInput) {
            productInput.value = params.get('q') || '';
        }

        if (companySelect) {
            companySelect.value = params.get('brand_id') || '';
        }

        const selectedCategories = new Set(getCategoryValuesFromParams(params));
        categoryChecks.forEach(function (checkbox) {
            checkbox.checked = selectedCategories.has(String(checkbox.value));
        });

        const nextMin = Number(params.get('min_price'));
        const nextMax = Number(params.get('max_price'));

        if (minInput && Number.isFinite(nextMin)) {
            minInput.value = String(nextMin);
        }
        if (maxInput && Number.isFinite(nextMax)) {
            maxInput.value = String(nextMax);
        }

        if (sliderEl && sliderEl.noUiSlider) {
            const slideMin = Number.isFinite(nextMin) ? nextMin : safeMin;
            const slideMax = Number.isFinite(nextMax) ? nextMax : safeMax;
            sliderEl.noUiSlider.set([slideMin, slideMax]);
        }
    }

    // Fetch filtered result HTML and replace only the product section.
    async function fetchFilters(url, shouldPushState) {
        if (!resultsWrap) {
            window.location.href = url;
            return;
        }

        if (activeFilterRequest) {
            activeFilterRequest.abort();
        }

        activeFilterRequest = new AbortController();
        setLoadingState(true);

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                signal: activeFilterRequest.signal
            });

            if (!response.ok) {
                throw new Error('Failed to fetch filtered results');
            }

            const payload = await response.json();

            if (typeof payload.html === 'string') {
                resultsWrap.innerHTML = payload.html;
            }

            if (resultCountEl && typeof payload.total !== 'undefined') {
                resultCountEl.textContent = Number(payload.total).toLocaleString('en-US');
            }

            if (shouldPushState) {
                window.history.pushState({}, '', url);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                // Hard fallback: if AJAX fails for any reason, continue with normal navigation.
                window.location.href = url;
            }
        } finally {
            setLoadingState(false);
        }
    }

    function requestFilters(shouldPushState = true) {
        fetchFilters(buildFilterUrl(), shouldPushState);
    }

    function debouncedFilterRequest(delayMs) {
        window.clearTimeout(filterDebounceTimer);
        filterDebounceTimer = window.setTimeout(function () {
            requestFilters(true);
        }, delayMs);
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            requestFilters(true);
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', function () {
            requestFilters(true);
        });
    }

    if (productInput) {
        productInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                requestFilters(true);
            }
        });
    }

    if (companySelect) {
        companySelect.addEventListener('change', function () {
            requestFilters(true);
        });
    }

    categoryChecks.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            debouncedFilterRequest(180);
        });
    });

    // Handle pagination links inside refreshed AJAX results.
    if (resultsWrap) {
        resultsWrap.addEventListener('click', function (event) {
            const pageLink = event.target.closest('.pagination a');
            if (!pageLink) {
                return;
            }

            event.preventDefault();
            fetchFilters(pageLink.href, true);
        });
    }

    // Browser back/forward should restore both form controls and results.
    window.addEventListener('popstate', function () {
        syncFormWithUrl(window.location.href);
        fetchFilters(window.location.href, false);
    });

    // Initialize slider (or fallback text) immediately on load.
    if (!sliderEl || safeMax <= safeMin || typeof noUiSlider === 'undefined') {
        if (minBadge) {
            minBadge.textContent = formatPrice(selectedMin || safeMin);
        }
        if (maxBadge) {
            maxBadge.textContent = formatPrice(selectedMax || safeMax);
        }
        if (minInput) {
            minInput.value = String(selectedMin || safeMin);
        }
        if (maxInput) {
            maxInput.value = String(selectedMax || safeMax);
        }
        return;
    }

    const startMin = Math.min(Math.max(selectedMin || safeMin, safeMin), safeMax);
    const startMax = Math.max(Math.min(selectedMax || safeMax, safeMax), safeMin);

    noUiSlider.create(sliderEl, {
        start: [startMin, startMax],
        connect: true,
        range: {
            min: safeMin,
            max: safeMax,
        },
        step: 1,
    });

    sliderEl.noUiSlider.on('update', function (values) {
        const currentMin = Math.round(Number(values[0]) * 100) / 100;
        const currentMax = Math.round(Number(values[1]) * 100) / 100;

        if (minBadge) {
            minBadge.textContent = formatPrice(currentMin);
        }
        if (maxBadge) {
            maxBadge.textContent = formatPrice(currentMax);
        }
        if (minInput) {
            minInput.value = String(currentMin);
        }
        if (maxInput) {
            maxInput.value = String(currentMax);
        }
    });

    sliderEl.noUiSlider.on('change', function () {
        debouncedFilterRequest(120);
    });
});
</script>

@endsection

