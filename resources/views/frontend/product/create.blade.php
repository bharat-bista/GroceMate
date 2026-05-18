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
                    <label class="text-sm font-medium text-slate-700">Business Account</label>
                    <select name="business_id" id="business-filter"
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
                        <option value="">All Businesses</option>
                        @foreach($businesses as $business)
                            <option value="{{ $business->id }}" @selected((string) $selectedBusinessId === (string) $business->id)>{{ $business->business_name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Choose a business to load its available products.</p>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Select Product Batch *</label>
                    <div class="relative">
                        <input type="text" id="batch-search-input"
                               autocomplete="off" placeholder="Type product name to search batches…"
                               class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
                        <div id="batch-dropdown"
                             class="hidden absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-lg mt-1 max-h-60 overflow-y-auto">
                        </div>
                    </div>
                    <input type="hidden" name="product_id" id="product-id-input">
                    <input type="hidden" id="batch-id-input">
                    <div id="selected-batch-info"
                         class="hidden mt-2 px-3 py-2 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-700">
                        <span id="batch-info-text"></span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Type at least 2 characters. Each result is a separate inventory batch. Products already in ecommerce are excluded.</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Category</label>
                    <input type="text" id="selected-category" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600"
                           value="" placeholder="Auto-filled from selected product" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Company</label>
                    <input type="text" id="selected-company" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600"
                           value="" placeholder="Auto-filled from selected product" />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">SKU (optional)</label>
                    <input name="sku" value="{{ old('sku') }}"
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
                          value="0" />
                </div>

                <div>
                      <label class="text-sm font-medium text-slate-700">Sale Price *</label>
                    <input name="mrp" type="number" step="1" data-money inputmode="numeric" max="9999999" value="{{ old('mrp', 0) }}" required
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
                    <label class="text-sm font-medium text-slate-700">Ecommerce Stock *</label>
                    <input name="ecommerce_stock" id="ecommerce-stock-input" type="number" step="0.001" min="0" value="{{ old('ecommerce_stock', 0) }}"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400"
                           placeholder="0.000" />
                    <p id="ecommerce-stock-error" class="hidden text-xs text-red-600 mt-1 font-medium"></p>
                    <p class="text-xs text-slate-500 mt-1">Reserve how much inventory stock should be available for ecommerce.</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Inventory Stock</label>
                    <input type="text" id="inventory-stock-display" readonly
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-500"
                           value="0.000" />
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
                <textarea name="description" id="description-input" class="hidden">{{ old('description') }}</textarea>
                <div id="description-editor"
                     class="mt-1 rounded-xl border border-slate-300 bg-white text-sm"></div>
            </div>
        </div>

        <!-- Thumbnail Images -->
        <div class="border-b border-slate-200 pb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Thumbnail Images</h3>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-700">Upload Thumbnails</label>
                <div class="mt-1 border-2 border-slate-300 border-dashed rounded-xl hover:border-slate-400 transition-all duration-200">
                    <div id="thumbnail-drop-zone" class="flex justify-center px-6 pt-5 pb-6">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-slate-600">
                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>Upload files</span>
                                    <input type="file" id="thumbnails-input" name="thumbnails[]" accept="image/jpeg,image/png,image/jpg" multiple class="sr-only" />
                                </label>
                                <p class="pl-1">or drag and drop multiple images</p>
                            </div>
                            <p class="text-xs text-slate-500">PNG, JPG up to 2MB each | First image becomes the main thumbnail</p>
                        </div>
                    </div>
                    <div id="thumbnail-preview-grid" class="hidden px-4 pb-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3"></div>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<style>
    #description-editor .ql-editor { min-height: 220px; }
    .batch-result-item:last-child { border-bottom: none; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const businessFilter         = document.getElementById('business-filter');
    const batchSearchInput       = document.getElementById('batch-search-input');
    const batchDropdown          = document.getElementById('batch-dropdown');
    const productIdInput         = document.getElementById('product-id-input');
    const batchIdInput           = document.getElementById('batch-id-input');
    const selectedBatchInfo      = document.getElementById('selected-batch-info');
    const batchInfoText          = document.getElementById('batch-info-text');
    const selectedCategoryInput  = document.getElementById('selected-category');
    const selectedCompanyInput   = document.getElementById('selected-company');
    const mrpInput               = document.getElementById('mrp-input');
    const discountInput          = document.getElementById('discount-input');
    const displayPriceInput      = document.getElementById('display-price');
    const profitDisplay          = document.getElementById('profit-display');
    const purchasePriceDisplay   = document.getElementById('purchase-price-display');
    const inventoryStockDisplay  = document.getElementById('inventory-stock-display');
    const stockLeftDisplay       = document.getElementById('stock-left-display');
    const ecommerceStockInput    = document.getElementById('ecommerce-stock-input');
    const stockErrorEl           = document.getElementById('ecommerce-stock-error');
    const thumbnailsInput        = document.getElementById('thumbnails-input');
    const thumbnailPreviewGrid   = document.getElementById('thumbnail-preview-grid');
    const descriptionInput       = document.getElementById('description-input');
    const form                   = document.querySelector('form');

    let purchasePrice  = 0;
    let inventoryStock = 0;
    let debounceTimer  = null;

    // ── Price / stock calculations ──────────────────────────────────────────
    function calculatePrices() {
        const mrp          = parseFloat(mrpInput.value) || 0;
        const discount     = parseFloat(discountInput.value) || 0;
        const displayPrice = mrp - (mrp * discount / 100);
        const profit       = displayPrice - purchasePrice;
        const ecomStock    = parseFloat(ecommerceStockInput?.value) || 0;

        displayPriceInput.value = displayPrice.toFixed(2);
        profitDisplay.value     = profit.toFixed(2);
        if (inventoryStockDisplay) inventoryStockDisplay.value = inventoryStock.toFixed(3);
        if (stockLeftDisplay)     stockLeftDisplay.value = Math.max(inventoryStock - ecomStock, 0).toFixed(3);
    }

    // ── Ecommerce stock real-time cap ───────────────────────────────────────
    function validateEcommerceStock() {
        if (!ecommerceStockInput || inventoryStock === 0) return true;
        const entered = parseFloat(ecommerceStockInput.value) || 0;
        if (entered > inventoryStock) {
            ecommerceStockInput.classList.add('border-red-400', 'focus:border-red-400', 'focus:ring-red-100');
            ecommerceStockInput.classList.remove('border-slate-300', 'focus:border-blue-500', 'focus:ring-blue-500');
            stockErrorEl.textContent = `Cannot exceed batch qty (${inventoryStock.toFixed(3)})`;
            stockErrorEl.classList.remove('hidden');
            return false;
        }
        ecommerceStockInput.classList.remove('border-red-400', 'focus:border-red-400', 'focus:ring-red-100');
        ecommerceStockInput.classList.add('border-slate-300', 'focus:border-blue-500', 'focus:ring-blue-500');
        stockErrorEl.classList.add('hidden');
        return true;
    }

    // ── Batch search ────────────────────────────────────────────────────────
    function fetchBatches(q) {
        const biz = encodeURIComponent(businessFilter.value);
        fetch(`/pos/batches/search?q=${encodeURIComponent(q)}&business_id=${biz}&exclude_ecommerce=1`)
            .then(r => r.json())
            .then(renderDropdown)
            .catch(() => {
                batchDropdown.innerHTML = '<div class="px-3 py-2 text-sm text-slate-500">Search failed.</div>';
                batchDropdown.classList.remove('hidden');
            });
    }

    function renderDropdown(batches) {
        if (!batches.length) {
            batchDropdown.innerHTML = '<div class="px-3 py-2 text-sm text-slate-500">No batches found.</div>';
        } else {
            batchDropdown.innerHTML = batches.map(b => {
                const expiry = b.expiry_date ? ` · Exp: ${b.expiry_date}` : '';
                const safe   = JSON.stringify(b).replace(/'/g, '&#39;');
                return `<div class="batch-result-item px-3 py-2.5 hover:bg-blue-50 cursor-pointer border-b border-slate-100"
                             data-batch='${safe}'>
                    <div class="font-medium text-sm text-slate-800">${b.product_name}</div>
                    <div class="text-xs text-slate-500">${b.batch_no} · Qty: ${parseFloat(b.qty_remaining).toFixed(3)}${expiry} · Cost: Rs ${b.unit_cost}</div>
                </div>`;
            }).join('');
        }
        batchDropdown.classList.remove('hidden');
        batchDropdown.querySelectorAll('.batch-result-item').forEach(el => {
            el.addEventListener('click', function () {
                selectBatch(JSON.parse(this.dataset.batch));
            });
        });
    }

    function selectBatch(b) {
        productIdInput.value   = b.product_id;
        batchIdInput.value     = b.batch_id;
        batchSearchInput.value = b.product_name;
        batchDropdown.classList.add('hidden');

        const expiry = b.expiry_date ? ` · Expiry: ${b.expiry_date}` : '';
        batchInfoText.textContent = `Batch: ${b.batch_no}${expiry} · Available: ${parseFloat(b.qty_remaining).toFixed(3)}`;
        selectedBatchInfo.classList.remove('hidden');

        selectedCategoryInput.value = b.category || 'N/A';
        selectedCompanyInput.value  = b.brand    || 'N/A';

        purchasePrice  = parseFloat(b.unit_cost)     || 0;
        inventoryStock = parseFloat(b.qty_remaining) || 0;
        purchasePriceDisplay.value = Math.round(purchasePrice);

        if (ecommerceStockInput) ecommerceStockInput.max = inventoryStock;
        validateEcommerceStock();
        calculatePrices();
    }

    batchSearchInput.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(debounceTimer);
        if (q.length < 2) { batchDropdown.classList.add('hidden'); return; }
        debounceTimer = setTimeout(() => fetchBatches(q), 250);
    });

    batchSearchInput.addEventListener('focus', function () {
        if (this.value.trim().length >= 2) fetchBatches(this.value.trim());
    });

    document.addEventListener('click', function (e) {
        if (!batchSearchInput.contains(e.target) && !batchDropdown.contains(e.target)) {
            batchDropdown.classList.add('hidden');
        }
    });

    // ── Pricing / stock inputs ──────────────────────────────────────────────
    mrpInput.addEventListener('input', calculatePrices);
    discountInput.addEventListener('input', calculatePrices);
    if (ecommerceStockInput) {
        ecommerceStockInput.addEventListener('input', function () {
            validateEcommerceStock();
            calculatePrices();
        });
    }

    // ── Thumbnail preview with per-image remove ─────────────────────────────
    let selectedFiles = [];

    function syncFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        thumbnailsInput.files = dt.files;
    }

    function renderPreviews() {
        thumbnailPreviewGrid.innerHTML = '';
        if (!selectedFiles.length) {
            thumbnailPreviewGrid.classList.add('hidden');
            return;
        }
        thumbnailPreviewGrid.classList.remove('hidden');
        selectedFiles.forEach(function (file, idx) {
            const reader = new FileReader();
            reader.onload = function (e) {
                // No overflow-hidden on wrap — that clips the remove button at the corners.
                const wrap = document.createElement('div');
                wrap.className = 'relative border border-slate-200 bg-slate-100 rounded-xl';
                wrap.style.aspectRatio = '1';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = file.name;
                img.className = 'absolute inset-0 w-full h-full object-cover rounded-xl';
                wrap.appendChild(img);

                if (idx === 0) {
                    const badge = document.createElement('div');
                    badge.className = 'absolute bottom-0 inset-x-0 bg-blue-600 bg-opacity-90 text-white text-center text-xs py-0.5 rounded-b-xl z-10';
                    badge.textContent = 'Main';
                    wrap.appendChild(badge);
                }

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.title = 'Remove';
                btn.className = 'absolute top-1 right-1 z-10 w-5 h-5 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center shadow hover:bg-red-700 focus:outline-none';
                btn.textContent = '×';
                btn.addEventListener('click', function () {
                    selectedFiles.splice(idx, 1);
                    syncFileInput();
                    renderPreviews();
                });
                wrap.appendChild(btn);

                thumbnailPreviewGrid.appendChild(wrap);
            };
            reader.readAsDataURL(file);
        });
    }

    thumbnailsInput.addEventListener('change', function () {
        selectedFiles = Array.from(this.files);
        renderPreviews();
    });

    // ── Form submit guard ───────────────────────────────────────────────────
    form.addEventListener('submit', function (e) {
        if (!productIdInput.value) {
            e.preventDefault();
            GroceMate.notify.error('Please select a product batch before saving.');
            return;
        }
        if (!validateEcommerceStock()) {
            e.preventDefault();
            GroceMate.notify.error('Ecommerce stock cannot exceed the batch quantity.');
        }
    }, true);

    // ── Quill description editor ────────────────────────────────────────────
    let descEditor = null;
    if (window.Quill && descriptionInput) {
        descEditor = new Quill('#description-editor', {
            theme: 'snow',
            modules: { toolbar: [[{ header: [1, 2, 3, false] }], ['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['link'], ['clean']] }
        });
        if (descriptionInput.value) descEditor.root.innerHTML = descriptionInput.value;
        form.addEventListener('submit', function () { descriptionInput.value = descEditor.root.innerHTML; });
    }

    calculatePrices();
});
</script>
@endpush
@endsection
