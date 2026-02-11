<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Inventory')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
  <div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-72 bg-white border-r border-slate-200 hidden md:block">
      <div class="p-6">
        <div class="flex items-center space-x-2">
  <img src="{{ asset('assets/img/logo/logo.png') }}" alt="GroceMate Logo" class="h-10 w-auto">
  <!-- Optional: Keep small text next to logo -->
  <span class="text-lg font-bold text-slate-900">GroceMate</span>
</div>

        <div class="text-xs text-slate-500 mt-1">Inventory Module</div>
      </div>
      <nav class="px-3 pb-6 space-y-1">

  <!-- Dashboard -->
  <a class="block px-3 py-2 rounded-lg hover:bg-slate-100"
     href="{{ route('inventory.dashboard') }}">
     Dashboard
  </a>

  <!-- Inventory Dropdown -->
  <div x-data="{ open: false }" class="space-y-1">
    
    <!-- Dropdown Button -->
    <button @click="open = !open"
            class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-100">
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

      <a class="block px-3 py-2 rounded-lg hover:bg-slate-100"
         href="{{ route('inventory.categories.index') }}">
         Category
      </a>

      <a class="block px-3 py-2 rounded-lg hover:bg-slate-100"
         href="{{ route('inventory.products.index') }}">
         Products
      </a>

      <a class="block px-3 py-2 rounded-lg hover:bg-slate-100"
         href="{{ route('inventory.suppliers.index') }}">
         Suppliers
      </a>

      <a class="block px-3 py-2 rounded-lg hover:bg-slate-100"
         href="{{ route('inventory.purchases.index') }}">
         Purchases (Stock-In)
      </a>

      <a class="block px-3 py-2 rounded-lg hover:bg-slate-100"
         href="{{ route('inventory.alerts.expiry') }}">
         Expiry Alerts
      </a>

    </div>
  </div>
  <div x-data="{ open: false }" class="space-y-1">
    
    <!-- Dropdown Button -->
    <button @click="open = !open"
            class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-100">
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

      <a class="block px-3 py-2 rounded-lg hover:bg-slate-100"
         href="#">
         Dashboard
      </a>

      <a class="block px-3 py-2 rounded-lg hover:bg-slate-100"
         href="#">
         New Sale
      </a>

      <a class="block px-3 py-2 rounded-lg hover:bg-slate-100"
         href="{{ route('pos.customers.index') }}">
         Customers
      </a>

      <a class="block px-3 py-2 rounded-lg hover:bg-slate-100"
         href="#">
         Payments
      </a>

      <a class="block px-3 py-2 rounded-lg hover:bg-slate-100"
         href="#">
         Income
      </a>

    </div>
  </div>

</nav>

    </aside>

    <!-- Main -->
    <main class="flex-1">
      <!-- Topbar -->
      <header class="bg-white border-b border-slate-200">
        <div class="px-6 py-4 flex items-center justify-between">
          <div>
            <div class="text-sm text-slate-500">@yield('subtitle', 'Manage inventory')</div>
            <div class="text-lg font-semibold">@yield('heading', 'Inventory')</div>
          </div>

          <div class="flex items-center gap-3">
            <div class="text-sm text-slate-600 hidden sm:block">
              {{ auth()->user()->name ?? 'User' }}
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
</body>
</html>
