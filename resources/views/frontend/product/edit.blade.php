@extends('inventory.layouts.inventory')

@section('title', 'Edit E-commerce Product')
@section('heading', 'Edit E-commerce Product')
@section('subtitle', 'Update product settings for online store')

@section('content')
<div class="space-y-6">
    <form method="POST" action="{{ route('inventory.ecommerce-products.update', $ecommerceProduct) }}" enctype="multipart/form-data"
          class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6">
        @csrf
        @method('PUT')

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
                    <label class="text-sm font-medium text-slate-700">Product</label>
                    <div class="mt-1 p-4 bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl border border-slate-200">
                        <div class="flex items-center gap-3">
                            @if($ecommerceProduct->thumbnail)
                                <img src="{{ Storage::url($ecommerceProduct->thumbnail) }}" 
                                     alt="{{ $ecommerceProduct->product->name }}"
                                     class="w-14 h-14 rounded-xl object-cover border border-slate-200 shadow-sm">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg">
                                    {{ substr($ecommerceProduct->product->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <div class="font-semibold text-slate-900">{{ $ecommerceProduct->product->name }}</div>
                                <div class="text-sm text-slate-500">
                                    {{ $ecommerceProduct->product->category->name ?? 'N/A' }} • {{ $ecommerceProduct->product->brandRelation->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">SKU (optional)</label>
                    <input name="sku" value="{{ old('sku', $ecommerceProduct->sku) }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                           placeholder="e.g. PRD-001" />
                </div>

                <input type="hidden" name="display_section" value="product_grid">
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                      <label class="text-sm font-medium text-slate-700">Purchase Price</label>
                      <input type="text" id="purchase-price-display" readonly
                          class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-500"
                          value="{{ number_format((float) ($ecommerceProduct->product->latestPurchaseItem->unit_cost ?? 0), 2) }}" />
                </div>

                <div>
                      <label class="text-sm font-medium text-slate-700">Sale Price *</label>
                    <input name="mrp" type="number" step="any" data-money inputmode="numeric" max="9999999" value="{{ old('mrp', $ecommerceProduct->mrp) }}" required
                           id="mrp-input"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Discount (%)</label>
                    <input name="discount_percent" type="number" step="0.01" value="{{ old('discount_percent', $ecommerceProduct->discount_percent) }}"
                           id="discount-input" min="0" max="100"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Display Price</label>
                    <input type="text" id="display-price" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-emerald-50 px-3 py-2.5 text-sm font-semibold text-emerald-700" 
                           value="{{ number_format((float) $ecommerceProduct->display_price, 2) }}" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Ecommerce Stock *</label>
                    <input name="ecommerce_stock" type="number" step="0.001" min="0" value="{{ old('ecommerce_stock', $ecommerceProduct->ecommerce_stock ?? 0) }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                           placeholder="0.000" />
                    <p class="text-xs text-slate-500 mt-1">Reserve how much inventory stock should be available for ecommerce.</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Inventory Stock</label>
                    <input type="text" id="inventory-stock-display" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-500"
                           value="{{ number_format((float) ($ecommerceProduct->product->stock->quantity ?? 0), 3) }}" />
                    <p class="text-xs text-slate-500 mt-1">Total stock available for the selected product.</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Stock Left</label>
                    <input type="text" id="stock-left-display" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-amber-50 px-3 py-2.5 text-sm font-semibold text-amber-700"
                           value="0.000" />
                    <p class="text-xs text-slate-500 mt-1">Inventory stock minus ecommerce stock.</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Profit</label>
                    <input type="text" id="profit-display" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-blue-50 px-3 py-2.5 text-sm font-semibold text-blue-700" 
                           value="{{ number_format((float) $ecommerceProduct->profit, 2) }}" />
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
                <input name="meta_keywords" value="{{ old('meta_keywords', $ecommerceProduct->meta_keywords) }}"
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
                <textarea name="description" id="description-input" class="hidden">{{ old('description', $ecommerceProduct->description) }}</textarea>
                <div id="description-editor"
                     class="mt-1 rounded-xl border border-slate-300 bg-white text-sm"></div>
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
            <div class="space-y-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if($ecommerceProduct->thumbnail)
                        <div>
                            <img src="{{ Storage::url($ecommerceProduct->thumbnail) }}"
                                 alt="Current thumbnail"
                                 class="w-full h-28 rounded-xl object-cover border-2 border-slate-200 shadow-sm">
                            <p class="text-xs text-slate-500 mt-2 text-center">Main image</p>
                        </div>
                    @endif

                    @forelse($ecommerceProduct->images as $image)
                        <div>
                            <img src="{{ Storage::url($image->image_path) }}"
                                 alt="Product thumbnail"
                                 class="w-full h-28 rounded-xl object-cover border-2 border-slate-200 shadow-sm">
                            <p class="text-xs text-slate-500 mt-2 text-center">
                                {{ $image->is_primary ? 'Primary thumbnail' : 'Additional image' }}
                            </p>
                        </div>
                    @empty
                        @if(!$ecommerceProduct->thumbnail)
                            <div class="col-span-2 md:col-span-4 text-sm text-slate-500">No thumbnails uploaded yet.</div>
                        @endif
                    @endforelse
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Upload More Thumbnails</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-slate-400 transition-all duration-200">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-slate-600">
                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>Upload files</span>
                                    <input type="file" name="thumbnails[]" accept="image/jpeg,image/png,image/jpg" multiple class="sr-only" />
                                </label>
                                <p class="pl-1">or drag and drop multiple images</p>
                            </div>
                            <p class="text-xs text-slate-500">PNG, JPG up to 2MB each</p>
                        </div>
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
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200 text-sm font-medium">
                Update Product
            </button>
        </div>
    </form>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<style>
    #description-editor .ql-editor {
        min-height: 220px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mrpInput = document.getElementById('mrp-input');
        const discountInput = document.getElementById('discount-input');
        const displayPriceInput = document.getElementById('display-price');
        const profitDisplay = document.getElementById('profit-display');
        const purchasePriceDisplay = document.getElementById('purchase-price-display');
        const inventoryStockDisplay = document.getElementById('inventory-stock-display');
        const stockLeftDisplay = document.getElementById('stock-left-display');
        const ecommerceStockInput = document.querySelector('input[name="ecommerce_stock"]');
        const descriptionInput = document.getElementById('description-input');
        const form = mrpInput.closest('form');
        let descriptionEditor = null;

        const purchasePrice = parseFloat(purchasePriceDisplay.value.replace(/,/g, '')) || 0;
        const inventoryStock = parseFloat(inventoryStockDisplay.value.replace(/,/g, '')) || 0;
        const currentEcommerceStock = parseFloat(ecommerceStockInput?.value) || 0;

        function calculatePrices() {
            const mrp = parseFloat(mrpInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const displayPrice = mrp - (mrp * discount / 100);
            const profit = displayPrice - purchasePrice;
            const ecommerceStock = parseFloat(ecommerceStockInput?.value) || currentEcommerceStock;
            const stockLeft = inventoryStock - ecommerceStock;

            displayPriceInput.value = displayPrice.toFixed(2);
            profitDisplay.value = profit.toFixed(2);
            if (stockLeftDisplay) {
                stockLeftDisplay.value = Math.max(stockLeft, 0).toFixed(3);
            }
        }

        mrpInput.addEventListener('input', calculatePrices);
        // Debounce discount recalculation so FP drift on partial values (e.g. 31→30.98) is avoided.
        let _discountTimer;
        discountInput.addEventListener('input', () => {
            clearTimeout(_discountTimer);
            _discountTimer = setTimeout(calculatePrices, 300);
        });
        if (ecommerceStockInput) {
            ecommerceStockInput.addEventListener('input', calculatePrices);
        }

        if (window.Quill && descriptionInput) {
            descriptionEditor = new Quill('#description-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link'],
                        ['clean']
                    ]
                }
            });

            if (descriptionInput.value) {
                descriptionEditor.root.innerHTML = descriptionInput.value;
            }

            if (form) {
                form.addEventListener('submit', function () {
                    descriptionInput.value = descriptionEditor.root.innerHTML;
                });
            }
        }

        calculatePrices();
    });
</script>
@endpush
@endsection
