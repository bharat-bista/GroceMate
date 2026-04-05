# Dynamic Advanced Search Viva Guide

## 1. What was the goal?
The goal was to make the Advanced page fully dynamic so products, categories, brands, and price filters are loaded from the backend database instead of static sample JavaScript data.

## 2. Which files are connected?
- Route: `routes/web.php` (`advanced` route)
- Controller: `app/Http/Controllers/frontend/AdvancedController.php`
- Advanced page view: `resources/views/frontend/advanced/index.blade.php`
- Header category strip (redirect source): `resources/views/frontend/layouts/header.blade.php`
- Header category data provider: `app/Providers/AppServiceProvider.php`
- Home page category/brand shortcuts: `resources/views/frontend/home/index.blade.php`

## 3. Route layer (entry point)
Advanced page uses GET request and query parameters:

```php
Route::get('/advanced', [AdvancedController::class, 'advanced'])->name('advanced');
```

Filters are passed in URL query like:
- `q=rice`
- `brand_id=3`
- `categories[]=5`
- `min_price=120`
- `max_price=900`

Because it is GET-based:
- URL is shareable/bookmarkable
- browser back/forward works naturally
- pagination keeps filters with `withQueryString()`

## 4. How frontend connects to backend
User interactions submit GET filters to the same route:
- Search button / Enter key
- Brand/company dropdown change
- Category checkbox change
- Price slider change

Also:
- Header category click redirects directly to `advanced?categories[]=<id>`
- Home category click redirects directly to `advanced?categories[]=<id>`
- Home brand click redirects to `advanced?brand_id=<id>`

So category or brand shortcuts pre-filter results immediately.

## 5. Controller flow (data retrieval logic)
Inside `AdvancedController@advanced(Request $request)`:

### Step A: Build base product scope
- Starts from `EcommerceProduct` records
- Keeps only latest row per `product_id` using grouped max id
- Keeps `status = in_stock`
- Ensures product has valid category and brand relation

Why latest-per-product logic?
- avoids repeated cards if historical rows exist for same product

### Step B: Calculate dynamic price bounds
- Calculates min and max from current available product scope
- Uses:
  - `COALESCE(NULLIF(display_price, 0), mrp)`
- This means if `display_price` is 0 or null, it safely uses `mrp`

### Step C: Read and sanitize filters
- `q` as trimmed text
- `brand_id` cast to integer
- `categories[]` cast to integer list, deduplicated
- `min_price` and `max_price` from request
- clamps selected range inside available range

### Step D: Apply filters to query
- Name filter via `whereHas('product', name like %q%)`
- Brand filter via `whereHas('product', brand_id)`
- Category filter via `whereHas('product', whereIn category_id)`
- Price range filter via calculated price expression
- Sorted by discount desc, then latest
- Paginated with query persistence

### Step E: Load filter options for UI
- Loads available brands with in-stock ecommerce products
- Loads available categories with in-stock ecommerce products

## 6. Blade rendering (dynamic UI)
Advanced Blade now renders:
- Dynamic categories list (checkboxes)
- Dynamic brand/company select options
- Dynamic product cards from `$ecommerceProducts`
- Dynamic prices, old prices, discount badges
- Dynamic links to description page:

```php
route('description', $ecommerceProduct->id)
```

No hardcoded demo array is used anymore.

## 7. noUiSlider and form behavior
The page uses hidden fields:
- `min_price`
- `max_price`

JS initializes slider from backend values and updates hidden inputs on change.
When slider stops (`change` event), form submits automatically.

This keeps:
- visible slider UX
- server-side query source of truth

## 8. Category redirect behavior (requested flow)
Category strip links now use:

```php
route('advanced', ['categories' => [$categoryId]])
```

Result:
- user clicks category
- Advanced opens
- that category checkbox is auto-selected
- only matching products are shown

This behavior is implemented for:
- desktop header category strip
- mobile drawer category links
- home top category cards

## 9. Why this is truly dynamic
If admin changes backend data:
- add/remove category -> appears/disappears in filters
- add/remove brand -> appears/disappears in company dropdown
- change product price -> slider bounds and results update
- change stock status -> products auto included/excluded

No manual frontend list update is needed.

## 10. Database tables used in this feature
- `ecommerce_products` (listing, prices, discount, stock state)
- `products` (product name, brand/category relations)
- `categories` (filter options)
- `brands` (filter options)

## 11. Viva architecture one-liner
Advanced Search is a server-driven Laravel MVC feature where GET query parameters are transformed into Eloquent filters, and Blade renders dynamic filter controls plus paginated product results from live database data.

---

## Viva Q and A (Ready to Speak)

### Q1. What changed in Advanced page?
It changed from static sample JavaScript products to fully database-driven search and filtering.

### Q2. Which design pattern is used?
MVC: Route -> Controller query logic -> Blade rendering.

### Q3. Is filtering client-side or server-side?
Server-side filtering with GET parameters.

### Q4. Why use GET for filters?
Because URLs become shareable, bookmarkable, and pagination-friendly.

### Q5. Which filters are supported?
Search text, brand/company, categories, minimum price, maximum price.

### Q6. How are categories preselected from header click?
Header link sends `categories[]=id` in query string; Blade checks and marks checkbox as selected.

### Q7. How does brand filter work?
Brand dropdown sends `brand_id`, controller filters using product relation.

### Q8. How does search by product name work?
Controller applies `whereHas(product)` with `name like %query%`.

### Q9. How does price range work?
Price slider updates hidden min/max inputs, then submits form; backend applies range filter.

### Q10. Why use `COALESCE(NULLIF(display_price, 0), mrp)`?
To avoid broken filtering when `display_price` is zero or missing.

### Q11. How are duplicate product cards prevented?
Controller base scope keeps latest ecommerce row per `product_id`.

### Q12. How are filter dropdown options loaded?
From DB using `whereHas(ecommerceProducts)` so only valid in-stock options appear.

### Q13. How does pagination keep filters?
`paginate(...)->withQueryString()` preserves current query parameters.

### Q14. Where does product card data come from?
From `$ecommerceProducts` collection in Blade loop.

### Q15. How does category click from home page work?
Home category links now send `categories[]=id` to Advanced route.

### Q16. How does mobile category drawer behave?
It uses the same dynamic category data and same redirect query format.

### Q17. What is the scalability benefit?
New categories/brands/products are automatically reflected without frontend hardcoding.

### Q18. Final short viva summary
Advanced Search is now a fully dynamic, backend-driven filtering module that accepts GET filters, executes relational Eloquent queries, and renders live paginated results with auto-selected filters from category redirects.
