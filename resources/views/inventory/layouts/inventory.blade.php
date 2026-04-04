<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>[x-cloak]{display:none!important;}</style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Inventory')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
  <div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-72 min-w-72 flex-shrink-0 bg-white border-r border-slate-200 hidden md:block">
      <div class="p-6">
        <div class="flex items-center space-x-2">
  <img src="{{ asset('assets/img/logo/logo.png') }}" alt="GroceMate Logo" class="h-10 w-auto">
  <!-- Optional: Keep small text next to logo -->
  <span class="text-lg font-bold text-slate-900">GroceMate</span>
</div>

        <div class="text-xs text-slate-500 mt-1">Inventory Module</div>
      </div>
      @php
        $isInventoryDashboard = request()->routeIs('inventory.dashboard');
        $isInventoryGroup = request()->routeIs(
            'inventory.categories.*',
            'inventory.products.*',
            'inventory.suppliers.*',
            'inventory.purchases.*',
            'inventory.brands.*',
            'inventory.alerts.*'
        );
        $isPosGroup = request()->routeIs(
            'pos.dashboard',
            'pos.invoices.*',
            'pos.customers.*',
            'pos.supplier-payments.*',
            'pos.income.*'
        );
        $isEcommerceGroup = request()->routeIs(
          'inventory.ecommerce-products.*',
          'inventory.ecommerce-brands.*',
          'inventory.ecommerce-categories.*'
        );
        $isBusinessProfile = request()->routeIs('business.*');
        $isAccountsGroup = request()->routeIs('admin.accounts.*');
        $isSettingsGroup = request()->routeIs(['taxes.*', 'admin.accounts.*']);

        $navLinkClass = function (bool $active = false) {
            return $active
                ? 'block px-3 py-2 rounded-lg bg-blue-50 text-blue-700 font-medium border border-blue-100 shadow-sm'
                : 'block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100';
        };

        $navButtonClass = function (bool $active = false) {
            return $active
                ? 'w-full flex items-center justify-between px-3 py-2 rounded-lg bg-slate-900 text-white'
                : 'w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100';
        };

        $navSectionLinkClass = function (bool $active = false) {
            return $active
                ? 'block px-3 py-2 rounded-lg bg-slate-900 text-white font-medium'
                : 'block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-100';
        };
      @endphp
      <nav class="px-3 pb-6 space-y-1">

  <!-- Dashboard -->
  <a class="{{ $navSectionLinkClass($isInventoryDashboard) }}"
     href="{{ route('inventory.dashboard') }}">
     Dashboard
  </a>

  <!-- Inventory Dropdown -->
  <div x-data="{ open: {{ $isInventoryGroup ? 'true' : 'false' }} }" class="space-y-1">
    
    <!-- Dropdown Button -->
    <button @click="open = !open"
            class="{{ $navButtonClass($isInventoryGroup) }}">
      <span>Inventory</span>
      <svg :class="{'rotate-180': open}" 
           class="w-4 h-4 transition-transform duration-200"
           fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <!-- Dropdown Items -->
    <div x-show="open" x-transition class="ml-4 space-y-1">

      <a class="{{ $navLinkClass(request()->routeIs('inventory.categories.*')) }}"
         href="{{ route('inventory.categories.index') }}">
         Category
      </a>

      <a class="{{ $navLinkClass(request()->routeIs('inventory.brands.*')) }}"
         href="{{ route('inventory.brands.index') }}">
         Brands
      </a>

      <a class="{{ $navLinkClass(request()->routeIs('inventory.products.*')) }}"
         href="{{ route('inventory.products.index') }}">
         Products
      </a>

      <a class="{{ $navLinkClass(request()->routeIs('inventory.suppliers.*')) }}"
         href="{{ route('inventory.suppliers.index') }}">
         Suppliers
      </a>

      <a class="{{ $navLinkClass(request()->routeIs('inventory.purchases.*')) }}"
         href="{{ route('inventory.purchases.index') }}">
         Purchases (Stock-In)
      </a>

      <a class="{{ $navLinkClass(request()->routeIs('inventory.alerts.*')) }}"
         href="{{ route('inventory.alerts.expiry') }}">
         Expiry Alerts
      </a>

    </div>
  </div>
  <div x-data="{ open: {{ $isEcommerceGroup ? 'true' : 'false' }} }" class="space-y-1">
    
    <!-- Dropdown Button -->
    <button @click="open = !open"
            class="{{ $navButtonClass($isEcommerceGroup) }}">
      <span>POS</span>
      <svg :class="{'rotate-180': open}" 
           class="w-4 h-4 transition-transform duration-200"
           fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <!-- Dropdown Items -->
    <div x-show="open" x-transition class="ml-4 space-y-1">

      <a class="{{ $navLinkClass(request()->routeIs('pos.dashboard')) }}"
         href="{{ route('pos.dashboard') }}">
         Dashboard
      </a>

      <a class="{{ $navLinkClass(request()->routeIs('pos.invoices.*')) }}"
         href="{{ route('pos.invoices.index') }}">
         New Sale
      </a>

      <a class="{{ $navLinkClass(request()->routeIs('pos.customers.*')) }}"
         href="{{ route('pos.customers.index') }}">
         Customers
      </a>

      <a class="{{ $navLinkClass(request()->routeIs('pos.supplier-payments.*')) }}"
         href="{{ route('pos.supplier-payments.index') }}">
         Payments
      </a>

      <a class="{{ $navLinkClass(request()->routeIs('pos.income.*')) }}"
         href="{{ route('pos.income.index') }}">
         Income
      </a>

    </div>
  </div>
  <div x-data="{ open: {{ $isEcommerceGroup ? 'true' : 'false' }} }" class="space-y-1">
    
    <!-- Dropdown Button -->
    <button @click="open = !open"
            class="{{ $navButtonClass($isEcommerceGroup) }}">
      <span>Ecommerce</span>
      <svg :class="{'rotate-180': open}" 
           class="w-4 h-4 transition-transform duration-200"
           fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <!-- Dropdown Items -->
    <div x-show="open" x-transition class="ml-4 space-y-1">

      <a class="{{ $navLinkClass(request()->routeIs('inventory.ecommerce-products.*')) }}"
        href="{{ route('inventory.ecommerce-products.index') }}">
        Product
      </a>

      <a class="{{ $navLinkClass(request()->routeIs('inventory.ecommerce-brands.*')) }}"
        href="{{ route('inventory.ecommerce-brands.index') }}">
        Brand
      </a>

      <a class="{{ $navLinkClass(request()->routeIs('inventory.ecommerce-categories.*')) }}"
        href="{{ route('inventory.ecommerce-categories.index') }}">
        Category
      </a>

    </div>
  </div>
  <!-- Dashboard -->
  <a class="{{ $navSectionLinkClass($isBusinessProfile) }}"
     href="{{ route('business.index') }}">
     Profile
  </a>
  <div x-data="{ open: {{ $isSettingsGroup ? 'true' : 'false' }} }" class="space-y-1">
    
    <!-- Dropdown Button -->
    <button @click="open = !open"
            class="{{ $navButtonClass($isSettingsGroup) }}">
      <span>Settings</span>
      <svg :class="{'rotate-180': open}" 
           class="w-4 h-4 transition-transform duration-200"
           fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <!-- Dropdown Items -->
    <div x-show="open" x-transition class="ml-4 space-y-1">

      @if(auth()->check() && auth()->user()->isAdmin())
    
      <a class="{{ $navLinkClass($isAccountsGroup) }}"
         href="{{ route('admin.accounts.index') }}">
         Manage Admins
      </a>
      
      <a class="{{ $navLinkClass(request()->routeIs('taxes.*')) }}"
         href="{{ route('taxes.index') }}">
         Tax Settings
      </a>
    
  @endif

    </div>
  </div>

  

</nav>

    </aside>

    <!-- Main -->
    <main class="flex-1 min-w-0 overflow-hidden">
      <!-- Topbar -->
      <header class="bg-white border-b border-slate-200">
        <div class="px-6 py-4 flex items-center justify-between">
          <div>
            <div class="text-sm text-slate-500">@yield('subtitle', 'Manage inventory')</div>
            <div class="text-lg font-semibold">@yield('heading', 'Inventory')</div>
          </div>

          <div class="flex items-center gap-3">
            <div class="text-sm text-slate-600 hidden sm:block">
              {{ auth()->user()->full_name ?? 'User' }}
            </div>
            <form method="POST" action="#">
              @csrf
              <button class="px-3 py-2 text-sm rounded-lg bg-slate-900 text-white hover:bg-slate-800">
                Logout
              </button>
            </form>
          </div>
        </div>
      </header>

      <div class="p-6">
        @if(session('success'))
          <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            {{ session('success') }}
          </div>
        @endif

        @if($errors->any())
          <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="font-semibold mb-2">Please fix the following:</div>
            <ul class="list-disc ml-5 space-y-1">
              @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
          </div>
        @endif

        @yield('content')
      </div>
    </main>
  </div>
   @stack('scripts')  {{-- ✅ THIS LINE --}}
  <script>
    document.addEventListener('click', function (event) {
      const backButton = event.target.closest('[data-back-button]');

      if (!backButton) {
        return;
      }

      const currentUrl = window.location.href;
      const referrer = document.referrer;

      if (!referrer) {
        return;
      }

      try {
        const referrerUrl = new URL(referrer);
        const currentLocation = new URL(currentUrl);

        if (referrerUrl.origin === currentLocation.origin && referrer !== currentUrl) {
          event.preventDefault();
          window.history.back();
        }
      } catch (error) {
        return;
      }
    });
  </script>
</body>
</html>
