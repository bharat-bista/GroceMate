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

    @include('inventory.partials.payment-method', [
        'paymentDefault' => 'cash',
        'paymentLabel'   => 'How the customer will pay for this invoice. Cash/Bank adds to business balance; Credit adds to customer due.',
    ])

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
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-semibold text-slate-700">Discount:</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <input type="number" id="discountInput" name="discount_pct"
                                       step="any" min="0" max="100"
                                       value="0"
                                       class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-1.5 text-right">
                                <span class="text-sm text-slate-500 shrink-0">%</span>
                            </div>
                            <div class="text-xs text-blue-600 text-right mt-0.5" id="discountRupees"></div>
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
    return (isNaN(n) ? 0 : Math.round(n)).toString();
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
    business_id: {{ $product['business_id'] ?? 0 }},
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

async function searchBatchesApi(query) {
    try {
        const businessId = document.querySelector('select[name="business_id"]')?.value || '';
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch(
            `/pos/batches/search?q=${encodeURIComponent(query)}&business_id=${encodeURIComponent(businessId)}`,
            {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                },
                credentials: 'same-origin',
            }
        );

        if (!response.ok) throw new Error('API ' + response.status);

        const data = await response.json();
        if (!Array.isArray(data)) throw new Error('Invalid response');

        return data;
    } catch (e) {
        return [];
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
    row.querySelector('.base-cost-input').value = Math.round(baseCost);
    row.querySelector('.subtotal-input').value = Math.round(subtotal);

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
    
    const discountPct    = parseFloat(document.getElementById('discountInput')?.value) || 0;
    const discountAmount = Math.round((totalBase + taxAmount) * discountPct / 100);
    const grandTotal     = Math.max(0, totalBase + taxAmount - discountAmount);

    const discountRupees = document.getElementById('discountRupees');
    if (discountRupees) {
        discountRupees.textContent = discountPct > 0 ? `− Rs ${formatCurrency(discountAmount)}` : '';
    }

    totalTaxElement.textContent    = formatCurrency(taxAmount);
    grandTotalElement.textContent  = formatCurrency(grandTotal);
}

// ---------- Row population from a batch result ----------
function updateRowFromBatch(rowId, batch) {
    const row = document.getElementById(`row-${rowId}`);
    if (!row) return;

    row.querySelector('.product-name-input').value  = batch.product_name;
    row.querySelector('.product-id-input').value    = batch.product_id;
    row.querySelector('.product-name-hidden').value = batch.product_name;
    row.querySelector('.batch-id-input').value      = batch.batch_id;

    const unit = batch.unit || 'pcs';
    const unitSelect  = row.querySelector('.unit-select');
    const unitDisplay = row.querySelector('.unit-display');
    unitSelect.value = unit;
    unitDisplay.textContent = unit;
    unitSelect.classList.add('hidden');
    unitDisplay.classList.remove('hidden');

    row.querySelector('.cost-input').value = batch.selling_price || 0;

    const qtyInput = row.querySelector('.qty-input');
    const batchMax = Math.floor(batch.qty_remaining);
    qtyInput.max   = batchMax;
    qtyInput.value = Math.min(parseInt(qtyInput.value) || 1, batchMax);

    validateQtyRow(row);
    updateSaveButtonState();
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

    const qLower = query.toLowerCase();

    if (results.length === 0) {
        const empty = document.createElement('div');
        empty.className = 'px-3 py-4 text-sm text-slate-500 text-center';
        empty.textContent = 'No products found for this business.';
        list.appendChild(empty);
    }

    results.forEach((batch) => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'block w-full px-3 py-2 text-left hover:bg-slate-50 border-b border-slate-100 last:border-0';

        const name    = batch.product_name || '';
        const idx     = name.toLowerCase().indexOf(qLower);
        const highlighted = idx >= 0
            ? name.substring(0, idx)
              + `<span class="text-blue-700 font-semibold">${name.substring(idx, idx + query.length)}</span>`
              + name.substring(idx + query.length)
            : name;

        const expiryHtml = batch.expiry_date
            ? `<span class="text-orange-600">Exp: ${batch.expiry_date}</span>`
            : '';

        item.innerHTML = `
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-semibold text-slate-900 truncate">${highlighted}</div>
                    <div class="text-[11px] text-slate-500 leading-tight mt-0.5">
                        Batch: <span class="font-medium text-slate-700">${batch.batch_no}</span>
                        &nbsp;|&nbsp; Avail: <span class="font-medium text-green-700">${batch.qty_remaining} ${batch.unit}</span>
                        ${expiryHtml ? `&nbsp;|&nbsp; ${expiryHtml}` : ''}
                    </div>
                </div>
                <div class="text-[11px] font-bold text-slate-700 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100 shrink-0 whitespace-nowrap">
                    Rs ${formatCurrency(batch.selling_price ?? 0)}/${batch.unit}
                </div>
            </div>
        `;

        item.addEventListener('click', () => {
            updateRowFromBatch(rowId, batch);
            dropdown.remove();
            activeAutocomplete = null;
            document.getElementById(`row-${rowId}`)?.querySelector('.qty-input')?.focus();
        });

        list.appendChild(item);
    });

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

    if (query.length < 2) {
        removeAutocomplete();
        return;
    }

    searchTimeout = setTimeout(async () => {
        const results = await searchBatchesApi(query);
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
                <input type="hidden" name="items[${rowId}][batch_id]" class="batch-id-input" />
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
                   step="1" min="1"
                   inputmode="numeric"
                   value="1"
                   required
                   class="qty-input w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm px-2 py-1.5">
        </td>
        <td class="px-4 py-3 min-w-[100px]">
            <input name="items[${rowId}][unit_cost]"
                   type="number"
                   step="any" min="0" max="9999999"
                   inputmode="numeric" data-money
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

    // update totals when qty or cost changes; validate batch limit in real time
    row.querySelector('.qty-input').addEventListener('input', () => {
        validateQtyRow(row);
        updateSaveButtonState();
        updateAllTotals();
    });
    row.querySelector('.cost-input').addEventListener('input', updateAllTotals);

    // remove row
    row.querySelector('.remove-btn').addEventListener('click', () => removeRow(rowId));

    // apply money sanitizer to dynamically created cost input
    if (window.GroceMate) GroceMate.money.init(row);

    return row;
}

function removeRow(rowId) {
    const rows = document.querySelectorAll('.purchase-row');
    if (rows.length <= 1) {
        GroceMate.notify.error('At least one item is required.');
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

function clearRowStockError(row) {
    row.querySelector('.qty-input')?.classList.remove('border-red-500');
    row.querySelector('.stock-error')?.remove();
}

function validateQtyRow(row) {
    const qtyInput = row.querySelector('.qty-input');
    if (!qtyInput) return true;
    const qty    = parseFloat(qtyInput.value) || 0;
    const maxQty = parseFloat(qtyInput.max);
    if (!isNaN(maxQty) && qty > maxQty) {
        showStockError(row, maxQty);
        return false;
    }
    clearRowStockError(row);
    return true;
}

function updateSaveButtonState() {
    const saveBtn = document.getElementById('saveInvoiceBtn');
    if (!saveBtn) return;
    const hasError = Array.from(document.querySelectorAll('.purchase-row')).some(r => {
        const qi = r.querySelector('.qty-input');
        if (!qi) return false;
        const max = parseFloat(qi.max);
        return !isNaN(max) && (parseFloat(qi.value) || 0) > max;
    });
    saveBtn.disabled = hasError;
    saveBtn.classList.toggle('opacity-50', hasError);
    saveBtn.classList.toggle('cursor-not-allowed', hasError);
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

    const gate = GroceMate.formGate.init({
        watch:    ['select[name="business_id"]', 'select[name="customer_id"]', 'input[name="invoice_no"]'],
        gate:     '#itemsBody',
        rowClass: '.purchase-row',
        addBtn:   '#addRow',
    });

    document.getElementById('addRow').addEventListener('click', function() {
        tbody.appendChild(createRow());
        updateAllTotals();
        gate.check();
        const newRowId = rowCounter - 1;
        document.querySelector(`#row-${newRowId} .product-name-input`).focus();
    });

    // Final tax change event
    document.getElementById('finalTaxSelect').addEventListener('change', function() {
        const totalBaseText = document.getElementById('totalBaseCost').textContent;
        const totalBase = parseFloat(totalBaseText.replace(/[^0-9.-]/g, '')) || 0;
        applyFinalTax(totalBase);
    });

    // Discount change event
    document.getElementById('discountInput').addEventListener('input', function() {
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
            GroceMate.notify.error('Please select a customer.');
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

            const productId = rows[i].querySelector('.product-id-input').value.trim();
            if (!productId) {
                GroceMate.notify.error(`Row ${i + 1}: Please select a product from the list.`);
                return;
            }
            if (!productName) {
                GroceMate.notify.error(`Row ${i + 1}: Please enter a product name.`);
                return;
            }
            if (!(qty > 0)) {
                GroceMate.notify.error(`Row ${i + 1}: Quantity must be greater than 0.`);
                return;
            }
            const qtyMax = parseFloat(rows[i].querySelector('.qty-input').max);
            if (!isNaN(qtyMax) && qty > qtyMax) {
                GroceMate.notify.error(`Row ${i + 1}: Quantity exceeds available batch stock (${qtyMax}).`);
                return;
            }
            if (cost < 0 || isNaN(cost)) {
                GroceMate.notify.error(`Row ${i + 1}: Unit cost cannot be negative.`);
                return;
            }
        }
        
        document.getElementById('purchaseForm').submit();
    }
});
</script>

@endsection
