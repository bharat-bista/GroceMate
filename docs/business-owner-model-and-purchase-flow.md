# Business Owner Model and Purchase Flow

## Purpose
This document explains the multi-business ownership model that was added to GroceMate, how it affects inventory, ecommerce, and purchase stock-in, and how to explain the implementation in a viva.

## 1. Problem Before the Change
Originally, products, purchases, and ecommerce entries were not clearly tied to a business account. That caused confusion in several places:

- It was unclear which business owned a product.
- Profit and loss could not be assigned cleanly to one business.
- Ecommerce products could show inventory-only items that should not appear online.
- Purchase stock-in could accidentally reuse a product under the wrong business.
- Reports and exports could mix data from multiple businesses.

## 2. Solution Chosen
A single-owner model was implemented.

### Core rule
Each product belongs to exactly one business account.

### Why this is useful
- Ownership is clear.
- Reporting becomes easier.
- Profit/loss can later be traced to the correct business.
- Ecommerce and inventory can be filtered by business.
- Duplicate checks become predictable.

## 3. Main Database Change
A `business_id` column was added to the `products` table.

### Migration
- `database/migrations/2026_04_04_120000_add_business_id_to_products_table.php`

### What it does
- Adds `business_id` as a foreign key to `businesses`.
- Allows old records to remain valid by keeping the column nullable.
- Adds an index on `business_id`, `name`, and `brand_id` for fast lookup.

### Why nullable at first
This avoids breaking existing data while you gradually assign businesses to old products.

## 4. Model Changes

### Product model
File: `app/Models/Product.php`

Added:
- `business_id` to `$fillable`
- `business()` relationship

This allows each product to point to its owning business.

### Business model
File: `app/Models/Business.php`

Added:
- `products()` relationship

This allows a business to list all of its products.

## 5. Inventory Product Logic
File: `app/Http/Controllers/Inventory/ProductController.php`

### What changed
1. `business_id` is now required when creating or updating a product.
2. Product names are normalized before duplicate checking.
3. Brand/company is resolved from either existing brand or new typed brand.
4. Duplicate protection blocks:
   - same product name + same brand inside the same business
   - same product name + same brand in a different business
5. Products can still have the same name if the brand/company is different.

### Normalization logic
Product names are cleaned before comparison:
- extra spaces are removed
- case-insensitive comparison is used

This prevents accidental duplicates like:
- `Chini 25 Kg`
- `chini 25 kg`
- `Chini   25 Kg`

### Why this rule is important
It keeps one business from accidentally reusing another business’s product identity.

## 6. Inventory Product UI Changes
### Product create form
File: `resources/views/inventory/products/create.blade.php`

Added:
- Business account dropdown
- Validation messages for `business_id`, `name`, `brand_id`, and `brand_name`

### Product edit form
File: `resources/views/inventory/products/edit.blade.php`

Added:
- Business account dropdown
- Validation messages

### Product list page
File: `resources/views/inventory/products/index.blade.php`

Added:
- Business filter dropdown
- Business column in the table

This helps admins quickly see which business owns each product.

## 7. Ecommerce Product Logic
File: `app/Http/Controllers/Inventory/EcommerceProductController.php`

### What changed
1. Ecommerce product list now supports filtering by business.
2. Ecommerce create page now supports filtering products by business.
3. Ecommerce list and create views now load business data.
4. The ecommerce product table now shows the owning business.

### Why this matters
Only products from the selected business should appear when adding ecommerce products. That prevents cross-business confusion.

## 8. Ecommerce Product UI Changes
### Ecommerce product list
File: `resources/views/frontend/product/index.blade.php`

Added:
- Business filter dropdown
- Business column
- Context-aware Add Product link

### Ecommerce create page
File: `resources/views/frontend/product/create.blade.php`

Added:
- Business filter selector
- Product dropdown scoped to selected business
- JavaScript filtering so the list of selectable products changes live when the business changes

## 9. Purchase Stock-In Logic
File: `app/Http/Controllers/Inventory/PurchaseController.php`

### What changed
1. Purchase records are tied to a business.
2. New products created from purchase stock-in are assigned to that business.
3. Purchase autocomplete is scoped by selected business.
4. The fallback autocomplete search also respects business selection.

### Why this fixed your issue
You found this case:
- `Chini 25 Kg` existed first under Bista Store.
- When trying to add it again under Sandesh Store, the app was still binding the earlier product/company.

The fix ensures:
- the purchase screen uses the selected business as the source of truth
- a new product created from stock-in gets the chosen business_id
- autocomplete only searches within that business’s purchase history

### Practical rule now
If Sandesh Store is selected, the purchase form should only suggest Sandesh-owned product history.
If a new stock-in creates a new product, it is stored under Sandesh Store automatically.

## 10. Purchase UI Changes
File: `resources/views/inventory/purchases/create.blade.php`

Added / updated:
- Business selection before item entry
- Business-aware product search
- Business-aware fallback search
- Company/brand display remains editable when needed

This means the user must pick the business account first, then add items.

## 11. Purchase Reports and Exports
### Purchase index
File: `resources/views/inventory/purchases/index.blade.php`

Added:
- Business filter
- Business column in the table
- Export URLs now preserve business selection

### PDF export
File: `resources/views/inventory/purchases/export-pdf.blade.php`

Added:
- Business name shown in report header if filtered

### Excel export
File: `resources/views/inventory/purchases/export-excel.blade.php`

Added:
- Business name shown at the top
- Date range shown when selected

### Controller export support
File: `app/Http/Controllers/Inventory/PurchaseController.php`

Added:
- Business filtering in index and export queries
- Business name lookup for report headers
- Business column in CSV export

## 12. Why This Helps Profit and Loss Later
This structure is the foundation for business-wise profit and loss.

### Current state
- Products have an owning business.
- Purchases have a business.
- Ecommerce products can be traced back to the business through the product.

### Next level for true ecommerce P/L
To compute accurate ecommerce profit/loss later, you would typically also store:
- order headers
- order items
- unit cost snapshot at sale time
- sale price at sale time
- refunds and fees

Then you can calculate:
- line profit = sale price - cost price
- business profit = sum of line profit for that business

## 13. Important Business Rules Now in the System
1. Every product must belong to one business account.
2. Same name + same brand/company cannot exist under two different businesses.
3. Same product name is allowed if the brand/company is different.
4. Purchase stock-in must follow the selected business account.
5. Ecommerce product selection must follow the selected business account.
6. Reports and exports should be filtered by business when requested.

## 14. Viva-Ready Short Explanation
You can say this in a viva:

> We implemented a single-owner business model. Every product belongs to one business account using `business_id`. We added validation so the same product name and brand cannot be reused by another business, but the same name is allowed with a different brand. We also updated inventory, ecommerce, and purchase stock-in screens so they all work with the selected business account. This reduces confusion and makes business-wise reporting and profit/loss tracking much cleaner.

## 15. Common Viva Questions and Answers

### Q1. Why did you choose a single-owner model?
Because it keeps ownership clear and makes reporting simpler. One product belongs to one business only.

### Q2. Why not allow the same product to belong to multiple businesses?
That would make duplicate handling and profit attribution complicated. It is possible, but it is a more complex shared-stock model.

### Q3. Why is `business_id` required now?
Because the system needs to know which business owns the product for reporting, filtering, and duplicate control.

### Q4. Why normalize the product name?
To avoid duplicates caused by case differences or extra spaces.

### Q5. Why does the purchase form use business-scoped autocomplete?
So products from one business do not leak into another business’s purchase entry.

### Q6. What happens if the same product exists in another business?
The system blocks the save if the same name and same brand/company already belongs to another business.

### Q7. What happens if the same product name exists but brand is different?
That is allowed, because it is treated as a separate product identity.

### Q8. How does this help ecommerce?
Ecommerce products can be shown only for the selected business, preventing mixed catalogs.

### Q9. How does this help reporting?
All purchases, products, and ecommerce items can now be filtered by business account.

### Q10. What is the next step for full profit/loss tracking?
Add ecommerce order and order-item tables with sale price and cost snapshot at the time of sale.

## 16. Files Changed in This Work
- `app/Http/Controllers/Inventory/ProductController.php`
- `app/Models/Product.php`
- `app/Models/Business.php`
- `database/migrations/2026_04_04_120000_add_business_id_to_products_table.php`
- `resources/views/inventory/products/create.blade.php`
- `resources/views/inventory/products/edit.blade.php`
- `resources/views/inventory/products/index.blade.php`
- `app/Http/Controllers/Inventory/EcommerceProductController.php`
- `resources/views/frontend/product/index.blade.php`
- `resources/views/frontend/product/create.blade.php`
- `app/Http/Controllers/Inventory/PurchaseController.php`
- `resources/views/inventory/purchases/create.blade.php`
- `resources/views/inventory/purchases/index.blade.php`
- `resources/views/inventory/purchases/export-pdf.blade.php`
- `resources/views/inventory/purchases/export-excel.blade.php`

## 17. Final Summary
The system now has a business-aware product ownership model. Inventory, ecommerce, and purchase stock-in flows all respect the selected business account. Duplicate handling is safer, reports are clearer, and the foundation is ready for accurate business-wise profit and loss tracking.
