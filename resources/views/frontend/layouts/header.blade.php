<header class="header-area">
  <!-- Top Bar (Desktop Only) -->
  <div class="top-bar d-none d-lg-flex justify-content-between align-items-center px-4 py-2">
    <div class="top-bar-left">
      <a href="https://maps.app.goo.gl/kUDL46Ynpvhb9q2x9"
         class="top-link"
         target="_blank"
         rel="noopener noreferrer"
         title="Find us on Google Maps">
        <i class="fas fa-map-marker-alt me-1"></i> Store Locator
      </a>
      <span class="top-divider">|</span>
      <a href="tel:1800-123-4567" class="top-link">
        <i class="fas fa-phone me-1"></i> 1800-123-4567
      </a>
    </div>
    
  </div>

  <!-- ================= MOBILE HEADER ================= -->
  <div class="container-fluid d-flex d-lg-none align-items-center justify-content-between px-3 py-2">
    <!-- Hamburger -->
    <button class="hamburger" id="navToggle" aria-label="Open menu" aria-controls="mobileNav" aria-expanded="false">
      <i class="fas fa-bars"></i>
    </button>

    <!-- Center Logo -->
    <div class="logo mx-auto text-center">
      <a href="{{ route('home') }}">
        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo" style="max-height:36px;">
      </a>
    </div>

    <div class="d-flex align-items-center gap-2">
      <!-- Cart -->
      <a href="{{ route('cart') }}" class="text-dark position-relative" aria-label="Cart">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill gm-cart-count">0</span>
      </a>

      <!-- User Dropdown (mobile) -->

      <!-- Search Toggle -->
      <button class="search-toggle-btn" id="searchToggle" aria-controls="mobileSearchBar" aria-expanded="false" aria-label="Toggle search">
        <i class="fas fa-search"></i>
      </button>
    </div>
  </div>

  <!-- ================= DESKTOP HEADER ================= -->
  <div class="container-fluid d-none d-lg-flex align-items-center justify-content-between px-4 py-3 main-header">
    <!-- Logo -->
    <div class="logo-section">
      <a href="{{ route('home') }}" class="logo-link">
        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="GroceMate Logo" class="main-logo">
      </a>
    </div>

    <!-- Desktop Search -->
    <div class="search-bar-wrapper flex-grow-1 mx-4">
      <div class="position-relative search-container mx-auto" style="max-width: 600px;">
        <form action="#" method="GET" class="modern-search-bar">
          <i class="fas fa-search search-icon"></i>
          <input type="text" name="query" id="global-search-input-desktop" class="search-input"
                 placeholder="Search for products, brands, and more...">
          <button type="submit" class="search-button">
            Search
          </button>
        </form>
        <!-- DESKTOP results -->
        <div id="global-search-results-desktop" class="search-results-dropdown"></div>
      </div>
    </div>

    <!-- Desktop Actions -->
    <div class="d-flex align-items-center header-actions gap-3">
      <!-- Cart Button -->
      <a href="{{ route('cart') }}" class="header-icon-btn cart-btn" aria-label="Shopping Cart">
        <i class="fas fa-shopping-cart"></i>
        <span class="icon-label">Cart</span>
        <span class="cart-badge gm-cart-count">0</span>
      </a>

      <!-- User Button -->
      <a href="#" class="header-icon-btn user-btn" aria-label="My Account">
        <i class="fas fa-user"></i>
        <span class="icon-label">Account</span>
      </a>
    </div>
  </div>

  <!-- Mobile search dropdown -->
  <div class="mobile-search-bar d-lg-none px-3 pt-2" id="mobileSearchBar">
    <div class="position-relative search-container">
      <form action="#" method="GET" class="d-flex">
        <input type="text" name="query" id="global-search-input-mobile" class="form-control rounded-0"
               placeholder="Search your products...">
        <button type="submit" class="btn mobile-search-btn"><i class="fas fa-search"></i></button>
      </form>
      <!-- MOBILE results -->
      <div id="global-search-results-mobile" class="list-group"></div>
    </div>
  </div>

@php
  $selectedHeaderCategoryIds = collect((array) request()->input('categories', []))
      ->map(fn ($id) => (int) $id)
      ->filter(fn ($id) => $id > 0)
      ->values()
      ->all();
@endphp

<nav class="main-nav d-none d-lg-block">
    <div class="container-fluid px-4">
        <div class="nav-inner d-flex align-items-center py-2">
            @forelse($headerNavCategories ?? [] as $headerCategory)
                <a href="{{ route('advanced', ['categories' => [$headerCategory->id]]) }}"
                   class="nav-link-item {{ in_array($headerCategory->id, $selectedHeaderCategoryIds, true) ? 'active' : '' }}">
                    {{ $headerCategory->name }}
                </a>
            @empty
                <a href="{{ route('advanced') }}" class="nav-link-item">Browse Products</a>
            @endforelse
        </div>
    </div>
</nav>


  <!-- ================= MOBILE OVERLAY NAV ================= -->
  <div class="mobile-nav-backdrop" id="mobileBackdrop" hidden></div>

  <aside class="mobile-nav-drawer" id="mobileNav" aria-hidden="true" tabindex="-1">
    <div class="mobile-nav-header">
      <span>Browse Categories</span>
      <button class="close-btn" id="closeNav" aria-label="Close menu">&times;</button>
    </div>
    <div class="mobile-nav-body">
      @forelse($headerNavCategories ?? [] as $headerCategory)
        <a class="mobile-nav-link"
           href="{{ route('advanced', ['categories' => [$headerCategory->id]]) }}">
          {{ $headerCategory->name }}
        </a>
      @empty
        <a class="mobile-nav-link" href="{{ route('advanced') }}">Browse Products</a>
      @endforelse
    </div>
  </aside>
</header>


<style>
/* ==========================================
   MODERN HEADER STYLES - 3 COLOR PALETTE
   ========================================== */

/* COLOR VARIABLES */
:root {
  --primary-green: #28a745;
  --primary-green-dark: #218838;
  --primary-green-light: #d4edda;
  --secondary-navy: #0A0F2C;
  --secondary-navy-light: #1a2340;
  --neutral-white: #FFFFFF;
  --neutral-light: #F5F5F5;
  --neutral-gray: #757575;
  --neutral-border: #E0E0E0;
}

/* ==========================================
   HEADER CONTAINER
   ========================================== */
.header-area {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: var(--neutral-white);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  transform: translateY(0);
}

.header-area.header-hidden {
  transform: translateY(-100%);
}

.header-area.scrolled {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

/* ==========================================
   TOP BAR (Desktop Only)
   ========================================== */
.top-bar {
  background: var(--primary-green);
  color: var(--neutral-white);
  font-size: 0.8rem;
  height: 32px;
}

.top-link {
  color: var(--neutral-white);
  text-decoration: none;
  transition: opacity 0.2s;
  padding: 0 8px;
}

.top-link:hover {
  opacity: 0.85;
  color: var(--neutral-white);
}

.top-divider {
  color: rgba(255, 255, 255, 0.4);
  margin: 0 4px;
}

/* ==========================================
   MAIN HEADER (Desktop)
   ========================================== */
.main-header {
  height: 80px;
  background: var(--neutral-white);
}

/* Logo Section */
.logo-section {
  display: flex;
  align-items: center;
}

.logo-link {
  text-decoration: none;
  display: flex;
  align-items: center;
}

.main-logo {
  height: 70px;
  width: auto;
}

/* Modern Search Bar */
.modern-search-bar {
  position: relative;
  display: flex;
  align-items: center;
  background: var(--neutral-white);
  border: 2px solid var(--neutral-border);
  border-radius: 12px;
  transition: all 0.3s ease;
  max-width: 600px;
  width: 100%;
}

.modern-search-bar:focus-within {
  border-color: var(--primary-green);
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);
}

.search-icon {
  position: absolute;
  left: 16px;
  color: var(--neutral-gray);
  font-size: 1rem;
  pointer-events: none;
}

.search-input {
  flex: 1;
  border: none;
  outline: none;
  padding: 12px 16px 12px 45px;
  font-size: 0.95rem;
  background: transparent;
}

.search-input::placeholder {
  color: var(--neutral-gray);
}

.search-button {
  background: var(--primary-green);
  color: var(--neutral-white);
  border: none;
  padding: 12px 24px;
  border-radius: 0 10px 10px 0;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.search-button:hover {
  background: var(--primary-green-dark);
}

/* Header Action Buttons */
.header-actions {
  gap: 12px;
}

.header-icon-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  color: var(--secondary-navy);
  padding: 8px 16px;
  border-radius: 8px;
  transition: all 0.2s ease;
  position: relative;
  min-width: 70px;
}

.header-icon-btn i {
  font-size: 2rem;
  margin-bottom: 4px;
}

.icon-label {
  font-size: 0.85rem;
  font-weight: 600;
}

.header-icon-btn:hover {
  background: var(--primary-green);
  color: var(--neutral-white);
  transform: translateY(-2px);
}

.cart-badge {
  position: absolute;
  top: 15px;
  right: 18px;
  background: var(--primary-green);
  color: var(--neutral-white);
  font-size: 0.85rem;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 14px;
  min-width: 24px;
  text-align: center;
  line-height: 1;
}

.header-icon-btn:hover .cart-badge {
  background: var(--neutral-white);
  color: var(--secondary-navy);
  font-weight: 700;
}

/* ==========================================
   NAVIGATION BAR
   ========================================== */
.main-nav {
  background: var(--secondary-navy);
  border-top: 2px solid var(--primary-green);
}

.nav-inner {
  gap: 0;
}

.nav-link-item {
  color: var(--neutral-white);
  text-decoration: none;
  padding: 8px 20px;
  font-size: 1.5rem;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  border-bottom: 3px solid transparent;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.nav-link-item i {
  font-size: 0.9rem;
  opacity: 0.8;
}

.nav-link-item:hover {
  color: var(--neutral-white);
  background: var(--secondary-navy-light);
  border-bottom-color: var(--primary-green);
}

.nav-link-item.active {
  border-bottom-color: var(--primary-green);
  font-weight: 600;
}

/* Search Results Dropdown */
.search-results-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  right: 0;
  background: var(--neutral-white);
  border: 1px solid var(--neutral-border);
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  max-height: 400px;
  overflow-y: auto;
  display: none;
  z-index: 1000;
}

.search-results-dropdown.show {
  display: block;
}

/* ==========================================
   MOBILE STYLES (Color Updates Only)
   ========================================== */
@media (max-width: 991.98px) {
  /* Mobile Header - Update colors only */
  .hamburger,
  .search-toggle-btn {
    color: var(--secondary-navy);
  }

  .mobile-search-btn {
    background: var(--primary-green) !important;
    border-color: var(--primary-green) !important;
    color: var(--neutral-white) !important;
  }

  .mobile-search-btn:hover {
    background: var(--primary-green-dark) !important;
  }

  .mobile-nav-drawer {
    background: var(--neutral-white);
  }

  .mobile-nav-header {
    background: var(--primary-green);
    color: var(--neutral-white);
    border-bottom: none;
  }

  .close-btn {
    color: var(--neutral-white);
  }

  .mobile-nav-link {
    color: var(--secondary-navy);
    border-bottom: 1px solid var(--neutral-border);
  }

  .mobile-nav-link:hover {
    background: var(--neutral-light);
    color: var(--primary-green);
  }

  .cart-badge {
    background: var(--primary-green);
    top: -5px !important;
    right: -5px !important;
    font-size: 0.7rem !important;
    padding: 2px 5px !important;
    min-width: 18px !important;
  }
}

/* Prevent background scroll when drawer is open */
.no-scroll { overflow: hidden; touch-action: none; }

/* Backdrop */
.mobile-nav-backdrop { 
  position: fixed; 
  inset: 0; 
  background: rgba(10, 15, 44, 0.6);
  z-index: 998; 
  backdrop-filter: blur(2px);
}

/* Drawer */
.mobile-nav-drawer {
  position: fixed; top: 0; left: 0; height: 100dvh;
  width: 88vw; max-width: 360px; 
  box-shadow: 2px 0 24px rgba(0,0,0,.15);
  transform: translateX(-100%); transition: transform .22s cubic-bezier(.2,.7,.3,1);
  z-index: 999; outline: none; display: flex; flex-direction: column;
  padding-bottom: env(safe-area-inset-bottom, 0);
}

@media (prefers-reduced-motion: reduce) { 
  .mobile-nav-drawer { transition: none; } 
}

.mobile-nav-drawer.open { transform: translateX(0); }

.mobile-nav-header { 
  display:flex; 
  align-items:center; 
  justify-content:space-between; 
  padding: 1rem; 
  font-weight: 700; 
  font-size: 1.1rem;
}

.close-btn { 
  border:none; 
  background:transparent; 
  font-size:2rem; 
  line-height:1; 
  cursor:pointer; 
  min-width:44px; 
  min-height:44px; 
  display:inline-flex; 
  align-items:center; 
  justify-content:center; 
}

/* Drawer body */
.mobile-nav-body { 
  padding: .5rem 0; 
  overflow-y: auto; 
  -webkit-overflow-scrolling: touch; 
  flex: 1 1 auto; 
}

.mobile-nav-link { 
  display:block; 
  padding:1rem 1.2rem; 
  text-decoration:none; 
  font-size: 1rem;
  font-weight: 500;
}

/* ===== MOBILE SEARCH VISIBILITY ===== */
@media (max-width: 991.98px) {
  .mobile-search-bar { display: none; }
  .mobile-search-bar.show { 
    display: block; 
    background: var(--neutral-light);
    padding: 12px;
  }
}

@media (min-width: 992px) {
  .mobile-search-bar { display: none; }
}

/* Tap sizes */
.hamburger, .search-toggle-btn, .user-icon {
  min-width:44px; min-height:44px; 
  display:inline-flex; 
  align-items:center; 
  justify-content:center;
  border: none; 
  background: transparent; 
  font-size:1.3rem;
  cursor: pointer;
}

/* User dropdown */
.user-dropdown { position: relative; }
.user-dropdown .user-menu {
  position: absolute; right: 0; top: 120%;
  background: var(--neutral-white);
  border: 1px solid var(--neutral-border);
  border-radius: 8px;
  min-width: 180px;
  padding: .5rem;
  display: none;
  z-index: 1001;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.user-dropdown.show .user-menu { display:block; }
</style>

<script>
(function () {
  const navToggle = document.getElementById('navToggle');
  const mobileNav = document.getElementById('mobileNav');
  const closeNav  = document.getElementById('closeNav');
  const backdrop  = document.getElementById('mobileBackdrop');
  const searchToggle   = document.getElementById('searchToggle');
  const mobileSearchBar= document.getElementById('mobileSearchBar');
  const body = document.body;

  // Sticky header hide/show functionality
  let lastScrollTop = 0;
  let scrollThreshold = 50; // Minimum scroll distance to trigger header hide/show
  const header = document.querySelector('.header-area');
  
  if (header) {
    // Add padding to body to prevent content jump when header is fixed
    body.style.paddingTop = header.offsetHeight + 'px';
    
    window.addEventListener('scroll', () => {
      const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
      
      // Don't hide/show header if at the top of the page
      if (currentScrollTop <= 0) {
        header.classList.remove('header-hidden');
        header.classList.add('scrolled');
        lastScrollTop = currentScrollTop;
        return;
      }
      
      // Don't hide/show header if scroll distance is less than threshold
      if (Math.abs(currentScrollTop - lastScrollTop) < scrollThreshold) {
        return;
      }
      
      // Hide header when scrolling down
      if (currentScrollTop > lastScrollTop) {
        header.classList.add('header-hidden');
      } 
      // Show header when scrolling up
      else {
        header.classList.remove('header-hidden');
        header.classList.add('scrolled');
      }
      
      lastScrollTop = currentScrollTop;
    }, { passive: true });
    
    // Update body padding on window resize
    window.addEventListener('resize', () => {
      body.style.paddingTop = header.offsetHeight + 'px';
    });
  }

  // Drawer guards
  if (navToggle && mobileNav && backdrop) {
    const FOCUSABLE = 'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])';
    let firstFocus, lastFocus;

    function setFocusTrap() {
      const nodes = mobileNav.querySelectorAll(FOCUSABLE);
      firstFocus = nodes[0];
      lastFocus  = nodes[nodes.length - 1];
    }

    function openDrawer() {
      mobileNav.classList.add('open');
      mobileNav.setAttribute('aria-hidden','false');
      backdrop.hidden = false;
      body.classList.add('no-scroll');
      navToggle.setAttribute('aria-expanded','true');
      setFocusTrap();
      (firstFocus || mobileNav).focus({preventScroll:true});
    }

    function closeDrawer() {
      mobileNav.classList.remove('open');
      mobileNav.setAttribute('aria-hidden','true');
      backdrop.hidden = true;
      body.classList.remove('no-scroll');
      navToggle.setAttribute('aria-expanded','false');
      navToggle.focus({preventScroll:true});
    }

    navToggle.addEventListener('click', openDrawer);
    if (closeNav) closeNav.addEventListener('click', closeDrawer);
    backdrop.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', (e) => {
      if (!mobileNav.classList.contains('open')) return;
      if (e.key === 'Escape') closeDrawer();
      if (e.key === 'Tab') {
        setFocusTrap();
        if (!firstFocus || !lastFocus) return;
        if (e.shiftKey && document.activeElement === firstFocus) { e.preventDefault(); lastFocus.focus(); }
        else if (!e.shiftKey && document.activeElement === lastFocus) { e.preventDefault(); firstFocus.focus(); }
      }
    });

    mobileNav.addEventListener('click', (e) => {
      const link = e.target.closest('a.mobile-nav-link');
      if (link) closeDrawer();
    });

    // Swipe to close
    let startX = null, startY = null, deltaX = 0;
    mobileNav.addEventListener('touchstart', (e) => {
      const t = e.touches[0]; startX = t.clientX; startY = t.clientY; deltaX = 0;
    }, { passive: true });

    mobileNav.addEventListener('touchmove', (e) => {
      if (startX === null) return;
      const t = e.touches[0]; const dx = t.clientX - startX; const dy = Math.abs(t.clientY - startY);
      if (dx > 0 && dy < 30) {
        deltaX = dx;
        mobileNav.style.transform = `translateX(${Math.min(dx, mobileNav.offsetWidth)}px)`;
      }
    }, { passive: true });

    mobileNav.addEventListener('touchend', () => {
      const threshold = Math.min(120, mobileNav.offsetWidth * 0.35);
      mobileNav.style.transform = '';
      if (deltaX > threshold) closeDrawer();
      startX = null; deltaX = 0;
    });
  }

  // ✅ Mobile search toggle — class-based
  if (searchToggle && mobileSearchBar) {
    searchToggle.addEventListener('click', () => {
      const isOpen = mobileSearchBar.classList.toggle('show');
      searchToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

      // Hide results when closing
      if (!isOpen) {
        const mobRes = document.getElementById('global-search-results-mobile');
        if (mobRes) mobRes.style.display = 'none';
      } else {
        // Focus input when opened
        const inp = document.getElementById('global-search-input-mobile');
        if (inp) setTimeout(() => inp.focus(), 0);
      }
    });

    // 🔒 Close the mobile search when switching to desktop
    const mql = window.matchMedia('(min-width: 992px)');
    function handleViewportChange(e) {
      if (e.matches) {
        mobileSearchBar.classList.remove('show');
        searchToggle.setAttribute('aria-expanded', 'false');
        const mobRes = document.getElementById('global-search-results-mobile');
        if (mobRes) mobRes.style.display = 'none';
      }
    }
    handleViewportChange(mql);
    if (typeof mql.addEventListener === 'function') mql.addEventListener('change', handleViewportChange);
    else mql.addListener(handleViewportChange); // Safari
  }
})();
</script>
