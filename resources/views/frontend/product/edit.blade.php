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

                <div>
                    <label class="text-sm font-medium text-slate-700">Product Status *</label>
                    <select name="status" required 
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
                        <option value="in_stock" @selected(old('status', $ecommerceProduct->status) == 'in_stock')>In Stock</option>
                        <option value="out_of_stock" @selected(old('status', $ecommerceProduct->status) == 'out_of_stock')>Out of Stock</option>
                        <option value="coming_soon" @selected(old('status', $ecommerceProduct->status) == 'coming_soon')>Coming Soon</option>
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
                               value="{{ number_format((float) ($ecommerceProduct->product->stock->quantity ?? 0), 3) }}" />
                        <p class="text-xs text-slate-400 mt-1">Available in inventory</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">E-commerce Stock</label>
                        <input name="ecommerce_stock" type="number" step="0.001" 
                               value="{{ old('ecommerce_stock', $ecommerceProduct->ecommerce_stock ?? 0) }}"
                               id="ecommerce-stock-input" min="0"
                               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                               placeholder="0" />
                        <p class="text-xs text-slate-400 mt-1">Allocated for e-commerce</p>
                        @error('ecommerce_stock')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">Total Available</label>
                        <input type="text" id="total-stock-display" readonly
                               class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm font-semibold text-slate-600 cursor-not-allowed"
                               value="{{ number_format((float) (($ecommerceProduct->product->stock->quantity ?? 0) + ($ecommerceProduct->ecommerce_stock ?? 0)), 3) }}" />
                        <p class="text-xs text-slate-400 mt-1">Inventory + Current E-commerce</p>
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
                           value="{{ number_format((float) ($ecommerceProduct->product->latestPurchaseItem->unit_cost ?? 0), 2) }}" />
                    <p class="text-xs text-slate-400 mt-1">From inventory</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Selling Price *</label>
                    <input name="mrp" type="number" step="0.01" value="{{ old('mrp', $ecommerceProduct->mrp) }}" required
                           id="mrp-input"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                           placeholder="0.00" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Discount (%)</label>
                    <input name="discount_percent" type="number" step="0.01" value="{{ old('discount_percent', $ecommerceProduct->discount_percent) }}"
                           id="discount-input" min="0" max="100"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                           placeholder="0" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Display Price</label>
                    <input type="text" id="display-price" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-emerald-50 px-3 py-2.5 text-sm font-semibold text-emerald-700 cursor-not-allowed" 
                           value="{{ number_format((float) $ecommerceProduct->display_price, 2) }}" />
                    <p class="text-xs text-slate-400 mt-1">Selling Price - Discount</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Profit</label>
                    <input type="text" id="profit-display" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-blue-50 px-3 py-2.5 text-sm font-semibold text-blue-700 cursor-not-allowed" 
                           value="{{ number_format((float) $ecommerceProduct->profit, 2) }}" />
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
                <div id="description-editor" class="bg-white rounded-xl border border-slate-300"></div>
                <input type="hidden" name="description" id="description-input" value="{{ old('description', $ecommerceProduct->description) }}">
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

            <!-- Existing Images -->
            @if($ecommerceProduct->images->count() > 0)
            <div class="mb-4">
                <label class="text-sm font-medium text-slate-700 mb-2 block">Current Images</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" id="existing-images">
                    @foreach($ecommerceProduct->images->sortBy('sort_order') as $image)
                    <div class="relative group" data-image-id="{{ $image->id }}">
                        <div class="aspect-square rounded-xl overflow-hidden border-2 {{ $image->is_primary ? 'border-emerald-500' : 'border-slate-200' }} shadow-sm relative">
                            <img src="{{ Storage::url($image->image_path) }}" 
                                 class="w-full h-full object-cover" 
                                 alt="Product image" />
                            <!-- Overlay on hover -->
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                @if(!$image->is_primary)
                                <button type="button" 
                                        onclick="setPrimaryImage({{ $image->id }})"
                                        class="p-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition" title="Set as primary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                @endif
                                <button type="button" 
                                        onclick="markForDeletion({{ $image->id }})"
                                        class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @if($image->is_primary)
                        <span class="absolute top-1 left-1 bg-emerald-500 text-white text-xs px-2 py-0.5 rounded-full">Primary</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 mt-2">Hover over an image to set as primary or delete. Deleted images will be removed when you save.</p>
            </div>
            @endif

            <!-- Hidden inputs for delete and primary image -->
            <div id="delete-images-container"></div>
            <input type="hidden" name="primary_image" id="primary-image-input" value="">

            <!-- Upload New Images -->
            <div>
                <label class="text-sm font-medium text-slate-700">Upload More Images</label>
                <p class="text-xs text-slate-500 mt-1 mb-2">Add more images to the product gallery</p>
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
                <!-- New Image Preview -->
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
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200 text-sm font-medium">
                Update Product
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
        const mrpInput = document.getElementById('mrp-input');
        const discountInput = document.getElementById('discount-input');
        const displayPriceInput = document.getElementById('display-price');
        const profitDisplay = document.getElementById('profit-display');
        const purchasePriceDisplay = document.getElementById('purchase-price-display');

        const purchasePrice = parseFloat(purchasePriceDisplay.value.replace(/,/g, '')) || 0;

        function calculatePrices() {
            const mrp = parseFloat(mrpInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const displayPrice = mrp - (mrp * discount / 100);
            const profit = displayPrice - purchasePrice;

            displayPriceInput.value = displayPrice.toFixed(2);
            profitDisplay.value = profit.toFixed(2);
        }

        mrpInput.addEventListener('input', calculatePrices);
        discountInput.addEventListener('input', calculatePrices);

        // Multiple Image Upload Preview
        const imagesInput = document.getElementById('images-input');
        const previewContainer = document.getElementById('image-preview-container');
        const dropZone = document.getElementById('image-drop-zone');

        if (imagesInput) {
            imagesInput.addEventListener('change', function() {
                previewImages(this.files);
            });
        }

        // Drag and drop
        if (dropZone) {
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
        }

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
                        <div class="aspect-square rounded-xl overflow-hidden border-2 border-blue-300 shadow-sm">
                            <img src="${e.target.result}" class="w-full h-full object-cover" />
                        </div>
                        <span class="absolute top-1 left-1 bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full">New</span>
                        <p class="text-xs text-slate-500 mt-1 truncate">${file.name}</p>
                    `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    });

    // Mark image for deletion
    function markForDeletion(imageId) {
        const container = document.getElementById('delete-images-container');
        const existingInput = container.querySelector(`input[value="${imageId}"]`);
        
        if (!existingInput) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_images[]';
            input.value = imageId;
            container.appendChild(input);
        }

        // Visual feedback - fade out the image
        const imageDiv = document.querySelector(`[data-image-id="${imageId}"]`);
        if (imageDiv) {
            imageDiv.classList.add('opacity-30');
            imageDiv.querySelector('.bg-red-500')?.classList.add('hidden');
            
            // Add undo button
            const undoBtn = document.createElement('button');
            undoBtn.type = 'button';
            undoBtn.className = 'absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 px-3 py-1 bg-slate-700 text-white text-xs rounded-lg hover:bg-slate-800 transition';
            undoBtn.textContent = 'Undo';
            undoBtn.onclick = function() {
                undoDelete(imageId);
            };
            imageDiv.appendChild(undoBtn);
        }
    }

    // Undo deletion
    function undoDelete(imageId) {
        const container = document.getElementById('delete-images-container');
        const input = container.querySelector(`input[value="${imageId}"]`);
        if (input) {
            input.remove();
        }

        const imageDiv = document.querySelector(`[data-image-id="${imageId}"]`);
        if (imageDiv) {
            imageDiv.classList.remove('opacity-30');
            imageDiv.querySelector('.bg-red-500')?.classList.remove('hidden');
            
            // Remove undo button
            const undoBtn = imageDiv.querySelector('button');
            if (undoBtn) undoBtn.remove();
        }
    }

    // Set primary image
    function setPrimaryImage(imageId) {
        document.getElementById('primary-image-input').value = imageId;

        // Update visual indicators
        document.querySelectorAll('#existing-images > div').forEach(div => {
            const imgContainer = div.querySelector('.aspect-square');
            const primaryBadge = div.querySelector('.bg-emerald-500.text-white.text-xs');
            const setPrimaryBtn = div.querySelector('[onclick^="setPrimaryImage"]');

            if (parseInt(div.dataset.imageId) === imageId) {
                imgContainer.classList.remove('border-slate-200');
                imgContainer.classList.add('border-emerald-500');
                if (!primaryBadge) {
                    const badge = document.createElement('span');
                    badge.className = 'absolute top-1 left-1 bg-emerald-500 text-white text-xs px-2 py-0.5 rounded-full';
                    badge.textContent = 'Primary';
                    div.appendChild(badge);
                }
                if (setPrimaryBtn) setPrimaryBtn.classList.add('hidden');
            } else {
                imgContainer.classList.remove('border-emerald-500');
                imgContainer.classList.add('border-slate-200');
                if (primaryBadge && !primaryBadge.classList.contains('bg-blue-500')) {
                    primaryBadge.remove();
                }
                if (setPrimaryBtn) setPrimaryBtn.classList.remove('hidden');
            }
        });
    }
</script>
@endpush
@endsection
