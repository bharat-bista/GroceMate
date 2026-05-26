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

            // Clamp any pre-existing value (e.g. old() flash after validation error)
            if (el.value !== '') {
                const parsed = parseFloat(el.value);
                let n = isFinite(parsed) ? Math.trunc(parsed) : 0;
                if (n < 0) n = 0;
                if (n > cap) n = cap;
                el.value = String(n);
            }

            // Block decimal key entry
            el.addEventListener('keydown', e => {
                if (e.key === '.' || e.key === ',') e.preventDefault();
            });

            // Enforce whole-rupee integer on every input event.
            // Uses parseFloat first to handle browser-injected scientific
            // notation (e.g. 1e5), then Math.trunc to strip any fractional part
            // without any floating-point rounding math.
            el.addEventListener('input', () => {
                if (el.value === '') return;
                const parsed = parseFloat(el.value);
                if (!isFinite(parsed)) { el.value = '0'; return; }
                let n = Math.trunc(parsed);
                if (n < 0) n = 0;
                if (n > cap) n = cap;
                const v = String(n);
                if (el.value !== v) el.value = v;
            });

            // Integer-safe clamp on blur — catches paste / autofill.
            // Uses the same trunc path, no Math.round floating-point math.
            el.addEventListener('blur', () => {
                if (el.value !== '') {
                    const parsed = parseFloat(el.value);
                    let n = isFinite(parsed) ? Math.trunc(parsed) : 0;
                    if (n < 0) n = 0;
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
