@extends('inventory.layouts.inventory')

@section('title', 'E-commerce Brands')
@section('heading', 'E-commerce Brands')
@section('subtitle', 'Brands with products in your online store')

@section('content')
<div class="space-y-6">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Brands</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalBrands }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Products</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalProducts }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- Brands Grid --}}
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">

        {{-- Header --}}
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">🏢 Brand Catalog</h2>
                    <p class="text-sm text-slate-600 mt-1">Brands are auto-populated from products added to e-commerce</p>
                </div>
            </div>
        </div>

        {{-- Search --}}
        <div class="p-4 bg-slate-50 border-b border-slate-200">
            <form method="GET" action="{{ route('inventory.ecommerce-brands.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Search Brands</label>
                    <input type="text" name="search" value="{{ $q }}" 
                           placeholder="Search by brand name..."
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-purple-500 focus:ring-purple-500" />
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium transition duration-200">
                        Search
                    </button>
                    <a href="{{ route('inventory.ecommerce-brands.index') }}"
                       class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium transition duration-200">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Results count --}}
        @if($brands->count() > 0)
            <div class="text-xs text-slate-500 px-6 pt-4 pb-2">
                Showing {{ $brands->firstItem() }} to {{ $brands->lastItem() }} of {{ $brands->total() }} brands
            </div>
        @endif

        {{-- Brands Grid --}}
        <div class="p-6">
            @if($brands->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($brands as $brand)
                        <a href="{{ route('inventory.ecommerce-brands.show', $brand) }}" 
                           class="group bg-white border border-slate-200 rounded-xl p-4 hover:shadow-lg hover:border-purple-300 transition-all duration-200">
                            <div class="aspect-square rounded-lg bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center mb-3 overflow-hidden">
                                @if($brand->image)
                                    <img src="{{ asset('assets/img/brands/' . $brand->image) }}" 
                                         alt="{{ $brand->name }}"
                                         class="w-full h-full object-contain p-2">
                                @else
                                    <span class="text-3xl font-bold text-purple-400">
                                        {{ strtoupper(substr($brand->name, 0, 2)) }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 text-center truncate group-hover:text-purple-600 transition">
                                {{ $brand->name }}
                            </h3>
                            <p class="text-xs text-slate-500 text-center mt-1">
                                {{ $brand->ecommerce_products_count }} {{ Str::plural('product', $brand->ecommerce_products_count) }}
                            </p>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $brands->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-1">No brands found</h3>
                    <p class="text-sm text-slate-500">Brands will appear here when you add products to e-commerce.</p>
                    <a href="{{ route('inventory.ecommerce-products.create') }}" 
                       class="inline-flex items-center mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add E-commerce Product
                    </a>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
