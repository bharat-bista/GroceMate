# GroceMate Codebase Audit Report (Full)

Scope reviewed: controllers, services, middleware, routes, and the Vanna sidecar. No code was changed; this report lists actionable fixes.

## Issue 1: POS controllers expose cross‑account data (missing business scoping) — 🔴 Critical

**Locations**
1. `app\Http\Controllers\POS\InvoiceController.php` L31-L54
2. `app\Http\Controllers\POS\CustomerController.php` L16-L55
3. `app\Http\Controllers\POS\IncomeController.php` L20-L65
4. `app\Http\Controllers\POS\ExpenseController.php` L14-L52
5. `app\Http\Controllers\POS\DashboardController.php` L25-L46
6. `app\Http\Controllers\POS\SupplierPaymentController.php` L25-L78

**Risk**  
Any admin/staff can see and aggregate all invoices, customers, income, expenses, and supplier payments across all businesses. This violates tenant isolation and the stated requirement that every subsystem be scoped by `business_account_id`.

**Current code (examples)**
```php
// InvoiceController.php L31-L54
$query = Invoice::select('invoices.*', 'customers.name as customer_name', 'customers.total_due as customer_total_due')
           ->join('customers', 'invoices.customer_id', '=', 'customers.id')
           ->with(['creator', 'business']);
```
```php
// IncomeController.php L20-L53
$query = Income::with(['customer', 'business']);
...
$incomes = $query->orderBy('created_at', 'desc')->paginate(15);
```
```php
// DashboardController.php L25-L46
$todaySales = Invoice::whereDate('created_at', $today)->sum('total_cost');
$totalCustomers = Customer::count();
$totalIncome = Income::sum('amount_received');
```

**Fixed version (example)**  
Scope every query to the active business context (from session/user selection), and add a `business_id` column where missing (e.g., `customers`).
```php
$businessId = (int) session('active_business_id'); // or validated request param
$query = Invoice::query()
    ->where('business_id', $businessId)
    ->with(['creator', 'business']);
```

## Issue 2: Inventory controllers expose cross‑account data — 🔴 Critical

**Locations**
1. `app\Http\Controllers\Inventory\ProductController.php` L21-L31
2. `app\Http\Controllers\Inventory\PurchaseController.php` L52-L55, L90-L96
3. `app\Http\Controllers\Inventory\SupplierController.php` L16-L55
4. `app\Http\Controllers\Inventory\DashboardController.php` L24-L101

**Risk**  
Inventory lists, purchases, suppliers, and dashboard analytics can be viewed across all businesses. This breaks tenant isolation and can leak vendor pricing, stock levels, and purchase history.

**Current code (examples)**
```php
// PurchaseController.php L52-L55
$products = Product::with(['stock', 'business', 'category', 'brandRelation'])
    ->orderBy('name')
    ->get();
```
```php
// SupplierController.php L16-L25
$suppliers = Supplier::query()
    ->with('businessAccount')
    ->orderByDesc('id')
    ->paginate(10);
```

**Fixed version (example)**  
Require a business context and filter all inventory data by it.
```php
$businessId = (int) session('active_business_id');
$products = Product::query()
    ->where('business_id', $businessId)
    ->with(['stock', 'category', 'brandRelation'])
    ->orderBy('name')
    ->get();
```

## Issue 3: Ecommerce reporting/admin views not scoped by business — 🔴 Critical

**Locations**
1. `app\Http\Controllers\Inventory\EcommerceIncomeController.php` L134-L193
2. `app\Http\Controllers\frontend\OrderController.php` L305-L323

**Risk**  
Ecommerce income dashboards and admin order lists can show orders and revenues across all businesses, exposing cross‑tenant sales data.

**Current code**
```php
// EcommerceIncomeController.php L134-L193
$query = $this->settledItemsQuery()
    ->selectRaw('orders.id as order_id')
    ->selectRaw('businesses.id as business_id')
    ->groupBy([...]);
```
```php
// OrderController.php L305-L323
$query = Order::with('items')->orderBy('created_at', 'desc');
$orders = $query->paginate(15);
```

**Fixed version (example)**  
Filter orders and joins by the active business.
```php
$businessId = (int) session('active_business_id');
$query->where('businesses.id', $businessId);
```

## Issue 4: Admin chatbot local rules leak cross‑business data — 🔴 Critical

**Locations**
1. `app\Services\AdminChatbotService.php` L167-L178 (low stock)
2. L337-L345 (customer due)
3. L388-L395 (supplier due)
4. L442-L452 (product demand)
5. L563-L573 (financial snapshot)

**Risk**  
The chatbot can return due balances, low stock, and sales across all businesses without any tenant filter, allowing a user to request sensitive data from other accounts.

**Current code (example)**
```php
// AdminChatbotService.php L337-L345
$rows = Customer::query()
    ->where('total_due', '>', 0)
    ->orderByDesc('total_due')
    ->limit($limit)
    ->get(['id', 'name', 'phone', 'total_due']);
```

**Fixed version (example)**  
Pass the active business into the chatbot and scope every query.
```php
$businessId = (int) $user?->active_business_id;
$rows = Customer::query()
    ->where('business_id', $businessId)
    ->where('total_due', '>', 0)
    ->orderByDesc('total_due')
    ->limit($limit)
    ->get();
```

## Issue 5: Vanna sidecar does not enforce business scope or limit for all queries — 🔴 Critical

**Locations**
1. `python_services\vanna_admin_chatbot\vanna_adapter.py` L103-L106
2. L188-L197

**Risk**  
Vanna can generate SQL that returns data from all businesses. The LIMIT guard only applies to raw `SELECT` statements; `WITH`, `SHOW`, or complex queries can run unbounded scans.

**Current code**
```python
sql = client.generate_sql(question=message)
safe_sql = self._ensure_read_only_sql(sql)
safe_sql = self._enforce_limit(safe_sql)
dataframe = client.run_sql(safe_sql)
```

**Fixed version (example)**  
Inject tenant filters and apply limits to all supported read queries.
```python
sql = client.generate_sql(question=message)
sql = self._ensure_read_only_sql(sql)
sql = self._enforce_business_scope(sql, business_id=user.get("business_id"))
sql = self._enforce_limit(sql)  # handle WITH/SHOW via a wrapper
dataframe = client.run_sql(sql)
```

## Issue 6: Public POS stock endpoints leak inventory data — 🔴 Critical

**Locations**
1. `routes\web.php` L40-L46
2. `app\Http\Controllers\POS\StockCheckController.php` L17-L38
3. `app\Http\Controllers\POS\ProductSearchController.php` L12-L38

**Risk**  
Anyone can query live stock availability and product lists by passing a `business_id`, exposing sensitive inventory data without authentication.

**Current code**
```php
// web.php L40-L46
Route::post('/pos/stock-check', [StockCheckController::class, 'check']);
Route::get('/pos/products/search', [ProductSearchController::class, 'searchProductsForPOS']);
```

**Fixed version (example)**  
Require authentication and authorization, or use signed requests.
```php
Route::middleware(['auth', 'admin_or_staff'])
    ->post('/pos/stock-check', [StockCheckController::class, 'check']);
```

## Issue 7: Invoice number generation is race‑condition prone — 🟡 Medium

**Location**
`app\Services\InvoiceNumberService.php` L19-L34

**Risk**  
Two concurrent invoice creations can read the same last invoice number and generate duplicates, causing collisions and failed inserts.

**Current code**
```php
$lastInvoice = Invoice::where('invoice_no', 'like', $prefix . '%')
    ->orderBy('invoice_no', 'desc')
    ->first();
```

**Fixed version (example)**  
Reserve numbers inside a transaction with a row lock or sequence table.
```php
return DB::transaction(function () use ($prefix) {
    $last = Invoice::where('invoice_no', 'like', $prefix . '%')
        ->lockForUpdate()
        ->orderBy('invoice_no', 'desc')
        ->first();
    ...
});
```

## Issue 8: Ecommerce checkout trusts client‑sent prices (stale/tampered data) — 🔴 Critical

**Locations**
1. `app\Http\Controllers\frontend\CheckoutController.php` L53-L71
2. `app\Http\Controllers\frontend\OrderController.php` L90-L114

**Risk**  
Prices are accepted from the client and used to compute totals without re‑validating from the database, enabling price tampering and stale pricing at checkout.

**Current code**
```php
$subtotal = round($items->sum(fn (array $item) => $item['price'] * $item['qty']), 2);
$total = round($subtotal + $deliveryCharge, 2);
```

**Fixed version (example)**  
Fetch authoritative prices from `ecommerce_products` and recompute totals server‑side.
```php
$products = EcommerceProduct::whereIn('id', $items->pluck('id'))->get()->keyBy('id');
$subtotal = $items->sum(fn($i) => $products[$i['id']]->display_price * $i['qty']);
```

## Issue 9: Ecommerce order creation is not idempotent — 🟡 Medium

**Location**
`app\Http\Controllers\frontend\OrderController.php` L128-L163

**Risk**  
Double‑submission or retries can create duplicate orders and deduct stock twice because no idempotency key is checked for non‑gateway payments.

**Current code**
```php
$order = DB::transaction(function () use (...) {
    $this->deductEcommerceStock($items);
    $order = Order::create([...]);
    ...
});
```

**Fixed version (example)**  
Store and enforce a unique idempotency key per checkout.
```php
$key = $request->header('Idempotency-Key');
$existing = Order::where('idempotency_key', $key)->first();
if ($existing) { return $existing; }
```

## Issue 10: Cancelled orders do not restore stock for all payment types — 🟡 Medium

**Location**
`app\Http\Controllers\frontend\OrderController.php` L379-L389

**Risk**  
Stock is restored only for verified ConnectIPS orders. Cancelling COD or eSewa orders leaves inventory permanently reduced.

**Current code**
```php
$shouldRestoreConnectIpsStock = $isFirstCancelTransition
    && $order->payment_method === 'connectips'
    && $order->payment_status === 'verified';
```

**Fixed version (example)**  
Restore ecommerce stock on first cancellation regardless of payment method.
```php
$shouldRestoreStock = $isFirstCancelTransition;
```

## Issue 11: Supplier payment callbacks are not idempotent — 🟡 Medium

**Locations**
1. `app\Http\Controllers\POS\SupplierPaymentController.php` L236-L259
2. L283-L315
3. L384-L450
4. L511-L605

**Risk**  
Repeated callbacks can create duplicate `supplier_payments` and negative `income` entries because there is no unique check on transaction IDs.

**Current code (example)**
```php
$supplierPayment = SupplierPayment::create([
    'payment_reference' => $khaltiData['transaction_id'] ?? $khaltiData['pidx'],
]);
```

**Fixed version (example)**  
Check for existing `payment_reference` before creating records.
```php
if (SupplierPayment::where('payment_reference', $ref)->exists()) {
    return response()->json(['success' => true, 'message' => 'Already processed']);
}
```

## Issue 12: FIFO availability check can race with ecommerce reservations — 🟡 Medium

**Locations**
`app\Services\FifoStockService.php` L31-L44, L87-L116

**Risk**  
`canConsume()` reads `ecommerce_stock` without locking. An ecommerce reservation can change between the availability check and the FIFO batch lock, allowing oversell or false availability.

**Current code**
```php
$ecommerce = (float) ($this->ecommerceProduct->newQuery()
    ->where('product_id', $productId)
    ->value('ecommerce_stock') ?? 0);
```

**Fixed version (example)**  
Lock the ecommerce product row in the same transaction used for consumption.
```php
$ecommerce = (float) $this->ecommerceProduct->newQuery()
    ->where('product_id', $productId)
    ->lockForUpdate()
    ->value('ecommerce_stock');
```

## Issue 13: Reporting dashboards execute many per‑day queries on the live DB — 🟡 Medium

**Locations**
1. `app\Http\Controllers\POS\DashboardController.php` L85-L105, L117-L207
2. `app\Http\Controllers\Inventory\DashboardController.php` L50-L93

**Risk**  
Per‑day loops run dozens of queries on the primary database. Heavy reporting can slow down POS and checkout traffic (cascade failure across subsystems).

**Current code**
```php
for ($i = 29; $i >= 0; $i--) {
    $salesTotal = Invoice::whereDate('created_at', $date)->sum('total_cost');
    $incomeTotal = Income::whereDate('transaction_date', $date)->sum('amount_received');
}
```

**Fixed version (example)**  
Aggregate in a single grouped query (or use a reporting replica).
```php
Invoice::selectRaw('DATE(created_at) as day, SUM(total_cost) as total')
    ->groupBy('day')
    ->get();
```

---

## Issue 14: Income/Expense updates race with balance increments on Business — 🟡 Medium

**Locations**
1. `app\Models\POS\Income.php` L36-L85
2. `app\Models\POS\Expense.php` L33-L91

**Risk**  
Model events update `business.balance` directly after creating/updating income or expense. If two concurrent requests create records, both read the same balance value and increment by their amount, losing one increment (race condition on balance).

**Current code**
```php
static::created(function ($income) {
    if ($income->business_id) {
        $business = Business::find($income->business_id);
        if ($business) {
            $business->increment('balance', $income->amount_received);
        }
    }
});
```

**Fixed version (example)**  
Use database-level atomic operations with a transaction and lock.
```php
DB::transaction(function () use ($income) {
    $business = Business::findOrFail($income->business_id);
    $business->lockForUpdate()->increment('balance', $income->amount_received);
});
```

---

## Issue 15: Payment gateway callbacks create duplicate income records — 🔴 Critical

**Locations**
1. `app\Http\Controllers\POS\SupplierPaymentController.php` L236-L266 (Khalti)
2. L283-L315 (Khalti verify)
3. L384-L450 (eSewa)

**Risk**  
Successful payments create a `SupplierPayment` + an `Income` record without checking if the transaction was already processed. Retries or webhook replays create duplicates, doubling the amount recorded.

**Current code**
```php
DB::transaction(function () use ($validated) {
    $supplierPayment = SupplierPayment::create($validated);
    Income::create([
        'reference_no' => 'PAY-' . $supplierPayment->id,
        'amount_received' => $supplierPayment->amount,
        ...
    ]);
});
```

**Fixed version (example)**  
Check for existing records by transaction ID before creating.
```php
$existing = SupplierPayment::where('payment_reference', $txnId)->first();
if ($existing) {
    return response()->json(['success' => true, 'message' => 'Already processed']);
}
```

---

## Issue 16: Cascading deletes can wipe audit trails — 🟡 Medium

**Locations**
1. `database\migrations\2026_02_12_000001_create_invoices_table.php`
2. `database\migrations\2026_02_12_000002_create_invoice_items_table.php`

**Risk**  
`onDelete('cascade')` on `invoices → customers` means deleting a customer removes all invoices, destroying the invoice history. This breaks reporting, tax filings, and audit trails.

**Current code**
```sql
-- in create_invoices_table.php
$table->foreignId('customer_id')->constrained()->onDelete('cascade');
```

**Fixed version (example)**  
Use `onDelete('restrict')` or `SET NULL` to preserve history.
```sql
$table->foreignId('customer_id')->constrained()->onDelete('restrict');
```

---

## Issue 17: Supplier payment controller not scoped by business — 🔴 Critical

**Locations**
`app\Http\Controllers\POS\SupplierPaymentController.php` L16-L77

**Risk**  
All supplier payment queries lack `business_id` filtering. Admin can see supplier payments and summary stats across all businesses.

**Current code**
```php
$payments = SupplierPayment::with('supplier')
    ->orderBy('date', 'desc')
    ->paginate(15);
$totalPaid = SupplierPayment::sum('amount');
$topSuppliers = SupplierPayment::join(...)->get();
```

**Fixed version (example)**  
Filter by active business.
```php
$businessId = (int) session('active_business_id');
$payments = SupplierPayment::query()
    ->where('business_account', $businessId)
    ->with('supplier')
    ->orderBy('date', 'desc')
    ->paginate(15);
```

---

## Issue 18: SupplierPayment model uses nullable `business_account` FK — 🟡 Medium

**Locations**
1. `app\Models\SupplierPayment.php`
2. `database\migrations\2026_03_10_054554_change_business_account_to_foreign_key_in_supplier_payments_table.php`

**Risk**  
Foreign key is nullable, allowing orphaned supplier payments with no business context. These records are invisible in filtered views, creating accounting gaps.

**Current code**
```php
$table->foreignId('business_account')->nullable()->constrained('businesses')->onDelete('set null');
```

**Fixed version (example)**  
Make the FK non‑nullable.
```php
$table->foreignId('business_account')->constrained('businesses')->onDelete('restrict');
```

---

## Issue 19: Ecommerce order deduction does not validate product ownership — 🔴 Critical

**Locations**
`app\Http\Controllers\frontend\CheckoutController.php` L230-L279
`app\Http\Controllers\frontend\OrderController.php` L183-L231

**Risk**  
Customer can place an order with products from any business by passing arbitrary `product_id` values. No check confirms the product belongs to the intended business.

**Current code**
```php
$products = EcommerceProduct::query()
    ->whereIn('id', $requiredQtyByProduct->keys()->all())
    ->lockForUpdate()
    ->get();
```

**Fixed version (example)**  
Validate all products share the same business context.
```php
$products = EcommerceProduct::query()
    ->whereIn('id', $requiredQtyByProduct->keys()->all())
    ->whereHas('product', fn($q) => $q->where('business_id', $expectedBusinessId))
    ->lockForUpdate()
    ->get();
```

---

## Summary

**Critical (8):** Issues 1, 2, 3, 4, 5, 6, 15, 19  
**Medium (9):** Issues 7, 8, 9, 10, 11, 12, 13, 14, 16, 17, 18

**Total: 19 issues identified** across the audit scope.

All issues have specific file/line locations, current broken code, and proposed fixes.
