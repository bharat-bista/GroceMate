@extends('inventory.layouts.inventory')

@section('title', 'Products')
@section('heading', 'Products')
@section('subtitle', 'Manage products and publish to E-commerce')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden mb-6">
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Product Catalog</h2>
                    <p class="text-sm text-slate-600 mt-1">Manage stock items, pricing, and E-commerce visibility</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('inventory.products.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Product
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <form method="GET" action="{{ route('inventory.products.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search', $q) }}"
                               placeholder="Product name or SKU..."
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div class="flex items-end gap-3">
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                            Search
                        </button>
                        <a href="{{ route('inventory.products.index') }}"
                           class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 text-sm font-medium">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Product</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Brand</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Category</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Purchase Price</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Stock</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">E-commerce</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($products as $product)
                        @php
                            $quantity = (float) ($product->stock->quantity ?? 0);
                            $reorderLevel = (float) ($product->stock->reorder_level ?? 0);
                            $isCritical = $quantity <= 0;
                            $isLow = !$isCritical && $reorderLevel > 0 && $quantity <= $reorderLevel;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900">{{ $product->name }}</div>
                                <div class="text-xs text-slate-500 mt-1">
                                    {{ $product->sku ?: 'No SKU' }} • {{ $product->unit }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-900">{{ $product->brandRelation ? $product->brandRelation->name : '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-900">{{ $product->category->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900">Rs {{ number_format((float) $product->selling_price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-slate-900">{{ number_format($quantity, 3) }}</span>
                                    <span class="inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-medium
                                        {{ $isCritical ? 'bg-red-100 text-red-700' : ($isLow ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                        {{ $isCritical ? 'Critical' : ($isLow ? 'Low' : 'Healthy') }}
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        {{ $reorderLevel > 0 ? 'Reorder: ' . number_format($reorderLevel, 3) : 'No reorder level set' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('inventory.products.toggle-listed', $product) }}">
                                    @csrf
                                    <button class="px-3 py-1.5 rounded-lg border text-sm font-medium transition
                                        {{ $product->is_listed ? 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                                        {{ $product->is_listed ? 'Listed' : 'Hidden' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                    {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $product->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('inventory.products.edit', $product) }}"
                                   class="text-green-600 hover:text-green-900 font-medium">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No products found</p>
                                    <p class="text-sm mt-1">Create your first product to start managing inventory.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('inventory.products.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Create Product
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
