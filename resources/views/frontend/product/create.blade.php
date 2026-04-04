@extends('inventory.layouts.inventory')

@section('title', 'Add E-commerce Product')
@section('heading', 'Add E-commerce Product')
@section('subtitle', 'Configure product for online store')

@section('content')
<div class="space-y-6">
    <form method="POST" action="{{ route('inventory.ecommerce-products.store') }}" enctype="multipart/form-data"
          class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="border-b border-slate-200 pb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Basic Information</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Select Product *</label>
                    <select name="product_id" required 
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
                        <option value="">Select a product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-category="{{ $product->category->name ?? 'N/A' }}"
                                    data-brand="{{ $product->brandRelation->name ?? 'N/A' }}"
                                    data-purchase-price="{{ $product->latestPurchaseItem->unit_cost ?? 0 }}"
                                    @selected(old('product_id') == $product->id)>
                                {{ $product->name }} ({{ $product->category->name ?? 'N/A' }} - {{ $product->brandRelation->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Only products marked as "Listed" are shown here</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">SKU (optional)</label>
                    <input name="sku" value="{{ old('sku') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                           placeholder="e.g. PRD-001" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Product Status *</label>
                    <select name="status" required 
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
                        <option value="in_stock" @selected(old('status') == 'in_stock')>In Stock</option>
                        <option value="out_of_stock" @selected(old('status') == 'out_of_stock')>Out of Stock</option>
                        <option value="coming_soon" @selected(old('status') == 'coming_soon')>Coming Soon</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="border-b border-slate-200 pb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Pricing</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div>
                    <label class="text-sm font-medium text-slate-700">Previous Price</label>
                    <input name="previous_price" type="number" step="0.01" value="{{ old('previous_price') }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                           placeholder="0.00" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">MRP *</label>
                    <input name="mrp" type="number" step="0.01" value="{{ old('mrp', 0) }}" required
                           id="mrp-input"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Discount (%)</label>
                    <input name="discount_percent" type="number" step="0.01" value="{{ old('discount_percent', 0) }}"
                           id="discount-input" min="0" max="100"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Display Price</label>
                    <input type="text" id="display-price" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-emerald-50 px-3 py-2.5 text-sm font-semibold text-emerald-700" 
                           value="0.00" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Profit</label>
                    <input type="text" id="profit-display" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-blue-50 px-3 py-2.5 text-sm font-semibold text-blue-700" 
                           value="0.00" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Purchase Price</label>
                    <input type="text" id="purchase-price-display" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-500" 
                           value="0.00" />
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="border-b border-slate-200 pb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">SEO Settings</h3>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-700">Meta Keywords</label>
                <input name="meta_keywords" value="{{ old('meta_keywords') }}"
                       class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                       placeholder="e.g. TV, electronics, Samsung" />
            </div>
        </div>

        <!-- Product Description -->
        <div class="border-b border-slate-200 pb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Product Description</h3>
            </div>
            <div>
                <textarea name="description" rows="6"
                          class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                          placeholder="Enter detailed product description...">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Thumbnail Image -->
        <div class="border-b border-slate-200 pb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Thumbnail Image</h3>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-700">Upload Thumbnail</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-slate-400 transition-all duration-200">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-slate-600">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                <span>Upload a file</span>
                                <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/jpg" class="sr-only" />
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-slate-500">PNG, JPG up to 2MB | Suggested: 300×475 px</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between pt-4">
            <a href="{{ route('inventory.ecommerce-products.index') }}" data-back-button
               class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200 text-sm font-medium">
                Back
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition duration-200 text-sm font-medium">
                Save Product
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productSelect = document.querySelector('select[name="product_id"]');
        const mrpInput = document.getElementById('mrp-input');
        const discountInput = document.getElementById('discount-input');
        const displayPriceInput = document.getElementById('display-price');
        const profitDisplay = document.getElementById('profit-display');
        const purchasePriceDisplay = document.getElementById('purchase-price-display');

        let purchasePrice = 0;

        function calculatePrices() {
            const mrp = parseFloat(mrpInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const displayPrice = mrp - (mrp * discount / 100);
            const profit = displayPrice - purchasePrice;

            displayPriceInput.value = displayPrice.toFixed(2);
            profitDisplay.value = profit.toFixed(2);
        }

        productSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            purchasePrice = parseFloat(selected.dataset.purchasePrice) || 0;
            purchasePriceDisplay.value = purchasePrice.toFixed(2);
            calculatePrices();
        });

        mrpInput.addEventListener('input', calculatePrices);
        discountInput.addEventListener('input', calculatePrices);

        // Initialize on load
        if (productSelect.value) {
            const selected = productSelect.options[productSelect.selectedIndex];
            purchasePrice = parseFloat(selected.dataset.purchasePrice) || 0;
            purchasePriceDisplay.value = purchasePrice.toFixed(2);
        }
        calculatePrices();
    });
</script>
@endpush
@endsection
