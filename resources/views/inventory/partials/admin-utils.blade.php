{{--
    GroceMate Admin Utilities — S0-3
    Included once by inventory.layouts.inventory.
    Three shared utilities available globally as window.GroceMate.*

    1. GroceMate.money   — whole-rupee money input enforcement
    2. GroceMate.notify  — single client-side notification (replaces alert())
    3. GroceMate.formGate — disables line-item rows until header fields are filled
--}}
<script>
window.GroceMate = window.GroceMate || {};

// ─── 1. Money Input ──────────────────────────────────────────────────────────
// Add data-money to any <input type="number"> that must stay whole-rupee.
// Auto-initialised on DOMContentLoaded for all data-money elements.
// Can be re-called after dynamic DOM changes: GroceMate.money.init(containerEl)
//
// Helpers:
//   GroceMate.money.parse('1,500') → 1500
//   GroceMate.money.format(1500)   → 'Rs 1,500'
GroceMate.money = {
    parse(val) {
        return Math.round(parseFloat(String(val).replace(/[^0-9.-]/g, '')) || 0);
    },
    format(val) {
        return 'Rs ' + Math.round(val || 0).toLocaleString();
    },
    init(scope) {
        const root = scope instanceof Element
            ? scope
            : (scope ? document.querySelector(scope) : document);
        if (!root) return;
        root.querySelectorAll('input[data-money]').forEach(el => {
            if (el.dataset.moneyInit) return; // prevent double-binding
            el.dataset.moneyInit = '1';

            const cap = parseInt(el.getAttribute('max'), 10) || 9999999;

            // Block decimal key entry
            el.addEventListener('keydown', e => {
                if (e.key === '.' || e.key === ',') e.preventDefault();
            });

            // Strip decimals + enforce cap on every input event.
            // This catches floating-point artefacts the browser injects for
            // very large numbers even when step="1" (IEEE 754 precision issue).
            el.addEventListener('input', () => {
                let v = el.value;
                if (v.includes('.')) v = v.split('.')[0]; // strip FP decimal
                v = v.replace(/[^0-9]/g, '');             // strip non-digits
                const n = parseInt(v, 10);
                if (!isNaN(n) && n > cap) v = String(cap);
                if (el.value !== v) el.value = v;
            });

            // Round on blur to catch paste / autofill
            el.addEventListener('blur', () => {
                if (el.value !== '') {
                    let n = Math.round(parseFloat(el.value) || 0);
                    if (n > cap) n = cap;
                    el.value = n;
                }
            });
        });
    }
};

// ─── 2. Notification Utility ─────────────────────────────────────────────────
// Shows exactly one client-side notification banner. Replaces bare alert().
// Inserts at the top of the main content pane, auto-dismisses success after 5s.
//
// Usage:
//   GroceMate.notify.success('Saved successfully.')
//   GroceMate.notify.error('Something went wrong.')
GroceMate.notify = {
    success(msg) { this._show(msg, 'success'); },
    error(msg)   { this._show(msg, 'error'); },
    _show(msg, type) {
        // Remove any previous client-side notification (never the server flash)
        document.querySelectorAll('.grm-notify').forEach(el => el.remove());

        const isOk = type === 'success';
        const el   = document.createElement('div');
        el.className = 'grm-notify mb-4 rounded-xl border p-4 text-sm font-medium ' + (
            isOk
                ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                : 'border-red-200   bg-red-50   text-red-800'
        );
        el.textContent = msg;

        // Insert as first child of the main .p-6 content wrapper
        const anchor = document.querySelector('main .p-6') || document.body;
        anchor.insertBefore(el, anchor.firstChild);

        if (isOk) setTimeout(() => el.remove(), 5000);
    }
};

// ─── 3. Form Gate ────────────────────────────────────────────────────────────
// Disables line-item rows until all required header fields are filled.
// Returns a { check } handle so createRow() can call check() after adding rows.
//
// Usage (purchase form — S1-5):
//   const gate = GroceMate.formGate.init({
//     watch:    ['select[name="business_id"]', 'select[name="supplier_id"]', 'input[name="invoice_no"]'],
//     gate:     '#itemsBody',
//     rowClass: '.purchase-row',
//     addBtn:   '#addRow',
//   });
//   // After creating a new row dynamically:
//   gate.check();
//
// Usage (POS invoice form — S2-5):
//   GroceMate.formGate.init({
//     watch:    ['select[name="business_id"]', 'select[name="customer_id"]'],
//     gate:     '#itemsBody',
//     rowClass: '.purchase-row',
//     addBtn:   '#addRow',
//   });
GroceMate.formGate = {
    init({ watch = [], gate, rowClass, addBtn } = {}) {
        const gateEl   = gate   ? (typeof gate   === 'string' ? document.querySelector(gate)   : gate)   : null;
        const addBtnEl = addBtn ? (typeof addBtn === 'string' ? document.querySelector(addBtn) : addBtn) : null;

        const update = () => {
            const allFilled = watch.every(sel => {
                const el = document.querySelector(sel);
                return el && String(el.value).trim() !== '';
            });

            // Collect rows
            const rows = gateEl
                ? (rowClass ? gateEl.querySelectorAll(rowClass) : [gateEl])
                : (rowClass ? document.querySelectorAll(rowClass) : []);

            rows.forEach(row => {
                row.querySelectorAll('input, select, textarea, button').forEach(inp => {
                    if (inp.classList.contains('remove-btn')) return; // always keep remove buttons active
                    inp.disabled = !allFilled;
                });
                row.style.opacity       = allFilled ? '1'    : '0.45';
                row.style.pointerEvents = allFilled ? ''     : 'none';
                row.style.userSelect    = allFilled ? ''     : 'none';
            });

            if (addBtnEl) addBtnEl.disabled = !allFilled;
        };

        watch.forEach(sel => {
            const el = document.querySelector(sel);
            if (el) {
                el.addEventListener('input',  update);
                el.addEventListener('change', update);
            }
        });

        update(); // set initial state immediately

        return { check: update }; // caller can trigger re-check after DOM changes
    }
};

// Auto-init money inputs
document.addEventListener('DOMContentLoaded', () => GroceMate.money.init());
</script>
