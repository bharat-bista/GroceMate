# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

GroceMate is a Laravel-based grocery management system with four integrated subsystems sharing a single database:
- **Inventory** — product/batch/FIFO stock tracking, purchase entry, expiry alerts
- **POS** — invoicing with credit/cash/bank payments, customer due ledger, supplier payment gateway integration
- **Ecommerce** — public storefront, eSewa/ConnectIPS checkout, admin order management
- **Financial** — business P&L, income/expense tracking, supplier payments, balance auto-update via model events

## Technology Stack

**Backend**: Laravel 12.x, PHP 8.2+  
**Frontend**: Blade templates, Tailwind CSS 4.x, Alpine.js 3.x, Vanilla JS, SweetAlert2  
**Database**: MySQL (production) / SQLite (development fallback)  
**Queue/Session/Cache**: Database driver  
**Build Tools**: Vite 7.x, PostCSS  
**Libraries**: Socialite (Google OAuth), dompdf (PDF export), Laravel Telescope

## Essential Commands

### Development
```bash
composer run dev                     # Full stack: Laravel + queue + logs + Vite
php artisan serve                    # HTTP only
php artisan queue:listen --tries=1   # Queue worker
npm run dev                          # Vite asset server
```

### Database
```bash
php artisan migrate
php artisan migrate:rollback
php artisan migrate:refresh          # Rollback all + re-run
php artisan tinker                   # REPL for quick DB inspection
```

### Testing
```bash
composer run test
php artisan test tests/Unit
php artisan test tests/Feature
php artisan test --filter=TestName
```

### Code Quality & Cache
```bash
vendor/bin/pint                  # Format (Laravel Pint)
vendor/bin/pint --test           # Check without fixing
php artisan config:clear && php artisan view:clear && php artisan cache:clear
```

### Setup from scratch
```bash
composer run setup
# or manually: composer install → cp .env.example .env → php artisan key:generate
# → php artisan migrate --force → npm install → npm run build
```

## Route Architecture

Routes in `routes/web.php` are organized into these middleware groups:

| Group | Prefix | Middleware | Covers |
|-------|--------|------------|--------|
| Public | — | none | Login, register, OTP, Google OAuth, home, cart, checkout, product pages |
| Frontend orders | `/orders*` | none | Customer order list/detail, cancel request |
| Inventory | `/inventory/*` | `auth`, `admin_or_staff` | Dashboard, products, purchases, suppliers, ecommerce products, sliders, orders, contacts |
| POS | `/pos/*` | `auth`, `admin` | Invoices, customers, income, expenses, supplier payments, Khalti/eSewa POS callbacks |
| Business/Admin | `/business/*`, `/admin/accounts/*`, `/taxes/*`, `/settings/*` | `auth`, `admin` | Business CRUD, account management, tax config, delivery fees |
| CSRF-exempt | `/khalti/verify`, `/pos/esewa/callback`, `/pos/khalti/callback`, `/checkout/esewa/callback` | — | Payment gateway callbacks |

Middleware aliases are registered in `bootstrap/app.php` via `->alias()` (not `Kernel.php`):
- `admin` → `AdminMiddleware` (role_id = 1)
- `admin_or_staff` → `AdminOrStaffMiddleware` (role_id = 1 or 3)

## Key Controllers

### `frontend/OrderController.php`
Handles both customer-facing order placement (COD/ConnectIPS) and all admin order management. Admin routes are prefixed `inventory.orders.*` under `admin_or_staff`.

- `store()` — validates items, derives `business_id` from first cart item's ecommerce product → product → business (falls back to `Business::min('id')`); guards admin/staff with JSON error response
- `updateDeliveryStatus()` — on cancel transition: restores ecommerce stock via `restoreEcommerceStock()`, creates `OrderRefund` if order was paid, calls `EcommerceIncomeSyncService::syncOrder()`
- `verifyPaymentSlip()` — PATCH for ConnectIPS slip verification; blocked when `delivery_status === 'cancelled'`
- Order view guards: Verify/Reject buttons only shown when `payment_status === 'pending' && delivery_status !== 'cancelled'`

### `frontend/CheckoutController.php`
Handles eSewa payment flow separately from OrderController:
- `initiateEsewa()` — validates cart, stores full order payload in session (`esewa_checkout_order`), returns `esewa_redirect.blade.php` HTML which auto-submits to eSewa gateway
- `esewaCallback()` — on success: verifies HMAC, creates order + deducts stock + syncs income in transaction; on failure/cancel: redirects to `/checkout` with error flash; derives `business_id` same way as OrderController

### `POS/InvoiceController.php`
- Pre-validates stock for all items before any DB writes (per-row error messages)
- Cash/bank invoices create an `Income` record with `customer_id = null` (critical: prevents polluting customer due calculation)
- Credit invoices do NOT create Income records — payment is recorded separately via the Income module

### `POS/CustomerController.php`
- `calculateTotalDue()` = `opening_due + credit_invoice_total - all_income_payments`
- Due ledger only counts `payment_method = 'credit'` invoices, NOT cash/bank invoices
- Setting `customer_id = null` on cash/bank Income records ensures they don't reduce this customer's due balance

### `Inventory/PurchaseController.php`
- Product deduplication: same name + same brand → reuse existing product; same name + different brand → create new product (never mutate existing product's brand)
- Each purchase item creates a `StockBatch` record; updates the `Stock` quantity cache

## Model Architecture

### Stock lifecycle
```
Purchase → PurchaseItem → StockBatch (status: active|depleted|expired)
                       ↗
Product ─→ Stock (quantity cache = sum of active batch qty_remaining)
         └→ EcommerceProduct (ecommerce_stock = reserved qty for storefront)
```

POS available stock = `Stock.quantity - EcommerceProduct.ecommerce_stock`  
Computed by `Product::posAvailableStock()` and `FifoStockService::getAvailable()`.

Products can only be deleted when `stock.quantity = 0` (enforced in `ProductController::destroy()`).

### `EcommerceProduct`
- `status` auto-set to `in_stock`/`out_of_stock` via `saving` model event based on `ecommerce_stock`
- `scopeStorefrontVisible()` — status=in_stock AND ecommerce_stock > 0
- `scopeActive()` — base product must have `is_active=true` AND `is_listed=true`

### `Income` model events (critical)
`Income::created` → increments `business.balance`  
`Income::updated` → adjusts balance delta when `amount_received` or `business_id` changes  
`Income::deleted` → decrements `business.balance`  
Use `Income::$skipBalanceUpdate = true` to bypass when doing bulk operations.

### `Order` model
- `canRequestCancellation()` — cancellation_request_status is null, not cancelled/delivered, and within 30 minutes of creation
- `isLocked()` — delivery_status = 'delivered'
- `isPaymentLocked()` / `isPaid()` — payment_status = 'verified'

## Services

### `FifoStockService`
Core stock consumption engine. Key methods:
- `consume(productId, qty, 'pos'|'ecommerce')` — consumes oldest active batches; returns `['batches_used' => [...]]`
- `consumeFromBatch(batchId, qty)` — batch-specific consumption for POS
- `canConsume(productId, qty, 'pos')` — pre-check without mutating
- `reverse(productId, qty, batchesUsed)` — undo a previous `consume()` (used on order cancel)
- `batchPosAvailable(batchId)` — available qty in a specific batch for POS (after ecommerce reservation)

Always call within `DB::transaction()`.

### `EcommerceIncomeSyncService`
Idempotent sync between ecommerce orders and POS income records. Called after every order status change.
- `syncOrder(order)` — if order is "settled" (payment_status=verified AND delivery_status≠cancelled): upserts Income records per business (reference: `ECOM-ORDER-{id}-BIZ-{businessId}`). If not settled: deletes any existing income records.
- `syncBusinessOrders(businessId)` — bulk sync all orders for a business (called on business dashboard load)

### `InvoiceNumberService`
Sequential invoice number generation. Call `InvoiceNumberService::generateInvoiceNumber()`.

### `AdminChatbotService`
Local mode returns hardcoded responses for low stock, expiry, due amounts, analytics. Vanna mode (optional) queries a Python sidecar. Config: `config/chatbot.php`.

## Shared Admin Utilities

Defined in `resources/views/inventory/partials/admin-utils.blade.php`, included by the inventory layout. Available as `window.GroceMate.*` on all admin pages.

### `GroceMate.money`
Add `data-money` + `step="any"` to `<input type="number">` for integer-only enforcement (uses `Math.trunc(parseFloat())`, not `Math.round`). Re-call `GroceMate.money.init(el)` after dynamic DOM changes.

### `GroceMate.notify`
`GroceMate.notify.success('msg')` / `.error('msg')` — replaces bare `alert()`. Shows one banner at the top of the content pane; success auto-dismisses after 5s.

### `GroceMate.formGate`
Disables line-item rows until all header fields are filled. Used by purchase entry and POS new sale.
```js
const gate = GroceMate.formGate.init({
    watch:    ['select[name="business_id"]', 'select[name="supplier_id"]', 'input[name="invoice_no"]'],
    gate:     '#itemsBody',
    rowClass: '.purchase-row',
    addBtn:   '#addRow',
});
gate.check(); // call after adding a new row dynamically
```

## Frontend JavaScript Patterns

### Cart (`window.GroceMateCart`)
Defined in `resources/views/frontend/layouts/main.blade.php`. Manages localStorage keys:
- `gm_cart_items` — full cart array
- `gm_buy_now_item` — single Buy Now item
- `gm_checkout_selected_items` — cart items selected for checkout
- `gm_checkout_draft` — saved form field values

### SweetAlert2 conventions
- Loaded from CDN in `frontend/layouts/main.blade.php` (always available on frontend)
- In admin views: loaded via `@push('scripts')` only on pages that need it
- Theme: `confirmButtonColor: '#16a34a'`, `cancelButtonColor: '#1e293b'` (or `#64748b` for secondary actions)

### Frontend CSS variables
Each frontend page defines `:root { --gm-primary: #2E7D32; --gm-primary-dark: #1B5E20; --gm-primary-light: #4CAF50; --gm-accent: #FF6B35; --gm-white: #FFFFFF; --gm-light: #F8FBF8; --gm-gray: #6B7280; --gm-gray-light: #E5E7EB; --gm-dark: #1F2937; --gm-shadow: ...; --gm-shadow-lg: ...; --gm-radius: 16px; --gm-radius-sm: 10px; --gm-transition: all 0.3s ease; }` in its own `<style>` block. This is NOT in a shared file — each page must include the `:root` block if it uses these variables.

## Role-Based Architecture

| role_id | Role | Access |
|---------|------|--------|
| 1 | Admin | Everything including POS, business settings, account management |
| 2 | Customer | Public frontend + own orders only. Registered with `status='N'` (unverified via OTP). `business_id` is nullable. |
| 3 | Staff | Inventory + ecommerce only (no POS, no business settings) |

Helper methods on `User`: `isAdmin()`, `isStaff()`, `isCustomer()`, `canAccessInventoryPanel()` (admin or staff).

Admin/staff accounts cannot place ecommerce orders — `OrderController::store()` returns a JSON error (`admin_staff_error: true`) which the checkout JS handles with a SweetAlert2 modal.

**Admin account creation flow**: Admin submits form → OTP sent to new account email → OTP verified → account created. OTP stored in `otp_resets` with `purpose='managed_account_register'`.

## Payment Gateways

### Ecommerce (eSewa)
1. `initiateEsewa()` stores order data in `session('esewa_checkout_order')`, returns redirect HTML opened in a new popup window
2. Original checkout tab polls `newWindow.closed` every 600ms and re-enables the Place Order button when the window closes
3. `esewaCallback()`: success → create order in DB + sync income + redirect to `/orders` with `order_confirmation` session flash; cancel/failure → redirect to `/checkout` with error flash
4. Orders page clears cart localStorage keys when `order_confirmation` flash is present

### POS (Khalti + eSewa for supplier payments)
- `SupplierPaymentController` handles both gateways for supplier payments
- Callbacks at `/pos/khalti/callback` and `/pos/esewa/callback` (CSRF-exempt)

### Ecommerce (ConnectIPS / bank transfer)
- Customer uploads payment slip (base64 image/PDF) during checkout
- Admin manually verifies via PATCH `/inventory/orders/{order}/verify-slip`
- Verify/Reject buttons shown only when `payment_status === 'pending' && delivery_status !== 'cancelled'`

Config keys: `KHALTI_PUBLIC_KEY`, `KHALTI_SECRET_KEY`, `ESEWA_PRODUCT_CODE`, `ESEWA_SECRET_KEY`, `ESEWA_PAYMENT_URL`, `ESEWA_STATUS_URL`.

## Key Invariants

1. **Business isolation**: Always filter queries by `business_id`. Every product, invoice, purchase, income, and expense belongs to a business.
2. **Stock transactions**: Always use `FifoStockService` inside `DB::transaction()` — never update `StockBatch` or `Stock` directly.
3. **Income creation side-effect**: Creating/updating/deleting `Income` records changes `business.balance` automatically via model events. Set `Income::$skipBalanceUpdate = true` for bulk operations.
4. **Customer due integrity**: Cash/bank POS sales must set `customer_id = null` on their Income records to avoid reducing the credit-only due formula.
5. **EcommerceIncomeSyncService**: Must be called after every order status change (delivery or payment). It is idempotent — safe to call multiple times.
6. **Purchase discount**: Discount is order-level (stored on `purchases.discount`), not per-row. Rendered in the tfoot summary, not as a table column.
7. **Asset pipeline**: Use `@vite()` in Blade; never inline large scripts into layouts — use `@push('scripts')` / `@stack('scripts')`.

## Key Config & Environment

- `config/chatbot.php` — chatbot mode (`local`/`vanna`), Vanna URL
- `ADMIN_CHATBOT_VANNA_ENABLED`, `ADMIN_CHATBOT_VANNA_URL`
- `DB_CONNECTION` — `mysql` (production), `sqlite` (development)
- `QUEUE_CONNECTION=database`, `CACHE_STORE=database`, `SESSION_DRIVER=database`
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` — OAuth

## Resources

- `role-based-login-documentation.md` — complete auth/middleware reference
- `project-methodology-technology.md` — framework/tool selection rationale
