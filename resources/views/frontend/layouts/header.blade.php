<header class="header-area">
  <!-- Top Bar -->
  <div class="top-bar bg-lightblue d-flex justify-content-between align-items-center px-3 py-1">
    <div>
      <a href="https://maps.app.goo.gl/kUDL46Ynpvhb9q2x9"
         class="top-link"
         target="_blank"
         rel="noopener noreferrer"
         title="Find us on Google Maps">
        <i class="fas fa-map-marker-alt me-1"></i> Store Locator
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
      <a href="{{ url('/') }}">
        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo" style="max-height:36px;">
      </a>
    </div>

    <div class="d-flex align-items-center gap-2">
      <!-- Cart -->
      <a href="#" class="text-dark position-relative" aria-label="Cart">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill">
          {{-- {{ $totalCartQuantity ?? 0 }} --}}
        </span>
      </a>

      <!-- User Dropdown (mobile) -->

      <!-- Search Toggle -->
      <button class="search-toggle-btn" id="searchToggle" aria-controls="mobileSearchBar" aria-expanded="false" aria-label="Toggle search">
        <i class="fas fa-search"></i>
      </button>
    </div>
  </div>

  <!-- ================= DESKTOP HEADER ================= -->
  <div class="container-fluid d-none d-lg-flex align-items-center justify-content-between py-2">
    <!-- Logo -->
    <div class="logo mx-auto d-lg-block">
      <a href="{{ url('/') }}">
        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Logo">
      </a>
    </div>

    <!-- Desktop Search -->
    <div class="search-bar-wrapper d-flex justify-content-center flex-grow-1">
      <div class="position-relative search-container" style="width:100%;max-width:600px;">
        <form action="#" method="GET" class="search-bar-desktop d-flex align-items-center">
          <input type="text" name="query" id="global-search-input-desktop" class="form-control rounded-0"
                 placeholder="Search your products..." style="border-top-left-radius:8px;border-bottom-left-radius:8px;">
          <button type="submit" class="btn rounded-0" style="border-top-right-radius:8px;border-bottom-right-radius:8px;">
            <i class="fas fa-search"></i>
          </button>
        </form>
        <!-- DESKTOP results -->
        <div id="global-search-results-desktop" class="list-group"></div>
      </div>
    </div>

    <!-- Desktop User -->
    <div class="d-flex align-items-center header-icons">
      <div class="user-dropdown" id="userDropDesktop">
       
        <div class="user-menu" id="userMenuDesktop" role="menu" aria-hidden="true">
          <div class="user-menu-inner text-center">
            <div><a href="#" class="top-link d-flex align-items-center justify-content-center">
              <i class="fas fa-user-plus me-2"></i> Sign up</a></div>
            <div><a href="#" class="top-link d-flex align-items-center justify-content-center">
              <i class="fas fa-sign-in-alt me-2"></i> Login</a></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Desktop Cart -->
    <a href="#" class="ms-3 text-dark position-relative" aria-label="Cart">
      <i class="fas fa-shopping-cart fa-2x"></i>
      <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill">
        {{-- {{ $totalCartQuantity ?? 0 }} --}}
      </span>
    </a>
  </div>

  <!-- Mobile search dropdown -->
  <div class="mobile-search-bar d-lg-none px-3 pt-2" id="mobileSearchBar">
    <div class="position-relative search-container">
      <form action="#" method="GET" class="d-flex">
        <input type="text" name="query" id="global-search-input-mobile" class="form-control rounded-0"
               placeholder="Search your products...">
        <button type="submit" class="btn btn-dark"><i class="fas fa-search"></i></button>
      </form>
      <!-- MOBILE results -->
      <div id="global-search-results-mobile" class="list-group"></div>
    </div>
  </div>

<nav class="main-nav d-none d-lg-block">
    <div class="nav-scroll-wrapper">
        <div class="nav-inner d-flex flex-nowrap text-white py-1 px-3">
            <a href="{{ route('advanced') }}" class="nav-link-item">Fruits & Vegetables</a>
            <a href="{{ route('advanced') }}" class="nav-link-item">Dairy & Eggs</a>
            <a href="{{ route('advanced') }}" class="nav-link-item">Snacks & Beverages</a>
            <a href="{{ route('advanced') }}" class="nav-link-item">Bakery</a>
            <a href="{{ route('advanced') }}" class="nav-link-item">Personal Care</a>
            <a href="{{ route('advanced') }}" class="nav-link-item">Household</a>
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
      <a class="mobile-nav-link" href="#">Fruits & Vegetables</a>
    <a class="mobile-nav-link" href="#">Dairy & Eggs</a>
    <a class="mobile-nav-link" href="#">Snacks & Beverages</a>
    <a class="mobile-nav-link" href="#">Bakery</a>
    <a class="mobile-nav-link" href="#">Personal Care</a>
    <a class="mobile-nav-link" href="#">Household</a>
    </div>
  </aside>
</header>


<style>
/* Sticky header */
.header-area {
  position: sticky; top: 0; z-index: 1000; background:#fff;
  box-shadow: 0 4px 6px rgba(0,0,0,.08);
}

/* Prevent background scroll when drawer is open */
.no-scroll { overflow: hidden; touch-action: none; }

/* Backdrop */
.mobile-nav-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 998; backdrop-filter: saturate(1) blur(1px); }

/* Drawer */
.mobile-nav-drawer {
  position: fixed; top: 0; left: 0; height: 100dvh;
  width: 88vw; max-width: 360px; background: #fff; color:#111;
  box-shadow: 2px 0 24px rgba(0,0,0,.15); border-right: 1px solid #eee;
  transform: translateX(-100%); transition: transform .22s cubic-bezier(.2,.7,.3,1);
  z-index: 999; outline: none; display: flex; flex-direction: column;
  padding-bottom: env(safe-area-inset-bottom, 0);
}
@media (prefers-reduced-motion: reduce) { .mobile-nav-drawer { transition: none; } }
.mobile-nav-drawer.open { transform: translateX(0); }
.mobile-nav-header { display:flex; align-items:center; justify-content:space-between; padding: .9rem 1rem; border-bottom: 1px solid #eee; font-weight: 700; }
.close-btn { border:none; background:transparent; font-size:1.8rem; line-height:1; cursor:pointer; min-width:44px; min-height:44px; display:inline-flex; align-items:center; justify-content:center; }

/* Drawer body */
.mobile-nav-body { padding: .5rem 0; overflow-y: auto; -webkit-overflow-scrolling: touch; flex: 1 1 auto; }
.mobile-nav-link { display:block; padding:.85rem 1rem; color:#111; text-decoration:none; border-bottom:1px solid #f3f4f6; }
.mobile-nav-link:hover, .mobile-nav-link:active { background:#f9fafb; }

/* ===== MOBILE SEARCH VISIBILITY (responsive, no !important) ===== */
@media (max-width: 991.98px) {
  .mobile-search-bar { display: none; }
  .mobile-search-bar.show { display: block; }
}
@media (min-width: 992px) {
  .mobile-search-bar { display: none; } /* force hidden on desktop */
}

/* Cart badge */
.cart-badge {
  font-size:.75rem; padding:3px 6px; min-width:18px; min-height:18px;
  display:inline-flex; align-items:center; justify-content:center;
  background: linear-gradient(to right, #0A0F2C, red);
  color:#fff; border-radius:30px; font-weight:700; box-shadow:0 2px 6px rgba(0,0,0,.2);
}

/* Desktop nav look */
.nav-scroll-wrapper { overflow-x:auto; }
.nav-link-item { color:#fff; text-decoration:none; padding:.4rem .8rem; white-space:nowrap; }

/* Tap sizes */
.hamburger, .search-toggle-btn, .user-icon {
  min-width:44px; min-height:44px; display:inline-flex; align-items:center; justify-content:center;
  border: none; background: transparent; font-size:1.2rem;
}

/* Small spacing fix for the mobile right cluster */
.d-flex.align-items-center.gap-2 > * { margin-right:.5rem; }
.d-flex.align-items-center.gap-2 > *:last-child { margin-right:0; }

/* User dropdown */
.user-dropdown { position: relative; }
.user-dropdown .user-menu {
  position: absolute; right: 0; top: 120%;
  background:#fff; border:1px solid #eee; border-radius:8px; min-width: 180px;
  padding:.5rem; display:none; z-index: 1001;
}
.user-dropdown.show .user-menu { display:block; }

/* Search results positioning */
.search-container { position: relative; }
#global-search-results-desktop, #global-search-results-mobile {
  position: absolute; top: calc(100% + 4px); left: 0; right: 0; width: auto;
  box-sizing: border-box; display: none; max-height: 60vh; overflow-y: auto;
  border: 1px solid #ccc; background: #fff; border-radius: 0 0 8px 8px; z-index: 9999;
}
#global-search-results-desktop .list-group-item,
#global-search-results-mobile .list-group-item { cursor: pointer; }
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
