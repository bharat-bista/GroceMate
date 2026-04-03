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
                    <select name="product_id" id="product-select" required size="1"
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                            style="max-height: 300px;">
                        <option value="">Select a product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-category="{{ $product->category->name ?? 'N/A' }}"
                                    data-brand="{{ $product->brandRelation->name ?? 'N/A' }}"
                                    data-purchase-price="{{ $product->latestPurchaseItem->unit_cost ?? 0 }}"
                                    data-inventory-stock="{{ $product->stock->quantity ?? 0 }}"
                                    @selected(old('product_id') == $product->id)>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Select from inventory products</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Category</label>
                    <input type="text" id="category-display" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-600 cursor-not-allowed"
                           value="" placeholder="Auto-filled from product" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Company / Brand</label>
                    <input type="text" id="brand-display" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-600 cursor-not-allowed"
                           value="" placeholder="Auto-filled from product" />
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

            <!-- Stock Section -->
            <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Inventory Stock</label>
                        <input type="text" id="inventory-stock-display" readonly
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-600 cursor-not-allowed"
                               value="0" />
                        <p class="text-xs text-slate-400 mt-1">Available in inventory</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">E-commerce Stock</label>
                        <input name="ecommerce_stock" type="number" step="0.001" value="{{ old('ecommerce_stock', 0) }}"
                               id="ecommerce-stock-input" min="0"
                               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                               placeholder="0" />
                        <p class="text-xs text-slate-400 mt-1">Allocate for e-commerce</p>
                        @error('ecommerce_stock')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">Remaining Inventory</label>
                        <input type="text" id="remaining-stock-display" readonly
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm font-semibold text-slate-600 cursor-not-allowed"
                               value="0" />
                        <p class="text-xs text-slate-400 mt-1">After allocation</p>
                    </div>
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
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div>
                    <label class="text-sm font-medium text-slate-700">Purchase Price</label>
                    <input type="text" id="purchase-price-display" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600 cursor-not-allowed" 
                           value="0.00" />
                    <p class="text-xs text-slate-400 mt-1">Auto-filled from inventory</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Selling Price *</label>
                    <input name="mrp" type="number" step="0.01" value="{{ old('mrp', 0) }}" required
                           id="mrp-input"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                           placeholder="0.00" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Discount (%)</label>
                    <input name="discount_percent" type="number" step="0.01" value="{{ old('discount_percent', 0) }}"
                           id="discount-input" min="0" max="100"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                           placeholder="0" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Display Price</label>
                    <input type="text" id="display-price" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-emerald-50 px-3 py-2.5 text-sm font-semibold text-emerald-700 cursor-not-allowed" 
                           value="0.00" />
                    <p class="text-xs text-slate-400 mt-1">Selling Price - Discount</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Profit</label>
                    <input type="text" id="profit-display" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-blue-50 px-3 py-2.5 text-sm font-semibold text-blue-700 cursor-not-allowed" 
                           value="0.00" />
                    <p class="text-xs text-slate-400 mt-1">Display Price - Purchase</p>
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
                <div id="description-editor" class="bg-white rounded-xl border border-slate-300"></div>
                <input type="hidden" name="description" id="description-input" value="{{ old('description') }}">
            </div>
        </div>

        <!-- Product Images -->
        <div class="border-b border-slate-200 pb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Product Images</h3>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-700">Upload Images (Multiple)</label>
                <p class="text-xs text-slate-500 mt-1 mb-2">First image will be set as the primary thumbnail</p>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-slate-400 transition-all duration-200" id="image-drop-zone">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-slate-600">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                <span>Upload files</span>
                                <input type="file" name="images[]" accept="image/jpeg,image/png,image/jpg,image/webp" multiple class="sr-only" id="images-input" />
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-slate-500">PNG, JPG, WEBP up to 2MB each | You can select multiple images</p>
                    </div>
                </div>
                <!-- Image Preview -->
                <div id="image-preview-container" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 hidden">
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
<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<style>
    #description-editor {
        min-height: 200px;
    }
    #description-editor .ql-editor {
        min-height: 180px;
        font-size: 14px;
    }
    .ql-toolbar.ql-snow {
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        border-color: #cbd5e1;
    }
    .ql-editor.ql-blank::before {
        font-style: normal;
        color: #94a3b8;
    }
    /* Limit dropdown to show ~10 items with scroll */
    #product-select option {
        padding: 8px 12px;
    }
    #product-select:focus {
        overflow-y: auto;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quill Editor
        const quill = new Quill('#description-editor', {
            theme: 'snow',
            placeholder: 'Enter detailed product description...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'link'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        });

        // Set initial content if exists
        const initialContent = document.getElementById('description-input').value;
        if (initialContent) {
            quill.root.innerHTML = initialContent;
        }

        // Update hidden input on text change
        quill.on('text-change', function() {
            document.getElementById('description-input').value = quill.root.innerHTML;
        });

        // Pricing Calculator
        const productSelect = document.getElementById('product-select');
        const categoryDisplay = document.getElementById('category-display');
        const brandDisplay = document.getElementById('brand-display');
        const mrpInput = document.getElementById('mrp-input');
        const discountInput = document.getElementById('discount-input');
        const displayPriceInput = document.getElementById('display-price');
        const profitDisplay = document.getElementById('profit-display');
        const purchasePriceDisplay = document.getElementById('purchase-price-display');

        // Stock elements
        const inventoryStockDisplay = document.getElementById('inventory-stock-display');
        const ecommerceStockInput = document.getElementById('ecommerce-stock-input');
        const remainingStockDisplay = document.getElementById('remaining-stock-display');

        let purchasePrice = 0;
        let inventoryStock = 0;

        function calculatePrices() {
            const mrp = parseFloat(mrpInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const displayPrice = mrp - (mrp * discount / 100);
            const profit = displayPrice - purchasePrice;

            displayPriceInput.value = displayPrice.toFixed(2);
            profitDisplay.value = profit.toFixed(2);
        }

        function calculateRemainingStock() {
            const ecommerceStock = parseFloat(ecommerceStockInput.value) || 0;
            const remaining = inventoryStock - ecommerceStock;
            remainingStockDisplay.value = remaining.toFixed(3);

            // Visual feedback
            if (remaining < 0) {
                remainingStockDisplay.classList.add('text-red-600', 'bg-red-50');
                remainingStockDisplay.classList.remove('bg-slate-100', 'text-slate-600');
            } else {
                remainingStockDisplay.classList.remove('text-red-600', 'bg-red-50');
                remainingStockDisplay.classList.add('bg-slate-100', 'text-slate-600');
            }
        }

        function updateProductInfo() {
            const selected = productSelect.options[productSelect.selectedIndex];
            if (productSelect.value) {
                categoryDisplay.value = selected.dataset.category || 'N/A';
                brandDisplay.value = selected.dataset.brand || 'N/A';
                purchasePrice = parseFloat(selected.dataset.purchasePrice) || 0;
                purchasePriceDisplay.value = purchasePrice.toFixed(2);
                inventoryStock = parseFloat(selected.dataset.inventoryStock) || 0;
                inventoryStockDisplay.value = inventoryStock.toFixed(3);
                ecommerceStockInput.max = inventoryStock;
            } else {
                categoryDisplay.value = '';
                brandDisplay.value = '';
                purchasePrice = 0;
                purchasePriceDisplay.value = '0.00';
                inventoryStock = 0;
                inventoryStockDisplay.value = '0';
            }
            calculatePrices();
            calculateRemainingStock();
        }

        productSelect.addEventListener('change', updateProductInfo);
        ecommerceStockInput.addEventListener('input', calculateRemainingStock);

        mrpInput.addEventListener('input', calculatePrices);
        discountInput.addEventListener('input', calculatePrices);

        // Initialize on load
        updateProductInfo();

        // Multiple Image Upload Preview
        const imagesInput = document.getElementById('images-input');
        const previewContainer = document.getElementById('image-preview-container');
        const dropZone = document.getElementById('image-drop-zone');

        imagesInput.addEventListener('change', function() {
            previewImages(this.files);
        });

        // Drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('border-blue-500', 'bg-blue-50');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            });
        });

        dropZone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            imagesInput.files = files;
            previewImages(files);
        });

        function previewImages(files) {
            previewContainer.innerHTML = '';
            if (files.length === 0) {
                previewContainer.classList.add('hidden');
                return;
            }

            previewContainer.classList.remove('hidden');

            Array.from(files).forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <div class="aspect-square rounded-xl overflow-hidden border-2 ${index === 0 ? 'border-emerald-500' : 'border-slate-200'} shadow-sm">
                            <img src="${e.target.result}" class="w-full h-full object-cover" />
                        </div>
                        ${index === 0 ? '<span class="absolute top-1 left-1 bg-emerald-500 text-white text-xs px-2 py-0.5 rounded-full">Primary</span>' : ''}
                        <p class="text-xs text-slate-500 mt-1 truncate">${file.name}</p>
                    `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>
@endpush
@endsection
