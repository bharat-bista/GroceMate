# AJAX and Performance Implementation Report

Date: 2026-04-16
Project: GroceMate

## 1. Objective
This report documents all completed work for:
1. AJAX implementation on the Advanced ecommerce page.
2. UI fixes related to filter interactions.
3. Performance optimization attempts across Home, Advanced, Description, Checkout, and Cart pages.
4. Current measured performance state and next recommended actions.

## 2. Summary of What Was Implemented

### 2.1 Advanced page AJAX filtering and pagination
Completed:
1. Replaced full-page reload filtering with AJAX partial refresh.
2. Added AJAX pagination handling.
3. Kept URL query state synchronized with browser history.
4. Kept price slider integrated with AJAX filtering.
5. Kept full fallback navigation if AJAX fails.

Main files:
1. [app/Http/Controllers/frontend/AdvancedController.php](../app/Http/Controllers/frontend/AdvancedController.php)
2. [resources/views/frontend/advanced/index.blade.php](../resources/views/frontend/advanced/index.blade.php)
3. [resources/views/frontend/advanced/partials/product-results.blade.php](../resources/views/frontend/advanced/partials/product-results.blade.php)

### 2.2 Suggestion UI cleanup
Completed:
1. Removed search suggestion dropdown UI and logic from Advanced page.
2. Preserved search/filter AJAX behavior.

Main file:
1. [resources/views/frontend/advanced/index.blade.php](../resources/views/frontend/advanced/index.blade.php)

### 2.3 Category and slider bug fixes
Completed:
1. Fixed broken JS structure on Advanced page (root cause of filter and slider issues).
2. Restored and verified slider initialization.
3. Improved category query parsing support for:
   1. categories=1&categories=2
   2. categories[]=1
   3. categories[0]=1

Main files:
1. [resources/views/frontend/advanced/index.blade.php](../resources/views/frontend/advanced/index.blade.php)
2. [app/Http/Controllers/frontend/AdvancedController.php](../app/Http/Controllers/frontend/AdvancedController.php)

### 2.4 Query and payload optimizations
Completed:
1. Standardized storefront filters via model scope.
2. Reduced selected columns in key ecommerce listing queries.
3. Replaced some nested relation checks with direct foreign-key checks.
4. Limited Home page brands and categories list size.
5. Simplified Home featured product query logic to reduce PHP-side iteration overhead.

Main files:
1. [app/Models/EcommerceProduct.php](../app/Models/EcommerceProduct.php)
2. [app/Http/Controllers/Frontend/HomeController.php](../app/Http/Controllers/Frontend/HomeController.php)
3. [app/Http/Controllers/frontend/AdvancedController.php](../app/Http/Controllers/frontend/AdvancedController.php)
4. [app/Http/Controllers/frontend/DescriptionController.php](../app/Http/Controllers/frontend/DescriptionController.php)
5. [app/Providers/AppServiceProvider.php](../app/Providers/AppServiceProvider.php)

### 2.5 Frontend perceived-speed improvements
Completed:
1. Added lazy loading and async decoding for many non-critical images on:
   1. Advanced results cards
   2. Description page recommendation/gallery thumbnails
   3. Home page product, promo, category, and brand images

Main files:
1. [resources/views/frontend/advanced/partials/product-results.blade.php](../resources/views/frontend/advanced/partials/product-results.blade.php)
2. [resources/views/frontend/description/index.blade.php](../resources/views/frontend/description/index.blade.php)
3. [resources/views/frontend/home/index.blade.php](../resources/views/frontend/home/index.blade.php)

## 3. Routing and Page Connectivity
Relevant routes:
1. Home: [routes/web.php](../routes/web.php)
2. Advanced: [routes/web.php](../routes/web.php)
3. Description: [routes/web.php](../routes/web.php)
4. Checkout: [routes/web.php](../routes/web.php)
5. Cart: [routes/web.php](../routes/web.php)

Primary controller connections:
1. Home page data assembly: [app/Http/Controllers/Frontend/HomeController.php](../app/Http/Controllers/Frontend/HomeController.php)
2. Advanced filters and AJAX JSON response: [app/Http/Controllers/frontend/AdvancedController.php](../app/Http/Controllers/frontend/AdvancedController.php)
3. Description product and recommendation logic: [app/Http/Controllers/frontend/DescriptionController.php](../app/Http/Controllers/frontend/DescriptionController.php)
4. Header category composer: [app/Providers/AppServiceProvider.php](../app/Providers/AppServiceProvider.php)

## 4. Advanced AJAX Flow (How It Works)

### 4.1 Request flow
1. User changes search input, brand select, category checkbox, or price slider.
2. Frontend script serializes filter form into query params.
3. Frontend sends fetch request to Advanced route with AJAX headers.
4. Controller applies filters and pagination.
5. Controller returns JSON payload containing rendered HTML partial and total count.
6. Frontend replaces only results container and updates count.
7. Frontend updates URL via pushState.
8. Browser back/forward triggers popstate and reloads matching filtered results via AJAX.

### 4.2 Files involved in the flow
1. AJAX script and event bindings: [resources/views/frontend/advanced/index.blade.php](../resources/views/frontend/advanced/index.blade.php)
2. Filter query and AJAX branch: [app/Http/Controllers/frontend/AdvancedController.php](../app/Http/Controllers/frontend/AdvancedController.php)
3. Partial-rendered product grid and pagination: [resources/views/frontend/advanced/partials/product-results.blade.php](../resources/views/frontend/advanced/partials/product-results.blade.php)

## 5. Stability and Data Rules Added
1. `storefrontVisible` scope ensures products are in stock and ecommerce stock > 0.
2. `booted` saving hook in ecommerce product auto-syncs status with stock.
3. Description page blocks out-of-stock selected product with a 404.
4. Advanced category parser now supports multiple array key formats.

File reference:
1. [app/Models/EcommerceProduct.php](../app/Models/EcommerceProduct.php)
2. [app/Http/Controllers/frontend/DescriptionController.php](../app/Http/Controllers/frontend/DescriptionController.php)
3. [app/Http/Controllers/frontend/AdvancedController.php](../app/Http/Controllers/frontend/AdvancedController.php)

## 6. Benchmarking Method Used
Benchmark command pattern:
1. Local HTTP requests via curl.
2. Multiple samples per page.
3. First request treated as cold hit.
4. Warm average computed from subsequent samples.

Pages measured:
1. Home
2. Advanced
3. Description
4. Checkout
5. Cart

## 7. Latest Measured Results
Warm performance snapshot (latest run):
1. Home: 1454.44 ms
2. Advanced: 582.68 ms
3. Description: 584.68 ms
4. Checkout: 557.10 ms
5. Cart: 519.57 ms

Interpretation:
1. Home remains the major bottleneck.
2. Advanced and Description are in a similar range.
3. Checkout and Cart are faster than Home but still above ideal for a local optimized stack.

## 8. What Was Tried and Why

### 8.1 Cache-based optimizations
Status:
1. Tested and then reduced/removed broad cache-remember usage on request paths.
2. Reason: environment uses `CACHE_STORE=database`, which can add DB overhead for frequent cache reads/writes.

### 8.2 Query simplification
Status:
1. Kept practical query-level optimizations that reduce payload and relation cost.
2. Maintained functional behavior while reducing unnecessary fields and broad loops.

## 9. Current End-State by Page

### 9.1 Advanced page
State:
1. AJAX filter and pagination fully implemented.
2. Price slider and category filters working.
3. Suggestion dropdown intentionally removed.

### 9.2 Home page
State:
1. Optimized compared to older logic, but still the slowest endpoint.
2. Heavy data assembly and page composition remain the main latency source.

### 9.3 Description page
State:
1. Query logic tightened for recommendations.
2. Image lazy-loading applied for non-critical images.

### 9.4 Checkout and Cart pages
State:
1. No major logic complexity in page entry methods.
2. Still affected by global app/render overhead and shared frontend layout cost.

## 10. Remaining High-Impact Next Steps
Recommended next implementation order:
1. Add targeted DB indexes for ecommerce access patterns:
   1. `ecommerce_products` fields used by status/stock/product/sorting filters.
   2. `products` fields used by category/brand/business joins.
   3. `sliders` fields used by active/type/slot/sort filtering.
2. Run SQL EXPLAIN on Home and Advanced core queries and tune based on actual plans.
3. Evaluate local stack tuning:
   1. MySQL buffer and query settings.
   2. PHP OPcache checks.
   3. Ensure no debug profiler overhead.

## 11. Validation Checklist
Completed checks:
1. No editor diagnostics in changed files for key implemented changes.
2. Functional checks performed for:
   1. Advanced AJAX refresh
   2. Category filtering
   3. Slider-driven filtering
   4. Pagination via AJAX
3. Repeated timing snapshots captured after each optimization stage.

## 12. Final Notes
1. The AJAX architecture for Advanced page is complete and stable.
2. The biggest unresolved speed problem is still Home page response time.
3. The next major gain will likely come from DB indexing and query-plan tuning, not frontend JavaScript changes.
