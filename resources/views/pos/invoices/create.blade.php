@extends('inventory.layouts.inventory')

@section('title', 'New Invoice')
@section('heading', 'New Invoice (POS Sale)')
@section('subtitle', 'Add customer, date and multiple products with taxes')

@section('content')
<form method="POST" action="{{ route('pos.invoices.store') }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6" id="purchaseForm">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="text-sm text-slate-600">Business *</label>
            <select name="business_id" required 
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
                <option value="">Select Business</option>
                @foreach($businesses as $b)
                    <option value="{{ $b->id }}" @selected(old('business_id')==$b->id)>{{ $b->business_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm text-slate-600">Customer *</label>
            <select name="customer_id" required 
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
                <option value="">Select Customer</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" @selected(old('customer_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm text-slate-600">Invoice Date *</label>
            <input type="date" name="invoice_date" value="{{ old('invoice_date', now()->toDateString()) }}" required
                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
        </div>

        <div>
            <label class="text-sm text-slate-600">Invoice No *</label>
            <input name="invoice_no" value="{{ old('invoice_no', $nextInvoiceNumber) }}" readonly
                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
            
        </div>
    </div>

    <!-- Payment Method Section -->
    <div class="border-t border-slate-200 pt-5">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Payment Method *</h3>
            <p class="text-sm text-slate-600">Select how the customer will pay for this invoice</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <input type="radio" name="payment_method" value="cash" id="payment_cash" 
                       @checked(old('payment_method', 'cash') == 'cash') required
                       class="peer sr-only">
                <label for="payment_cash" 
                       class="flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 hover:border-slate-300 border-slate-200">
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <div class="font-medium">Cash</div>
                        <div class="text-xs opacity-75">Paid in cash</div>
                    </div>
                </label>
            </div>

            <div class="relative">
                <input type="radio" name="payment_method" value="credit" id="payment_credit" 
                       @checked(old('payment_method') == 'credit') required
                       class="peer sr-only">
                <label for="payment_credit" 
                       class="flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 peer-checked:text-yellow-700 hover:border-slate-300 border-slate-200">
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <div class="font-medium">Credit</div>
                        <div class="text-xs opacity-75">Pay later</div>
                    </div>
                </label>
            </div>

            <div class="relative">
                <input type="radio" name="payment_method" value="bank" id="payment_bank" 
                       @checked(old('payment_method') == 'bank') required
                       class="peer sr-only">
                <label for="payment_bank" 
                       class="flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700 hover:border-slate-300 border-slate-200">
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <div class="font-medium">Bank</div>
                        <div class="text-xs opacity-75">Bank transfer</div>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-200 pt-5">
        <div class="flex items-center justify-between mb-4">
            <div class="font-semibold text-lg">Invoice Items</div>
            <button type="button" id="addRow"
                    class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Row
            </button>
        </div>

        <div class="mt-3 overflow-x-auto border border-slate-200 rounded-lg relative">
            <table class="w-full text-sm" id="itemsTable">
                <thead class="text-slate-700 bg-slate-100">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium">Product</th>
                        <th class="text-left px-4 py-3 font-medium">Unit</th>
                        <th class="text-left px-4 py-3 font-medium">Qty</th>
                        <th class="text-left px-4 py-3 font-medium">Unit Price</th>
                        <th class="text-left px-4 py-3 font-medium">Base Cost</th>
                        <th class="text-left px-4 py-3 font-medium">Subtotal</th>
                        <th class="text-center px-4 py-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200" id="itemsBody">
                    <!-- Rows will be added here by JavaScript -->
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-300">
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-right font-semibold text-slate-700">Total Base Cost:</td>
                        <td class="px-4 py-4 font-semibold text-slate-900 text-right" id="totalBaseCost">0.00</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right font-semibold text-slate-700">
                            Total Tax:
                        </td>

                        <!-- Tax selector -->
                        <td class="px-4 py-3">
                            <select name="final_tax_id" id="finalTaxSelect"
                                    class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm">
                                <option value="">No Tax</option>
                                @foreach($taxes as $tax)
                                    <option value="{{ $tax->id }}"
                                            data-rate="{{ $tax->rate }}"
                                            data-type="{{ $tax->type }}"
                                            @selected(old('final_tax_id')==$tax->id)>
                                        {{ $tax->name }}
                                        ({{ $tax->type === 'percentage' ? $tax->rate.'%' : $tax->rate.' fixed' }})
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <!-- Calculated tax amount -->
                        <td class="px-4 py-3 font-semibold text-red-600 text-right" id="totalTax">
                            0.00
                        </td>

                        <td></td>
                    </tr>
                    <tr class="bg-slate-100">
                        <td colspan="5" class="px-4 py-4 text-right font-bold text-lg text-slate-900">GRAND TOTAL:</td>
                        <td class="px-4 py-4 font-bold text-lg text-green-700 text-right" id="grandTotal">0.00</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <p class="text-xs text-slate-500 mt-2">
            <strong>Tip:</strong> Start typing product name to see suggestions from inventory.
            New products will be created automatically.
        </p>
    </div>

    <!-- Save Invoice Button Section -->
    <div class="flex gap-3 pt-4 border-t border-slate-200 mt-6">
        <button type="button" id="saveInvoiceBtn" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
            Save Invoice
        </button>
        <a href="{{ route('pos.invoices.index') }}" data-back-button
           class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
            Cancel
        </a>
    </div>
</form>

<!-- Email Confirmation Modal -->
<div id="emailModal" 
     class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
     style="display: none; align-items: center; justify-content: center; padding: 1rem;">

    <!-- Modal Box -->
    <div class="bg-white rounded-2xl shadow-2xl p-6 animate-scaleIn"
         style="width: 100%; max-width: 600px; margin: 0 auto; border: 1px solid #666768ff;">

        <h3 class="text-lg font-semibold text-gray-800 mb-3">
            Send Invoice to Customer?
        </h3>

        <p class="text-gray-600 text-sm mb-6">
            Would you like to send this invoice to the customer's email address?
        </p>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Customer Email
            </label>
            <input type="email"
                   id="customerEmailInput"
                   class="w-full rounded-xl border border-gray-300 px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                   readonly>
        </div>

        <div class="flex gap-2" style="flex-wrap: wrap;">
            <button type="button"
                    id="sendEmailYes"
                    class="flex-1 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700 transition"
                    style="min-width: 150px;">
                Yes, Send Email
            </button>

            <button type="button"
                    id="sendEmailNo"
                    class="flex-1 py-2 rounded-xl bg-gray-600 text-white hover:bg-gray-700 transition"
                    style="min-width: 150px;">
                No, Just Save
            </button>
            
            <button type="button"
                    id="sendEmailCancel"
                    class="flex-1 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 transition"
                    style="min-width: 150px;">
                Cancel
            </button>
        </div>

    </div>
</div>


<script>
// ---------- Helpers ----------
function formatCurrency(amount) {
    const n = Number(amount);
    return (isNaN(n) ? 0 : n).toFixed(2);
}

// Available units
const units = ['kg', 'liter', 'pcs', 'cartoon', 'peti', 'bori', 'box', 'bottle', 'pack', 'set'];

// products injected from backend (optional fallback)
const products = [
@foreach($products as $product)
{
    id: {{ $product['id'] }},
    name: "{{ addslashes($product['name']) }}",
    unit: "{{ $product['unit'] }}",
    sku: "{{ $product['sku'] ?? '' }}",
    selling_price: {{ $product['selling_price'] ?? 0 }},
    pos_available: {{ $product['pos_available_stock'] ?? 0 }},
},
@endforeach
];

// taxes injected from backend
const taxes = [
@foreach($taxes as $tax)
{
    id: {{ $tax->id }},
    name: "{{ addslashes($tax->name) }}",
    rate: {{ $tax->rate }},
    type: "{{ $tax->type }}",
    formatted: "{{ $tax->type === 'percentage' ? $tax->rate . '%' : $tax->rate . ' fixed' }}",
},
@endforeach
];

let rowCounter = 0;
let activeAutocomplete = null;
let searchTimeout = null;

async function searchProductsApi(query) {
    try {
        console.log('🔍 Searching:', query);
        
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const response = await fetch(`/pos/products/search?q=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: { 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            credentials: 'same-origin'
        });
        
        console.log('📡 Status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ API Error:', response.status, errorText);
            throw new Error('API Error');
        }
        
        const data = await response.json();
        console.log('✅ Results:', data);
        
        // Ensure data is an array
        if (!Array.isArray(data)) {
            console.error('❌ Invalid response format:', data);
            throw new Error('Invalid response');
        }
        
        return data;
    } catch (e) {
        console.error('🚨 API Failed, using fallback search:', e);
        
        // Fallback: search in the injected products array
        const qLower = query.toLowerCase();
        const fallbackResults = products.filter(p => 
            p.name.toLowerCase().includes(qLower)
        ).slice(0, 10);
        
        console.log('🔄 Fallback results:', fallbackResults);
        return fallbackResults;
    }
}

// ---------- Totals ----------
function calculateRowTotal(rowId) {
    const row = document.getElementById(`row-${rowId}`);
    if (!row) return null;

    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const unitCost = parseFloat(row.querySelector('.cost-input').value) || 0;
    const baseCost = qty * unitCost;
    const subtotal = baseCost;

    // Update display
    row.querySelector('.base-cost').textContent = formatCurrency(baseCost);
    row.querySelector('.subtotal').textContent = formatCurrency(subtotal);

    // Hidden inputs
    row.querySelector('.base-cost-input').value = baseCost.toFixed(2);
    row.querySelector('.subtotal-input').value = subtotal.toFixed(2);

    return { baseCost, subtotal };
}

function updateAllTotals() {
    let totalBase = 0;

    document.querySelectorAll('.purchase-row').forEach(row => {
        const rowId = row.id.replace('row-', '');
        const totals = calculateRowTotal(rowId);
        if (!totals) return;

        totalBase += totals.baseCost;
    });

    document.getElementById('totalBaseCost').textContent = formatCurrency(totalBase);

    // apply FINAL tax here
    applyFinalTax(totalBase);
}

// Final tax calculation function
function applyFinalTax(totalBase) {
    const taxSelect = document.getElementById('finalTaxSelect');
    const totalTaxElement = document.getElementById('totalTax');
    const grandTotalElement = document.getElementById('grandTotal');
    
    if (!taxSelect || !totalTaxElement || !grandTotalElement) return;
    
    const selectedOption = taxSelect.options[taxSelect.selectedIndex];
    let taxAmount = 0;
    
    if (selectedOption && selectedOption.value) {
        const rate = parseFloat(selectedOption.dataset.rate);
        const type = selectedOption.dataset.type;
        
        if (type === 'fixed') {
            taxAmount = rate;
        } else {
            taxAmount = (totalBase * rate) / 100;
        }
    }
    
    const grandTotal = totalBase + taxAmount;
    
    totalTaxElement.textContent = formatCurrency(taxAmount);
    grandTotalElement.textContent = formatCurrency(grandTotal);
}

// ---------- Product selection ----------
function updateProductFromInput(rowId, payload) {
    const row = document.getElementById(`row-${rowId}`);
    if (!row) return;

    const productInput = row.querySelector('.product-name-input');
    const productIdInput = row.querySelector('.product-id-input');
    const unitSelect = row.querySelector('.unit-select');
    const unitDisplay = row.querySelector('.unit-display');
    const costInput = row.querySelector('.cost-input');

    const name = payload.name || '';
    const id = payload.id ? Number(payload.id) : null;
    const unit = payload.unit || 'pcs';
    // Use selling price as the default unit price for POS.
    const sellingPrice = payload.selling_price || 0;

    productInput.value = name;
    productIdInput.value = id || '';
    unitSelect.value = unit;
    unitDisplay.textContent = unit;

    // Show/hide select for new/existing
    if (id) {
        unitSelect.classList.add('hidden');
        unitDisplay.classList.remove('hidden');
    } else {
        unitSelect.classList.remove('hidden');
        unitDisplay.classList.add('hidden');
    }

    costInput.value = sellingPrice;

    updateAllTotals();
}

// ---------- Autocomplete UI ----------
function removeAutocomplete() {
    if (activeAutocomplete) {
        activeAutocomplete.remove();
        activeAutocomplete = null;
    }
}

function createAutocompleteDropdown(rowId, inputElement, results) {
    // remove old dropdown
    if (activeAutocomplete) {
        activeAutocomplete.remove();
        activeAutocomplete = null;
    }

    const query = inputElement.value.trim();
    if (!query) return;

    // Build dropdown INSIDE the same cell (better UI)
    const wrapper = inputElement.closest('.relative');
    if (!wrapper) return;

    // remove previous dropdown in wrapper if exists
    const old = wrapper.querySelector('.autocomplete-dropdown');
    if (old) old.remove();

    const dropdown = document.createElement('div');
    dropdown.className = 'autocomplete-dropdown absolute left-0 right-0 mt-1 bg-white border border-slate-300 rounded-xl shadow-sm z-50 max-h-60 overflow-y-auto text-sm';

    // Better UI container
    const list = document.createElement('div');
    list.className = 'max-h-80 overflow-y-auto';

    // Check exact match (ignore case)
    const qLower = query.toLowerCase();
    const hasExact = results.some(p => (p.name || '').toLowerCase() === qLower);

    // --- Existing products first ---
    if (results.length > 0) {
        results.slice(0, 10).forEach((p) => {
            // Prevent selection of items that have no POS-available stock.
            const posAvailable = Number(p.pos_available ?? 0);
            const inStock = posAvailable > 0;
            const item = document.createElement('button');
            item.type = 'button';
            item.disabled = !inStock;
            item.className = inStock
                ? 'block w-full px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-xl cursor-pointer truncate'
                : 'block w-full px-3 py-2 text-sm text-slate-400 rounded-xl cursor-not-allowed opacity-60 truncate';
            
            // highlight matching text
            const name = p.name || '';
            const idx = name.toLowerCase().indexOf(qLower);
            const highlighted =
                idx >= 0
                    ? name.substring(0, idx) +
                      `<span class="text-blue-700 font-semibold">${name.substring(idx, idx + query.length)}</span>` +
                      name.substring(idx + query.length)
                    : name;

            item.innerHTML = `
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-semibold text-slate-900 truncate">${highlighted}</div>
                        <div class="text-[11px] text-slate-500 leading-tight">
                            ${p.sku ? `SKU: ${p.sku} • ` : ''}Unit: ${p.unit ?? '-'} •
                            ${inStock ? `In stock: ${posAvailable} units` : '<span class="text-red-600">Out of stock</span>'}
                        </div>
                    </div>
                    <div class="text-[11px] font-bold text-slate-600 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100 shrink-0">
                        Price: Rs ${formatCurrency(p.selling_price ?? 0)}
                    </div>
                </div>
            `;

            item.addEventListener('click', () => {
                if (!inStock) {
                    return;
                }

                updateProductFromInput(rowId, {
                    id: p.id,
                    name: p.name,
                    unit: p.unit,
                    selling_price: p.selling_price,
                    pos_available: p.pos_available
                });

                dropdown.remove();
                activeAutocomplete = null;

                const row = document.getElementById(`row-${rowId}`);
                row.querySelector('.qty-input')?.focus();
            });

            list.appendChild(item);
        });
    }

    // --- Create new: only if no exact match ---
    if (!hasExact) {
        const createBtn = document.createElement('button');
        createBtn.type = 'button';
        createBtn.className = 'w-full text-left px-3 py-2 bg-green-50 hover:bg-green-100 outline-none';

        createBtn.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-green-600 text-white text-sm">+</span>
                <div class="text-sm font-medium text-green-800">
                    Create new product: <span class="font-semibold">"${query}"</span>
                </div>
            </div>
        `;

        createBtn.addEventListener('click', () => {
            updateProductFromInput(rowId, { name: query });

            dropdown.remove();
            activeAutocomplete = null;

            const row = document.getElementById(`row-${rowId}`);
            row.querySelector('.unit-select')?.focus();
        });

        // add separator if there are results
        if (results.length > 0) {
            const sep = document.createElement('div');
            sep.className = 'h-px bg-slate-200';
            list.appendChild(sep);
        }
        list.appendChild(createBtn);
    }

    dropdown.appendChild(list);
    wrapper.appendChild(dropdown);
    activeAutocomplete = dropdown;

    // close when click outside
    setTimeout(() => {
        const close = (e) => {
            if (activeAutocomplete && !wrapper.contains(e.target)) {
                activeAutocomplete.remove();
                activeAutocomplete = null;
                document.removeEventListener('click', close);
            }
        };
        document.addEventListener('click', close);
    }, 50);
}

async function handleProductSearch(rowId, inputElement) {
    clearTimeout(searchTimeout);

    const query = inputElement.value.trim();
    console.log('🎯 Search triggered for:', query, 'in row:', rowId);

    // always update hidden product_name (because server needs it)
    const row = document.getElementById(`row-${rowId}`);
    if (row) {
        row.querySelector('.product-name-hidden').value = query;
    }

    if (query.length < 2) {
        console.log('⏸️ Query too short, removing autocomplete');
        removeAutocomplete();
        return;
    }

    searchTimeout = setTimeout(async () => {
        console.log('⏰ Searching after delay for:', query);
        const results = await searchProductsApi(query);
        console.log('📦 Got results:', results.length, 'items');
        // show dropdown ALWAYS (includes Create New + results)
        createAutocompleteDropdown(rowId, inputElement, results);
    }, 300);
}

// ---------- Row creation ----------
function createRow() {
    const rowId = rowCounter++;
    const row = document.createElement('tr');
    row.id = `row-${rowId}`;
    row.className = 'purchase-row hover:bg-slate-50';

    const unitOptions = units.map(u => `<option value="${u}">${u}</option>`).join('');

    row.innerHTML = `
        <td class="px-4 py-3">
            <div class="relative">
                <input type="text"
                       class="product-name-input w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm px-2 py-1.5"
                       placeholder="Type product name..."
                       autocomplete="off" />
                <input type="hidden" name="items[${rowId}][product_id]" class="product-id-input" />
                <input type="hidden" name="items[${rowId}][product_name]" class="product-name-hidden" />
            </div>
        </td>
        <td class="px-4 py-3">
            <select name="items[${rowId}][product_unit]"
                    class="unit-select w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm px-2 py-1.5">
                ${unitOptions}
            </select>
            <div class="unit-display text-slate-700 font-medium text-sm px-2 py-1.5 hidden"></div>
        </td>
        <td class="px-4 py-3">
            <input name="items[${rowId}][qty]"
                   type="number"
                   step="0.001"
                   min="0.001"
                   value="1"
                   required
                   class="qty-input w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm px-2 py-1.5">
        </td>
        <td class="px-4 py-3 min-w-[100px]">
            <input name="items[${rowId}][unit_cost]"
                   type="number"
                   step="0.01"
                   min="0"
                   value="0"
                   required
                   class="cost-input w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-1.5 text-right">
        </td>
        <td class="px-4 py-3">
            <div class="text-slate-900 font-medium text-sm">
                <span class="base-cost">0.00</span>
                <input type="hidden" name="items[${rowId}][base_cost]" class="base-cost-input" value="0">
            </div>
        </td>
        <td class="px-4 py-3">
            <div class="text-green-700 font-bold text-sm">
                <span class="subtotal">0.00</span>
                <input type="hidden" name="items[${rowId}][line_total]" class="subtotal-input" value="0">
            </div>
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button"
                    class="remove-btn px-2 py-1 rounded bg-red-50 text-red-700 hover:bg-red-100 text-xs font-medium border border-red-200">
                ✕
            </button>
        </td>
    `;

    // bind events (better than inline HTML)
    const productInput = row.querySelector('.product-name-input');

    // AUTO CAPITALIZE INPUT and trigger search
    productInput.addEventListener('input', () => {
        // auto capitalize each word
        productInput.value = productInput.value.replace(/\b\w/g, l => l.toUpperCase());
        handleProductSearch(rowId, productInput); // call your autocomplete search
    });

    // also trigger search on focus
    productInput.addEventListener('focus', () => handleProductSearch(rowId, productInput));

    // update totals when qty or cost changes
    row.querySelector('.qty-input').addEventListener('input', () => {
        // Clear any previous stock errors when quantities change.
        clearStockErrors();
        updateAllTotals();
    });
    row.querySelector('.cost-input').addEventListener('input', updateAllTotals);

    // remove row
    row.querySelector('.remove-btn').addEventListener('click', () => removeRow(rowId));

    return row;
}

function removeRow(rowId) {
    const rows = document.querySelectorAll('.purchase-row');
    if (rows.length <= 1) {
        alert('At least one item is required.');
        return;
    }
    const row = document.getElementById(`row-${rowId}`);
    if (row) row.remove();
    removeAutocomplete();
    updateAllTotals();
}

// ---------- Stock check ----------
function clearStockErrors() {
    document.querySelectorAll('.qty-input').forEach(input => {
        input.classList.remove('border-red-500');
    });
    document.querySelectorAll('.stock-error').forEach(el => el.remove());
}

function showStockError(row, available) {
    const qtyInput = row.querySelector('.qty-input');
    if (!qtyInput) return;

    qtyInput.classList.add('border-red-500');

    let errorEl = row.querySelector('.stock-error');
    if (!errorEl) {
        errorEl = document.createElement('div');
        errorEl.className = 'stock-error text-red-600 text-xs mt-1';
        qtyInput.parentElement.appendChild(errorEl);
    }

    errorEl.textContent = `Only ${available} available`;
}

async function checkStock() {
    // Build a minimal payload with product_id + qty (skip rows with no product).
    const rows = document.querySelectorAll('.purchase-row');
    const items = [];

    rows.forEach(row => {
        const productId = row.querySelector('.product-id-input')?.value;
        const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;

        if (productId) {
            items.push({ product_id: Number(productId), qty });
        }
    });

    clearStockErrors();

    if (items.length === 0) {
        return true;
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        const response = await fetch('/pos/stock-check', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            },
            credentials: 'same-origin',
            body: JSON.stringify({ items })
        });

        if (!response.ok) {
            console.error('Stock check failed:', response.status);
            return false;
        }

        const data = await response.json();
        if (data?.ok) {
            return true;
        }

        const results = Array.isArray(data?.items) ? data.items : [];
        const byProduct = new Map(results.map(item => [String(item.product_id), item]));

        rows.forEach(row => {
            const productId = row.querySelector('.product-id-input')?.value;
            if (!productId) return;

            const result = byProduct.get(String(productId));
            if (result && !result.ok) {
                showStockError(row, result.available);
            }
        });

        return false;
    } catch (e) {
        console.error('Stock check error:', e);
        return false;
    }
}

// ---------- Init ----------
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('itemsBody');
    tbody.appendChild(createRow());
    updateAllTotals();

    document.getElementById('addRow').addEventListener('click', function() {
        tbody.appendChild(createRow());
        updateAllTotals();
        const newRowId = rowCounter - 1;
        document.querySelector(`#row-${newRowId} .product-name-input`).focus();
    });

    // Final tax change event
    document.getElementById('finalTaxSelect').addEventListener('change', function() {
        const totalBaseText = document.getElementById('totalBaseCost').textContent;
        const totalBase = parseFloat(totalBaseText.replace(/[^0-9.-]/g, '')) || 0;
        applyFinalTax(totalBase);
    });

    // Escape closes dropdown
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            removeAutocomplete();
        }
    });

    // Email modal functionality
    const emailModal = document.getElementById('emailModal');
    const saveInvoiceBtn = document.getElementById('saveInvoiceBtn');
    const sendEmailYes = document.getElementById('sendEmailYes');
    const sendEmailNo = document.getElementById('sendEmailNo');
    const sendEmailCancel = document.getElementById('sendEmailCancel');
    const customerEmailInput = document.getElementById('customerEmailInput');
    let sendEmail = false;

    // Get customer email when customer is selected
    function getCustomerEmail(customerId) {
        const customers = @json($customers->toArray());
        const customer = customers.find(c => c.id == customerId);
        return customer ? customer.email : '';
    }

    // Show email modal when save button is clicked
    saveInvoiceBtn.addEventListener('click', async function() {
        const customer = document.querySelector('select[name="customer_id"]').value;
        if (!customer) {
            alert('Please select a customer');
            return;
        }

        const stockOk = await checkStock();
        if (!stockOk) {
            return;
        }

        const customerEmail = getCustomerEmail(customer);
        customerEmailInput.value = customerEmail || 'No email on file';
        
        if (customerEmail) {
            emailModal.style.display = 'flex'; // Show modal
        } else {
            // No email, just submit the form
            submitForm(false);
        }
    });

    // Handle Yes button - send email
    sendEmailYes.addEventListener('click', function() {
        emailModal.style.display = 'none'; // Hide modal
        submitForm(true);
    });

    // Handle No button - don't send email
    sendEmailNo.addEventListener('click', function() {
        emailModal.style.display = 'none'; // Hide modal
        submitForm(false);
    });

    // Handle Cancel button - close modal without saving
    sendEmailCancel.addEventListener('click', function() {
        emailModal.style.display = 'none'; // Hide modal
        // Don't submit the form, just close the modal
    });

    // Submit form with email flag
    function submitForm(withEmail) {
        // Add hidden input for email flag
        let emailInput = document.querySelector('input[name="send_email"]');
        if (!emailInput) {
            emailInput = document.createElement('input');
            emailInput.type = 'hidden';
            emailInput.name = 'send_email';
            document.getElementById('purchaseForm').appendChild(emailInput);
        }
        emailInput.value = withEmail ? '1' : '0';

        // Validate and submit form
        const rows = document.querySelectorAll('.purchase-row');
        for (let i = 0; i < rows.length; i++) {
            const productName = rows[i].querySelector('.product-name-input').value.trim();
            const qty = parseFloat(rows[i].querySelector('.qty-input').value);
            const cost = parseFloat(rows[i].querySelector('.cost-input').value);

            if (!productName) {
                alert(`Row ${i + 1}: Please enter a product name`);
                return;
            }
            if (!(qty > 0)) {
                alert(`Row ${i + 1}: Quantity must be greater than 0`);
                return;
            }
            if (cost < 0 || isNaN(cost)) {
                alert(`Row ${i + 1}: Unit cost cannot be negative`);
                return;
            }
        }
        
        document.getElementById('purchaseForm').submit();
    }
});
</script>

@endsection
