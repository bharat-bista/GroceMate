@extends('inventory.layouts.inventory')

@section('title', $brand->name . ' - E-commerce Brand')
@section('heading', $brand->name)
@section('subtitle', 'E-commerce products from this brand')

@section('content')
<div class="space-y-6">

    {{-- Back Button --}}
    <a href="{{ route('inventory.ecommerce-brands.index') }}" 
       class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium text-sm">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Brands
    </a>

    {{-- Brand Card --}}
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-gradient-to-r from-purple-50 to-blue-50">
            <div class="flex flex-col md:flex-row items-center gap-6">
                
                {{-- Brand Logo --}}
                <div class="w-32 h-32 rounded-2xl bg-white border-2 border-purple-200 flex items-center justify-center overflow-hidden shadow-lg">
                    @if($brand->image)
                        <img src="{{ Storage::url($brand->image) }}" 
                             alt="{{ $brand->name }}"
                             class="w-full h-full object-contain p-3">
                    @else
                        <span class="text-5xl font-bold text-purple-400">
                            {{ strtoupper(substr($brand->name, 0, 2)) }}
                        </span>
                    @endif
                </div>

                {{-- Brand Info --}}
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-3xl font-bold text-slate-900">{{ $brand->name }}</h1>
                    <p class="text-slate-600 mt-2">{{ $products->total() }} {{ Str::plural('product', $products->total()) }} in e-commerce</p>
                </div>

                {{-- Stats --}}
                <div class="flex gap-4">
                    <div class="bg-white rounded-xl px-6 py-4 text-center border border-purple-200 shadow">
                        <p class="text-3xl font-bold text-purple-600">{{ $products->total() }}</p>
                        <p class="text-xs text-slate-500 mt-1">Products</p>
                    </div>
                    <div class="bg-white rounded-xl px-6 py-4 text-center border border-blue-200 shadow">
                        <p class="text-3xl font-bold text-blue-600">{{ $inStock }}</p>
                        <p class="text-xs text-slate-500 mt-1">In Stock</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="bg-white shadow-xl rounded-3xl border border-slate-200 overflow-hidden">

        <div class="p-6 border-b border-slate-200">
            <h2 class="text-xl font-bold text-slate-900">📦 Products</h2>
        </div>

        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-left">#</th>
                            <th class="px-6 py-4 text-left">Image</th>
                            <th class="px-6 py-4 text-left">Product</th>
                            <th class="px-6 py-4 text-left">SKU</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Display Price</th>
                            <th class="px-6 py-4 text-center">Stock</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($products as $index => $ep)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 text-slate-500 font-medium">
                                    {{ $products->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $primaryImage = $ep->images()->orderBy('sort_order')->first();
                                    @endphp
                                    @if($primaryImage)
                                        <img src="{{ Storage::url($primaryImage->image_path) }}" 
                                             alt="{{ $ep->product->name }}"
                                             class="w-12 h-12 rounded-lg object-cover border border-slate-200">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    {{ $ep->product->name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-mono text-xs">
                                    {{ $ep->sku ?: '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusColors = [
                                            'in_stock' => 'bg-green-100 text-green-800',
                                            'out_of_stock' => 'bg-red-100 text-red-800',
                                            'coming_soon' => 'bg-yellow-100 text-yellow-800',
                                            'discontinued' => 'bg-slate-100 text-slate-800',
                                        ];
                                        $statusLabels = [
                                            'in_stock' => 'In Stock',
                                            'out_of_stock' => 'Out of Stock',
                                            'coming_soon' => 'Coming Soon',
                                            'discontinued' => 'Discontinued',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$ep->status] ?? 'bg-slate-100 text-slate-800' }}">
                                        {{ $statusLabels[$ep->status] ?? ucfirst($ep->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-slate-900">
                                    ₹{{ number_format($ep->display_price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-semibold {{ $ep->ecommerce_stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $ep->ecommerce_stock ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('inventory.ecommerce-products.edit', $ep) }}" 
                                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                                           title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('inventory.ecommerce-products.show', $ep) }}" 
                                           class="p-2 text-slate-600 hover:bg-slate-50 rounded-lg transition" 
                                           title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 border-t border-slate-200">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-1">No products found</h3>
                <p class="text-sm text-slate-500">This brand has no e-commerce products yet.</p>
            </div>
        @endif

    </div>

</div>
@endsection
