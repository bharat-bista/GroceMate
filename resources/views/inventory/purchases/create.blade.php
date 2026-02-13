@extends('inventory.layouts.inventory')

@section('title', 'New Purchase')
@section('heading', 'New Purchase (Stock-In)')
@section('subtitle', 'Add supplier, date and multiple products with taxes')

@section('content')
<form method="POST" action="{{ route('inventory.purchases.store') }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6" id="purchaseForm">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="text-sm text-slate-600">Business Account *</label>
            <select name="business_id" required 
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
                <option value="">Select Business Account</option>
                @foreach($businesses as $b)
                    <option value="{{ $b->id }}" @selected(old('business_id')==$b->id)>{{ $b->business_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm text-slate-600">Supplier *</label>
            <select name="supplier_id" required 
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400">
                <option value="">Select Supplier</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm text-slate-600">Purchase Date *</label>
            <input type="date" name="purchase_date" value="{{ old('purchase_date', now()->toDateString()) }}" required
                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
        </div>

        <div>
            <label class="text-sm text-slate-600">Invoice No *</label>
            <input name="invoice_no" value="{{ old('invoice_no') }}" required
                   class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 hover:border-slate-400" />
        </div>
    </div>

    <div class="border-t border-slate-200 pt-5">
        <div class="flex items-center justify-between mb-4">
            <div class="font-semibold text-lg">Purchase Items</div>
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
                        <th class="text-left px-4 py-3 font-medium">Unit Cost</th>
                        <th class="text-left px-4 py-3 font-medium">Base Cost</th>
                        <th class="text-left px-4 py-3 font-medium">Subtotal</th>
                        <th class="text-left px-4 py-3 font-medium">Expiry Date</th>
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
            <strong>Tip:</strong> Start typing product name to see suggestions from purchase history.
            New products will be created automatically.
        </p>
    </div>

    <!-- Save Purchase Button Section -->
    <div class="flex gap-3 pt-4 border-t border-slate-200 mt-6">
        <button class="px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
      Save
    </button>
        <a href="{{ route('inventory.suppliers.index') }}"
       class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Cancel
    </a>
    </div>
</form>

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
    last_cost: {{ $product['last_cost'] ?? 0 }},
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
    formatted: "{{ $tax->type === 'percentage' ? $tax->rate . '%' : $tax->rate . ' per unit' }}",
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
        
        const response = await fetch(`/inventory/purchases/search-products?q=${encodeURIComponent(query)}`, {
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
        console.log('📡 Headers:', response.headers);
        
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


// ---------- Product selection (IMPORTANT FIX) ----------
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
    const lastCost = payload.last_cost || 0;

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

    costInput.value = lastCost;

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
dropdown.className =
    'autocomplete-dropdown absolute left-0 right-0 mt-1 bg-white border border-slate-300 rounded-xl shadow-sm z-50 max-h-60 overflow-y-auto text-sm';
dropdown.style.transition = 'all 0.2s';
dropdown.style.boxShadow = '0 2px 6px rgba(0,0,0,0.1)';

    // Better UI container
    const list = document.createElement('div');
    list.className = 'max-h-80 overflow-y-auto';

    // Check exact match (ignore case)
    const qLower = query.toLowerCase();
    const hasExact = results.some(p => (p.name || '').toLowerCase() === qLower);

    // --- Existing products first ---
    if (results.length > 0) {
        results.slice(0, 10).forEach((p) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className =
    'block w-full px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-xl cursor-pointer truncate';
item.style.transition = 'background 0.2s';
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
                ${p.sku ? `SKU: ${p.sku} • ` : ''}Unit: ${p.unit ?? '-'}
            </div>
        </div>
        <div class="text-[11px] font-bold text-slate-600 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100 shrink-0">
            Last: ${formatCurrency(p.last_cost ?? 0)}
        </div>
    </div>
`;
dropdown.className = 'autocomplete-dropdown absolute left-0 min-w-[220px] mt-1 bg-white border border-slate-300 rounded-xl shadow-lg z-50 max-h-60 overflow-y-auto';
            item.addEventListener('click', () => {
                updateProductFromInput(rowId, {
                    id: p.id,
                    name: p.name,
                    unit: p.unit,
                    last_cost: p.last_cost
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
        createBtn.className =
            'w-full text-left px-3 py-2 bg-green-50 hover:bg-green-100 outline-none';

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
        <td class="px-4 py-3">
            <input name="items[${rowId}][expiry_date]"
                   type="date"
                   class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm px-2 py-1.5">
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button"
                    class="remove-btn px-2 py-1 rounded bg-red-50 text-red-700 hover:bg-red-100 text-xs font-medium border border-red-200">
                ✕
            </button>
        </td>
    `;

    // bind events (better than inline HTML)
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
row.querySelector('.qty-input').addEventListener('input', updateAllTotals);
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

    // form validation (keep simple)
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        const supplier = document.querySelector('select[name="supplier_id"]').value;
        if (!supplier) {
            e.preventDefault();
            alert('Please select a supplier');
            return;
        }

        const rows = document.querySelectorAll('.purchase-row');
        for (let i = 0; i < rows.length; i++) {
            const productName = rows[i].querySelector('.product-name-input').value.trim();
            const qty = parseFloat(rows[i].querySelector('.qty-input').value);
            const cost = parseFloat(rows[i].querySelector('.cost-input').value);

            if (!productName) {
                e.preventDefault();
                alert(`Row ${i + 1}: Please enter a product name`);
                return;
            }
            if (!(qty > 0)) {
                e.preventDefault();
                alert(`Row ${i + 1}: Quantity must be greater than 0`);
                return;
            }
            if (cost < 0 || isNaN(cost)) {
                e.preventDefault();
                alert(`Row ${i + 1}: Unit cost cannot be negative`);
                return;
            }
        }
    });
});
</script>

@endsection