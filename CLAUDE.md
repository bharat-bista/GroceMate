# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

GroceMate is a comprehensive Laravel-based grocery management system supporting multiple independent subsystems:
- **Inventory Management** - Product tracking with batch/FIFO stock management
- **POS Billing System** - Point-of-sale invoicing with payment gateway integration
- **E-commerce Platform** - Online storefront with order management
- **Financial Reporting** - Income, expenses, supplier payments, and business analytics
- **Role-Based Access Control** - Admin, Staff, and Customer roles with granular permissions

## Technology Stack

**Backend**: Laravel 12.x with PHP 8.2+
**Frontend**: Blade templates, Tailwind CSS 4.x, Alpine.js 3.x, Vanilla JavaScript
**Database**: MySQL (primary) / SQLite (development fallback)
**Queue/Session/Cache**: Database driver
**Build Tools**: Vite 7.x, PostCSS
**Additional Libraries**: Socialite (Google OAuth), dompdf (PDF generation), Laravel Telescope (monitoring)

## Essential Commands

### Development Server
```bash
composer run dev                     # Full stack: Laravel, queue, logs, Vite
php artisan serve                    # HTTP server on :8000
php artisan queue:listen --tries=1   # Queue processor
php artisan pail --timeout=0         # Real-time logs
npm run dev                          # Vite dev server
```

### Database & Migrations
```bash
php artisan migrate              # Run pending migrations
php artisan migrate --force      # Force on production
php artisan migrate:rollback     # Undo last batch
php artisan migrate:refresh      # Rollback all + re-run
```

### Testing
```bash
composer run test                   # Run full test suite
php artisan test tests/Unit         # Unit tests only
php artisan test tests/Feature      # Feature tests only
php artisan test --filter=TestName  # Run specific test
php artisan tinker                  # Interactive REPL for quick tests
```

### Code Quality
```bash
vendor/bin/pint                  # Format code (Laravel Pint)
vendor/bin/pint --test           # Check without fixing
php artisan config:clear         # Clear config cache
php artisan cache:clear          # Clear all cache
php artisan view:clear           # Clear view cache
```

### Setup
```bash
composer run setup               # Complete setup from scratch
# Manual: composer install, cp .env.example .env, php artisan key:generate
# Then: php artisan migrate --force, npm install, npm run build
```

### Production Build
```bash
npm run build                    # Compile assets with Vite
php artisan config:cache         # Cache configuration
php artisan route:cache          # Cache routes
php artisan view:cache           # Cache views
```

## Project Structure

### Models (app/Models/)
- **User**: role_id based (1=admin, 2=customer, 3=staff) with helper methods `isAdmin()`, `isStaff()`, `isCustomer()`, `canAccessInventoryPanel()`
- **Core**: Product, Stock, StockBatch, Purchase, PurchaseItem, Business, Supplier
- **POS**: POS/Invoice, POS/InvoiceItem, POS/Customer, POS/Income, POS/Expense
- **Ecommerce**: EcommerceProduct, Order, OrderItem, OrderRefund
- **Support**: Brand, Category, Tax, OtpReset, DeliveryFeeSetting, ContactMessage

Key relationships:
- Product (1:1) Stock (quantity cache), (1:N) StockBatch (FIFO tracking), (1:1) EcommerceProduct
- Business contains all operational data (products, invoices, purchases via foreign keys)

### Controllers (app/Http/Controllers/)
- `frontend/` - Customer pages (home, cart, checkout, order, account management)
- `Inventory/` - Admin/staff product, category, brand, purchase, supplier management
- `POS/` - Admin invoice, customer, income, expense, supplier payment handling
- `Auth/` - Password reset and OTP flows
- Standalone: BusinessController, TaxController, DeliveryFeeSettingController, AdminChatbotController

### Middleware (app/Http/Middleware/)
- `AdminMiddleware` - role_id === 1 only
- `AdminOrStaffMiddleware` - role_id === 1 or 3 (inventory/ecommerce access)
- Registered in `bootstrap/app.php` with `->alias()`

### Services (app/Services/)
- `FifoStockService` - FIFO batch consumption for invoices
- `InvoiceNumberService` - Sequential invoice number generation
- `AdminChatbotService` - Local chatbot logic for business intelligence
- `EcommerceIncomeSyncService` - Sync orders to POS income

### Routes (routes/web.php)

Route groups:
1. **Public** - Login, register, forgot password, home, cart, checkout, product search
2. **Inventory** (`/inventory/*`) - `['auth', 'admin_or_staff']` - Product, purchase, supplier, ecommerce management
3. **POS** (`/pos/*`) - `['auth', 'admin']` only - Invoices, customers, income, expenses, supplier payments
4. **Business & Admin** (`/business/*`, `/admin/accounts/*`, `/taxes/*`) - `['auth', 'admin']` only
5. **Payment Callbacks** - Khalti and eSewa (CSRF exempted in bootstrap/app.php)
6. **Frontend Orders** (`/orders*`) - Customer order viewing

### Views (resources/views/)
- `inventory/` - Admin/staff dashboard, products, purchases, suppliers, ecommerce; layout at `layouts/inventory.blade.php`
- `pos/` - Dashboard, customers, invoices, income, expenses, Khalti/eSewa redirect templates
- `frontend/` - Main site layout, catalog, cart, checkout, account pages, orders, contact
- `emails/` - OTP, invoice, order confirmation, order messages, contact replies

### Database (70+ migrations)
- **Users**: role_id (1/2/3), status (Y/N), google_id (OAuth), business_id
- **Stock Management**: products (selling_price, is_listed), stock (quantity cache), stock_batches (purchase_date, expiry_date, status: active/depleted/expired), purchase_items (batch_no)
- **POS**: invoices, invoice_items, customers, incomes, expenses
- **Ecommerce**: orders, order_items, order_refunds, ecommerce_products, ecommerce_product_images, sliders, delivery_fee_settings
- **Financial**: businesses, suppliers, supplier_payments, taxes
- **Support**: otp_resets (with purpose column), contact_messages

## Role-Based Architecture

**Three User Roles** (role_id):

1. **Admin (role_id = 1)** - Full access to everything; can create/delete admin and staff accounts (except self)
2. **Staff (role_id = 3)** - Inventory and ecommerce only (no POS, no business settings); middleware: `admin_or_staff`
3. **Customer (role_id = 2)** - Public frontend and personal orders only; created on registration with status='N' (unverified)

**Admin Account Creation Flow**: Admin form → OTP sent to new account email → OTP verified → account created. OTP stored in otp_resets with `purpose='managed_account_register'`.

## Key Business Logic

### Stock Management (FIFO)
- **Stock**: Denormalized quantity cache (sum of all batch quantities)
- **StockBatch**: Individual batches with purchase_date, expiry_date, status lifecycle: `active → depleted/expired`
- **Invoice Processing**: `FifoStockService` consumes oldest active batches first when creating POS invoices
- **Ecommerce Reservation**: `EcommerceProduct.ecommerce_stock` reserves inventory from Stock; use `posAvailableStock()` = `availableStock()` - `ecommerceReserved()` for POS

### Multi-Tenancy via Business
- Business is the data container for all operations
- Product, Invoice, Purchase, Income, Expense all have business_id
- Queries **must** filter by business_id for data isolation

### OTP System
- Table: otp_resets with purpose field (`registration`, `password_reset`, `managed_account_register`)
- Validation: unused, not expired, attempts < 5

### Payment Gateways
- **Khalti**: POS supplier payment integration
- **eSewa**: Both POS supplier payment and ecommerce checkout
- Callbacks are CSRF-exempted in bootstrap/app.php: `khalti/verify`, `pos/esewa/*`, `pos/khalti/*`

### Admin Chatbot
- Dashboard-embedded AI assistant; **Local Mode** has hardcoded responses (low stock, expiry, due amounts, analytics)
- **Vanna Mode** (optional): Python sidecar for complex SQL queries
- Config: `config/chatbot.php`; env vars: `ADMIN_CHATBOT_VANNA_ENABLED`, `ADMIN_CHATBOT_VANNA_URL`

## Shared Admin Utilities (S0-3)

Three utilities defined in `resources/views/inventory/partials/admin-utils.blade.php` and included by the inventory layout. Available globally as `window.GroceMate.*` on every admin page.

### `GroceMate.money` — whole-rupee money input
- Add `data-money` to any `<input type="number">` to auto-enforce integer-only entry (blocks `.`, rounds on blur).
- `GroceMate.money.parse('1,500')` → `1500`
- `GroceMate.money.format(1500)` → `'Rs 1,500'`
- `GroceMate.money.init(el)` — re-call after dynamic DOM changes.

### `GroceMate.notify` — single client-side notification
- Replaces bare `alert()` calls. Shows one banner at the top of the content pane; auto-dismisses success after 5 s.
- `GroceMate.notify.success('Saved.')` / `GroceMate.notify.error('Failed.')`

### `GroceMate.formGate` — multi-step form gating
- Disables all line-item row inputs until every header field has a value. Used by purchase entry (S1-5) and POS new sale (S2-5).
```js
const gate = GroceMate.formGate.init({
    watch:    ['select[name="business_id"]', 'select[name="supplier_id"]', 'input[name="invoice_no"]'],
    gate:     '#itemsBody',     // container holding rows
    rowClass: '.purchase-row', // selector for individual rows
    addBtn:   '#addRow',       // optional Add Row button
});
gate.check(); // call after adding a new row dynamically
```

## Important Patterns

1. **Business Isolation**: Always filter queries by `business_id` in admin controllers.
2. **Stock Updates**: Use `FifoStockService` for invoice processing; wrap in `DB::transaction()`.
3. **Transactions**: Wrap multi-step operations (create invoice + update stock + log) in `DB::transaction()`.
4. **Asset Pipeline**: Use `@vite()` in Blade; import CSS/JS through Vite entry points.
5. **Middleware Registration**: Aliases registered in `bootstrap/app.php` via `->alias()`, not in `Kernel.php`.

## Key Config & Environment

- `config/chatbot.php` - Admin chatbot UI and Vanna sidecar settings
- `DB_CONNECTION` - `mysql` for prod, `sqlite` for dev
- `QUEUE_CONNECTION=database`, `CACHE_STORE=database`, `SESSION_DRIVER=database`
- `KHALTI_PUBLIC_KEY` / `KHALTI_SECRET_KEY` - payment integration
- `ESEWA_PRODUCT_CODE` / `ESEWA_SECRET_KEY` / `ESEWA_PAYMENT_URL` / `ESEWA_STATUS_URL` - payment integration
- `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` / `GOOGLE_REDIRECT_URI` - OAuth

## Resources

- **Role-Based System**: See `role-based-login-documentation.md` for complete auth/middleware guide
- **Tech Decisions**: See `project-methodology-technology.md` for framework/tool selection rationale
