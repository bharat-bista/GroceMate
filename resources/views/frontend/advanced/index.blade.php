
   
<style>
    
    
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
    border-color: goldenrod;
    box-shadow: 0 0 0 2px rgba(218,165,32,0.2);
    outline: none;
}

/* Optional Apply Button */
.search-filters .btn-apply-filter {
    background: #0A0F2C;
    color: #fff;
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
}
.search-filters .btn-apply-filter:hover {
    background: goldenrod;
    color: #0A0F2C;
}

/* Responsive */
@media (max-width: 576px) {
   .search-filters {
        flex-direction: row; /* keep horizontal */
        flex-wrap: wrap; /* allow wrapping if really small */
        gap: 10px; /* reduce gap a bit on mobile */
    }

    .search-filters .form-control,
    .search-filters .form-select {
        flex: 1 1 48%; /* each takes ~half width on mobile */
        min-width: 120px;
    }

    .search-filters .btn-apply-filter {
        width: 100%; /* button still full width if exists */
        margin-top: 10px;
    }
    

}


        .category-title {
            color: white;
            font-weight: bold;
            padding: 10px;
            margin-bottom: 0;
            text-align: center;
            background: #0A0F2C;
        }


    .search-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
        font-size:20px;
    }

    .search-filters > * {
        flex: 1 1 200px;
        min-width: 150px;
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
    /* Responsive */
@media (max-width: 576px) {
   .category-list {
        max-height: calc(3 * 40px); /* assuming each label ~40px height */
        overflow-y: auto;
        -webkit-overflow-scrolling: touch; /* smooth scroll on iOS */
    }

    .category-list label {
        padding: 10px 8px;
        border-bottom: 1px solid #eee;
    }

}
    
    .search-icon-advanced {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0A0F2C;
        color: white;
        padding: 0 15px;
        border-radius: 5px;
        cursor: pointer;
    }

.price-range-section {
        padding: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .price-min, .price-max {
        font-size: 14px;
        color: #0A0F2C;
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
        background: teal;
        border: none;
    }
    
    .form-range::-moz-range-thumb {
        width: 18px;
        height: 18px;
        background: teal;
        border: none;
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
            <div id="product-listing" class="row"></div>
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
    });
</script>

@endsection
