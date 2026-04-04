# Business Owner Model Viva Q&A

## 1. What problem did you solve?
I solved the confusion of product ownership across multiple business accounts by making every product belong to one business.

## 2. What is the core design?
A single-owner model. Each product has one `business_id` and belongs to one business only.

## 3. Why was this needed?
Because inventory, ecommerce, purchases, and profit/loss reports were mixing data from different businesses.

## 4. What database change did you make?
I added `business_id` to the `products` table as a foreign key to `businesses`.

## 5. Why is `business_id` important?
It tells the system which business owns the product.

## 6. Why did you make the column nullable in migration?
To avoid breaking old data while existing products are gradually assigned to businesses.

## 7. What model changes were made?
- `Product` now belongs to `Business`
- `Business` now has many `Product`

## 8. How does product creation work now?
The admin must choose a business account before saving the product.

## 9. How does product update work?
The admin can change ownership, but validation still prevents duplicates and conflicts.

## 10. What duplicate rule did you implement?
Same normalized product name + same brand/company cannot exist in the same business or another business.

## 11. What if the same name exists but brand is different?
That is allowed.

## 12. Why allow same name with different brand?
Because the business/company identity is different, so it is treated as a different product entry.

## 13. Why normalize the product name?
To avoid false duplicates caused by spacing or case differences.

## 14. How does ecommerce use the business model?
Ecommerce product lists and create screens are filtered by business so only the selected business’s products appear.

## 15. How does purchase stock-in use the business model?
Purchase creation and autocomplete are scoped to the selected business account.

## 16. What was the purchase bug you fixed?
The same product could get reused under the wrong business when stock-in was repeated from another business.

## 17. How did you fix the purchase issue?
I scoped purchase search by business and made new products created from purchase stock-in inherit the selected `business_id`.

## 18. Why is that important?
So each purchase entry stays linked to the correct business and reports remain accurate.

## 19. How do reports work now?
Purchase lists and exports can be filtered by business account.

## 20. What did you add to the export files?
Business name and business-aware filters in CSV, PDF, and Excel exports.

## 21. How does this help profit/loss tracking?
It creates a clear ownership base so future business-wise profit/loss calculations can be reliable.

## 22. Is this enough for full ecommerce profit/loss?
Not fully. It is the ownership foundation. Full ecommerce profit/loss also needs order records, sold quantity, and sale-time cost snapshots.

## 23. What would be the next step for full ecommerce P/L?
Add ecommerce orders and order items, then calculate profit using sale price minus product cost.

## 24. Why is the model called single-owner?
Because one product record belongs to one business account only.

## 25. What is the main benefit of this design?
It makes data clean, reporting easier, and ownership clear across inventory, ecommerce, and purchases.

## 26. Short viva answer summary
We implemented a business-based single-owner product system. Every product belongs to one business via `business_id`. Inventory, ecommerce, and purchase stock-in now respect the selected business, and duplicate checks prevent cross-business conflicts. This gives clear ownership and prepares the system for accurate profit/loss tracking.
