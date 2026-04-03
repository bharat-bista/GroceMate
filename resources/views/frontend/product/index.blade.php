@extends('inventory.layouts.inventory')

@section('title', 'E-commerce Products')
@section('heading', 'E-commerce Products')
@section('subtitle', 'Manage products for online store')

@section('content')
<div class="space-y-6">

    {{-- Messages --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-100 text-red-700 border border-red-200 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Products</p>
                    <p class="text-3xl font-bold mt-2">{{ $ecommerceProducts->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm font-medium">In Stock</p>
                    <p class="text-3xl font-bold mt-2">{{ $ecommerceProducts->where('status', 'in_stock')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Out of Stock</p>
                    <p class="text-3xl font-bold mt-2">{{ $ecommerceProducts->where('status', 'out_of_stock')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-6 text-white border-2 border-black">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium">Coming Soon</p>
                    <p class="text-3xl font-bold mt-2">{{ $ecommerceProducts->where('status', 'coming_soon')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- Products Table --}}
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">

        {{-- Header --}}
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">🛒 E-commerce Catalog</h2>
                    <p class="text-sm text-slate-600 mt-1">Manage products visible on your online store</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('inventory.ecommerce-products.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Product
                    </a>
                </div>
            </div>
        </div>

        {{-- Search and Filters --}}
        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <form method="GET" action="{{ route('inventory.ecommerce-products.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ $q }}"
                               placeholder="Product name or SKU..."
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                        <select name="status"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="in_stock" @selected(request('status') == 'in_stock')>In Stock</option>
                            <option value="out_of_stock" @selected(request('status') == 'out_of_stock')>Out of Stock</option>
                            <option value="coming_soon" @selected(request('status') == 'coming_soon')>Coming Soon</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex items-end gap-3">
                        <button type="submit"
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition duration-200">
                            Search
                        </button>
                        <a href="{{ route('inventory.ecommerce-products.index') }}"
                           class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium transition duration-200">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Results count --}}
        @if($ecommerceProducts->count() > 0)
            <div class="text-xs text-slate-500 px-6 pt-4 pb-2">
                Showing {{ $ecommerceProducts->firstItem() }} to {{ $ecommerceProducts->lastItem() }} of {{ $ecommerceProducts->total() }} results
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">#</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Thumbnail</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Product</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">SKU</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">MRP</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Discount</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Display Price</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Profit</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Stock</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($ecommerceProducts as $index => $eProduct)
                        <tr class="hover:bg-slate-50 transition duration-150">

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-500">
                                    {{ ($ecommerceProducts->currentPage() - 1) * $ecommerceProducts->perPage() + $index + 1 }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $firstImage = $eProduct->images->sortBy('sort_order')->first();
                                @endphp
                                @if($firstImage)
                                    <div class="relative">
                                        <img src="{{ Storage::url($firstImage->image_path) }}" 
                                             alt="{{ $eProduct->product->name }}"
                                             class="w-14 h-14 rounded-xl object-cover border border-slate-200 shadow-sm">
                                        @if($eProduct->images->count() > 1)
                                            <span class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-medium">
                                                +{{ $eProduct->images->count() - 1 }}
                                            </span>
                                        @endif
                                    </div>
                                @elseif($eProduct->thumbnail)
                                    <img src="{{ Storage::url($eProduct->thumbnail) }}" 
                                         alt="{{ $eProduct->product->name }}"
                                         class="w-14 h-14 rounded-xl object-cover border border-slate-200 shadow-sm">
                                @else
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-r from-slate-100 to-slate-200 flex items-center justify-center border border-slate-200">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">{{ $eProduct->product->name }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ $eProduct->product->category->name ?? 'N/A' }} • {{ $eProduct->product->brandRelation->name ?? 'N/A' }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">{{ $eProduct->sku ?? '-' }}</div>
                                <div class="text-xs text-slate-400">ID: {{ $eProduct->id }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-900">Rs {{ number_format((float) $eProduct->mrp, 2) }}</div>
                                @if($eProduct->previous_price)
                                    <div class="text-xs text-slate-400 line-through">Rs {{ number_format((float) $eProduct->previous_price, 2) }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($eProduct->discount_percent > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        {{ $eProduct->discount_percent }}% OFF
                                    </span>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-emerald-600">Rs {{ number_format((float) $eProduct->display_price, 2) }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold {{ $eProduct->profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $eProduct->profit >= 0 ? '+' : '' }}Rs {{ number_format((float) $eProduct->profit, 2) }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold {{ ($eProduct->ecommerce_stock ?? 0) > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ number_format((float) ($eProduct->ecommerce_stock ?? 0), 0) }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    Inv: {{ number_format((float) ($eProduct->product->stock->quantity ?? 0), 0) }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'in_stock' => 'bg-emerald-100 text-emerald-800',
                                        'out_of_stock' => 'bg-red-100 text-red-800',
                                        'coming_soon' => 'bg-amber-100 text-amber-800',
                                    ];
                                    $statusLabels = [
                                        'in_stock' => 'In Stock',
                                        'out_of_stock' => 'Out of Stock',
                                        'coming_soon' => 'Coming Soon',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$eProduct->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $statusLabels[$eProduct->status] ?? $eProduct->status }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('inventory.ecommerce-products.edit', $eProduct) }}"
                                       class="text-blue-600 hover:text-blue-800 font-medium transition duration-150">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('inventory.ecommerce-products.destroy', $eProduct) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium transition duration-150">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="text-slate-500">
                                    <svg class="mx-auto h-16 w-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                                    </svg>
                                    <p class="text-lg font-medium">No e-commerce products found</p>
                                    <p class="text-sm mt-1">Add products to your online store to get started.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('inventory.ecommerce-products.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium transition duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Add Product
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ecommerceProducts->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $ecommerceProducts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
