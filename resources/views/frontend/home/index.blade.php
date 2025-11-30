@extends('frontend.layouts.main')

@section('main-content')
<!-- Add this after your header section -->
<section class="slider-section">
    <div class="slider-container">
        <div class="slider-track">          
            <!-- Slide 1 -->
            <div class="slide">
                <img src="{{ asset('assets/img/slide/slide1.jpg') }}" alt="slide1">
                <div class="slide-content">
                    <h2>"Freshness Delivered, Happiness Guaranteed!"</h2>
                    <p>Freshness Delivered, Happiness Guaranteed!</p>
                    <a href="#" class="slide-btn">Shop Now</a>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="slide">
                <img src="{{ asset('assets/img/slide/slide2.jpg') }}" alt="slide2">
                <div class="slide-content">
                    <h2>"Freshness Delivered, Happiness Guaranteed!"</h2>
                    <p>Freshness Delivered, Happiness Guaranteed!</p>
                    <a href="#" class="slide-btn">Shop Now</a>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="slide">
                <img src="{{ asset('assets/img/slide/slide3.png') }}" alt="slide3">
                <div class="slide-content">
                    <h2>"Freshness Delivered, Happiness Guaranteed!"</h2>
                    <p>Freshness Delivered, Happiness Guaranteed!</p>
                    <a href="#" class="slide-btn">Shop Now</a>
                </div>
            </div>
            
            <!-- Slide 4 -->
            <div class="slide">
                <img src="{{ asset('assets/img/slide/slide4.webp') }}" alt="slide4">
                <div class="slide-content">
                    <h2>"Freshness Delivered, Happiness Guaranteed!"</h2>
                    <p>Freshness Delivered, Happiness Guaranteed!</p>
                    <a href="#" class="slide-btn">Shop Now</a>
                </div>
            </div>
        </div>
        
        <!-- Navigation Arrows -->
        <div class="slider-nav">
            <button class="prev-btn" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
            <button class="next-btn" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>
        </div>
        
        <!-- Dots Navigation -->
        <div class="slider-dots">
            <div class="dot active" data-slide="0"></div>
            <div class="dot" data-slide="1"></div>
            <div class="dot" data-slide="2"></div>
            <div class="dot" data-slide="3"></div> <!-- Added 4th dot -->
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize each slider-container independently
  document.querySelectorAll('.slider-container').forEach(container => {
    const track = container.querySelector('.slider-track');
    if (!track) return; // nothing to do

    const slides = track.querySelectorAll('.slide');
    const dots = container.querySelectorAll('.dot');
    const prevBtn = container.querySelector('.prev-btn');
    const nextBtn = container.querySelector('.next-btn');

    if (slides.length === 0) return;

    let currentIndex = 0;
    let slideInterval;
    const slideCount = slides.length;

    function updateSliderPosition() {
      // Move track by percentage of container width (each slide = 100%)
      track.style.transform = `translateX(-${currentIndex * 100}%)`;

      // Update dots inside this container only
      if (dots.length) {
        dots.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
      }
    }

    function goToSlide(index) {
      if (index >= slideCount) {
        currentIndex = 0;
      } else if (index < 0) {
        currentIndex = slideCount - 1;
      } else {
        currentIndex = index;
      }
      updateSliderPosition();
    }

    function nextSlide() { goToSlide(currentIndex + 1); }
    function prevSlide() { goToSlide(currentIndex - 1); }

    // Auto slide
    function startAutoSlide() {
      clearInterval(slideInterval);
      slideInterval = setInterval(nextSlide, 5000);
    }
    function pauseAutoSlide() { clearInterval(slideInterval); }

    // Navigation buttons
    if (nextBtn) nextBtn.addEventListener('click', () => { pauseAutoSlide(); nextSlide(); startAutoSlide(); });
    if (prevBtn) prevBtn.addEventListener('click', () => { pauseAutoSlide(); prevSlide(); startAutoSlide(); });

    // Dots (ensure each dot has correct data-slide for that container)
    dots.forEach(dot => {
      dot.addEventListener('click', () => {
        const idx = parseInt(dot.getAttribute('data-slide'), 10);
        if (!Number.isNaN(idx)) {
          pauseAutoSlide();
          goToSlide(idx);
          startAutoSlide();
        }
      });
    });

    // Pause on hover
    container.addEventListener('mouseenter', pauseAutoSlide);
    container.addEventListener('mouseleave', startAutoSlide);

    // Initialize
    updateSliderPosition();
    startAutoSlide();

    // Keep layout consistent on resize
    window.addEventListener('resize', updateSliderPosition);
  });
});
</script>

<section>
    <!-- Top Sale Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="trending-label mb-0">TOP SALE:</h1>
        <a href="#" class="view-more-link">View More &raquo;</a>
    </div>

    <div class="cart-scroll-wrapper">
        <div class="cart-scroll-inner">
            <!-- Product Card 1 -->
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunflow</h5>
                <h4 class="product-title">Sunflower Oil</h4>
                <p class="product-description">1 litre </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Product 1" class="product-img">
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
                <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Rani</h5>
                <h4 class="product-title">Massor Dal</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunflow</h5>
                <h4 class="product-title">Sunflower Oil</h4>
                <p class="product-description">1 litre </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
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
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Rani</h5>
                <h4 class="product-title">Massor Dal</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunflow</h5>
                <h4 class="product-title">Sunflower Oil</h4>
                <p class="product-description">1 litre </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Product 1" class="product-img">
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
                <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Rani</h5>
                <h4 class="product-title">Massor Dal</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            
        </div>
    </div>
</section>

<script>
    document.querySelectorAll('.cart-scroll-inner').forEach((container) => {
        let scrollX = 0;
        const cardWidth = 270; // width + padding/margin

        setInterval(() => {
            scrollX += cardWidth;
            if (scrollX >= container.scrollWidth - container.clientWidth) {
                scrollX = 0;
            }
            container.scrollTo({
                left: scrollX,
                behavior: 'smooth'
            });
        }, 2000); // every 2 seconds
    });
</script>

    <!--banner section -->

<section class="advertisement-section container-fluid my-4">
    <div class="row g-3">
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3 mb-lg-0">
            <div class="ad-banner">
                <img src="{{ asset('assets/img/slide/slide1.jpg') }}" alt="Ad 1" class="img-fluid ad-image">
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3 mb-lg-0">
            <div class="ad-banner">
                <img src="{{ asset('assets/img/slide/slide2.jpg') }}" alt="Ad 2" class="img-fluid ad-image">
            </div>
        </div>
    </div>
</section>
<section class="featured-product-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="featured-heading d-flex align-items-center gap-2">
            <i class="bi bi-stars fs-3 text-primary"></i>
            <h1 class="featured-label mb-0">Featured Products</h1>
        </div>
        <a href="#" class="view-more-link">View More &raquo;</a>
    </div>

    <div class="product-grid">
        <!-- Product Card 1 -->
        <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunflow</h5>
                <h4 class="product-title">Sunflower Oil</h4>
                <p class="product-description">1 litre </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <a href="{{ route('description') }}" class="buy-now-btn">Buy Now</a>

                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Product 1" class="product-img">
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
                <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Rani</h5>
                <h4 class="product-title">Massor Dal</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunflow</h5>
                <h4 class="product-title">Sunflower Oil</h4>
                <p class="product-description">1 litre </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
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
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Rani</h5>
                <h4 class="product-title">Massor Dal</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunflow</h5>
                <h4 class="product-title">Sunflower Oil</h4>
                <p class="product-description">1 litre </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Product 1" class="product-img">
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
                <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Rani</h5>
                <h4 class="product-title">Massor Dal</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
       
    </div>
</section>
<section class="top-categories py-5 w-100">
    <div class="container-fluid px-lg-5">
        <h2 class="text-center mb-5 fw-bold" style="color: #f39c12;">Top Categories</h2>

        <div class="row justify-content-center g-4">
            <!-- Category Card 1 -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="category-card text-center p-4 border rounded shadow-sm">
                    <img src="assets/img/catagory/catagory5.jpg" alt="FRUITS" class="img-fluid mb-3 category-img">
                    <h5 class="fw-bold text-primary small">Staples & Grains</h5>
                </div>
            </div>

            <!-- Category Card 2 -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="category-card text-center p-4 border rounded shadow-sm">
                    <img src="assets/img/catagory/catagory2.avif" alt="VEGETABLES" class="img-fluid mb-3 category-img">
                    <h5 class="fw-bold text-primary small">Snacks & Confectionery</h5>
                </div>
            </div>

            <!-- Category Card 3 -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="category-card text-center p-4 border rounded shadow-sm">
                    <img src="assets/img/catagory/catagory3.jpg" alt="DAIRY" class="img-fluid mb-3 category-img">
                    <h5 class="fw-bold text-primary small">Beverages</h5>
                </div>
            </div>

            <!-- Category Card 4 -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="category-card text-center p-4 border rounded shadow-sm">
                    <img src="assets/img/catagory/catagory5.jpg" alt="SNACKS" class="img-fluid mb-3 category-img">
                    <h5 class="fw-bold text-primary small">Cooking Essentials</h5>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="category-card text-center p-4 border rounded shadow-sm">
                    <img src="assets/img/catagory/catagory4.png" alt="SNACKS" class="img-fluid mb-3 category-img">
                    <h5 class="fw-bold text-primary small">Personal Care & Hygiene</h5>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="category-card text-center p-4 border rounded shadow-sm">
                    <img src="assets/img/catagory/catagory5.jpg" alt="SNACKS" class="img-fluid mb-3 category-img">
                    <h5 class="fw-bold text-primary small">Cooking Essentials</h5>
                </div>
            </div>
            
            

            
        </div>
    </div>
</section>


<section class="slider-section">
    <div class="slider-container">
        <div class="slider-track">          
            <!-- Slide 1 -->
            <div class="slide">
                <img src="{{ asset('assets/img/slide/slide2.jpg') }}" alt="slide2">
                <div class="slide-content">
                    <h2>"Freshness Delivered, Happiness Guaranteed!"</h2>
                    <p>Freshness Delivered, Happiness Guaranteed!</p>
                    <a href="#" class="slide-btn">Shop Now</a>
                </div>
            </div>
       </div>
    </div>
</section>

<section>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center mb-5 fw-bold" style="color: #f39c12;">Shop By Brand:</h2>
    </div>
    <div class="cart-scroll-wrapper">
        <div class="cart-scroll-inner">
            <!-- Brand 1 -->
            <div class="product-card">
                <img src="assets/img/brands/brand1.png" alt="Brand 1" class="product-img">
                <h4 class="product-title">Brand 1</h4>
            </div>

            <!-- Brand 2 -->
            <div class="product-card">
                <img src="assets/img/brands/brand2.png" alt="Brand 2" class="product-img">
                <h4 class="product-title">Brand 2</h4>
            </div>

            <!-- Brand 3 -->
            <div class="product-card">
                <img src="assets/img/brands/brand3.png" alt="Brand 3" class="product-img">
                <h4 class="product-title">Brand 3</h4>
            </div>

            <!-- Brand 4 -->
            <div class="product-card">
                <img src="assets/img/brands/brand4.png" alt="Brand 4" class="product-img">
                <h4 class="product-title">Brand 4</h4>
            </div>

            <!-- Brand 5 -->
            <div class="product-card">
                <img src="assets/img/brands/brand5.png" alt="Brand 5" class="product-img">
                <h4 class="product-title">Brand 5</h4>
            </div>
            <div class="product-card">
                <img src="assets/img/brands/brand1.png" alt="Brand 1" class="product-img">
                <h4 class="product-title">Brand 1</h4>
            </div>

            <!-- Brand 2 -->
            <div class="product-card">
                <img src="assets/img/brands/brand2.png" alt="Brand 2" class="product-img">
                <h4 class="product-title">Brand 2</h4>
            </div>

            <!-- Brand 3 -->
            <div class="product-card">
                <img src="assets/img/brands/brand3.png" alt="Brand 3" class="product-img">
                <h4 class="product-title">Brand 3</h4>
            </div>

            <!-- Brand 4 -->
            <div class="product-card">
                <img src="assets/img/brands/brand4.png" alt="Brand 4" class="product-img">
                <h4 class="product-title">Brand 4</h4>
            </div>

            <!-- Brand 5 -->
            <div class="product-card">
                <img src="assets/img/brands/brand5.png" alt="Brand 5" class="product-img">
                <h4 class="product-title">Brand 5</h4>
            </div>
        </div>
    </div>
</section>


</section>  

<section class="featured-product-section">
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div class="featured-heading d-flex align-items-center gap-2">
            <i class="bi bi-stars fs-3 text-primary"></i>
            <h1 class="featured-label mb-0">Latest Products</h1>
        </div>
        <a href="#" class="view-more-link">View More &raquo;</a>
    </div>

    <div class="product-grid">
        <!-- Product Card 1 -->
        <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunflow</h5>
                <h4 class="product-title">Sunflower Oil</h4>
                <p class="product-description">1 litre </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Product 1" class="product-img">
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
                <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Rani</h5>
                <h4 class="product-title">Massor Dal</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunflow</h5>
                <h4 class="product-title">Sunflower Oil</h4>
                <p class="product-description">1 litre </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
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
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Rani</h5>
                <h4 class="product-title">Massor Dal</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Sunflow</h5>
                <h4 class="product-title">Sunflower Oil</h4>
                <p class="product-description">1 litre </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
            <div class="product-card" data-url="#">
                <div class="offer-badge">20% OFF</div>
                <img src="{{ asset('assets/img/product/product2.jpg') }}" alt="Product 1" class="product-img">
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
                <img src="{{ asset('assets/img/product/product3.jpg') }}" alt="Product 1" class="product-img">
                <h5 class="product-sku">Brand: Rani</h5>
                <h4 class="product-title">Massor Dal</h4>
                <p class="product-description">1 kg </p>
                <div class="price-section">
                    <span class="old-price">Rs 180</span>
                    <span class="new-price">Rs 120</span>
                </div>
                <div class="btn-group-wrapper">
                    <button class="add-to-cart-btn">Add To Cart</button>
                    <button class="buy-now-btn">Buy Now</button>
                </div>
            </div>
       
    </div>
</section>
<section class="why-grocemate-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">🛒 Why Choose GroceMate?</h2>
            <p class="section-subtitle">Your trusted partner for fresh groceries, quality products, and convenient online shopping</p>
        </div>

        <div class="row g-4">
            <!-- Feature Cards -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <h5>✅ Wide Range of Products</h5>
                    <p>From fresh vegetables, fruits, dairy, to pantry staples and household essentials – all in one place.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <h5>✅ Fast & Convenient Delivery</h5>
                    <p>Get your groceries delivered to your doorstep quickly and safely, anywhere in Nepal.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <h5>✅ Affordable Prices</h5>
                    <p>Competitive pricing with regular discounts and special offers to save you more on your daily shopping.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <h5>✅ Trusted Brands</h5>
                    <p>We partner with reputable local and international brands to ensure quality and freshness.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <h5>✅ Easy Online Ordering</h5>
                    <p>User-friendly app and website interface for quick ordering, cart management, and multiple payment options.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100">
                    <h5>✅ Customer Support</h5>
                    <p>Dedicated support team ready to help with orders, returns, or inquiries for a smooth shopping experience.</p>
                </div>
            </div>
        </div>

        <!-- Company Overview -->
        <div class="company-info mt-5">
            <h4>🏢 About Us</h4>
            <p>GroceMate is Nepal’s premier online grocery platform, dedicated to delivering fresh, high-quality groceries and household essentials. We aim to make grocery shopping convenient, affordable, and reliable for families and businesses alike.</p>

            <h4 class="mt-4">🛍️ Our Products & Services</h4>
            <ul class="product-list">
                <li><strong>Fresh Produce:</strong> Fruits, Vegetables, Organic Products</li>
                <li><strong>Dairy & Bakery:</strong> Milk, Cheese, Bread, Eggs</li>
                <li><strong>Pantry & Household:</strong> Rice, Flour, Spices, Cleaning Supplies</li>
                <li><strong>Online Services:</strong> Easy Ordering, Multiple Payment Options, Fast Delivery</li>
            </ul>

            <h4 class="mt-4">🎯 Our Mission</h4>
            <p>To provide a seamless, affordable, and trustworthy grocery shopping experience, making fresh and quality products accessible to every home in Nepal.</p>
        </div>
    </div>
</section>



<script>
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Ignore clicks on buttons or form elements
            if(!e.target.closest('button') && !e.target.closest('form')) {
                window.location.href = card.dataset.url;
            }
        });
    });
</script>



@endsection
