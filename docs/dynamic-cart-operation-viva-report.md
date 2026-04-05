# Dynamic Cart Operation Viva Report

## 1. Report Objective
This report explains how the cart works end-to-end in the current GroceMate frontend implementation, including:
- add-to-cart from product cards
- duplicate item prevention
- dynamic header cart count
- remove and quantity update behavior
- cart summary calculation
- current limitations and future backend integration points

This is written for viva presentation in technical and practical detail.

---

## 2. Scope of Current Implementation
Current cart behavior is primarily frontend-driven using browser storage (`localStorage`) with a shared cart service.

### Included in current scope
- Dynamic add from product cart icons
- Duplicate protection (same product cannot be added twice)
- Toast/popup feedback
- Header count live updates
- Cart page remove and qty synchronization
- Count synchronization across tabs/windows (via storage event)

### Not yet fully backend-persistent
- Server-side cart DB per user
- Login-linked cart merge strategy
- Real API integration for add/update/remove

---

## 3. Files Involved

### Core shared cart engine
- `resources/views/frontend/layouts/main.blade.php`

### Header cart UI and count badges
- `resources/views/frontend/layouts/header.blade.php`

### Product pages that send cart data
- `resources/views/frontend/home/index.blade.php`
- `resources/views/frontend/advanced/index.blade.php`
- `resources/views/frontend/description/index.blade.php`

### Cart page operation and synchronization
- `resources/views/frontend/cart/index.blade.php`

### Route/controller layer (currently mostly placeholder)
- `routes/web.php`
- `app/Http/Controllers/frontend/CartController.php`

---

## 4. High-Level Architecture

The cart follows a **shared frontend cart service** approach:

1. Product UI emits add action with product dataset (`id`, `name`, `price`, `image`).
2. Global cart service in main layout receives and validates.
3. Cart service writes to `localStorage` key: `gm_cart_items`.
4. Service triggers custom update event and refreshes header badge count.
5. Cart page reads same storage and synchronizes remove/qty operations.
6. Any remove/update operation updates storage and header count instantly.

Design pattern: **single source of truth in localStorage + global service API**.

---

## 5. Cart Data Model

Each cart entry is stored as:

```json
{
  "id": "123",
  "name": "Product Name",
  "price": 120,
  "image": "https://.../image.jpg",
  "qty": 1
}
```

Storage key:
- `gm_cart_items`

---

## 6. Global Cart Service (Main Layout)

In `main.blade.php`, a global object is exposed:

```js
window.GroceMateCart
```

### Main functions
- `getItems()` -> read array from storage
- `setItems(items)` -> replace full cart
- `addItem(item)` -> add new item if not duplicate
- `removeItem(itemId)` -> remove by id
- `updateQty(itemId, qty)` -> update quantity (min 1)
- `updateBadges()` -> update all `.gm-cart-count` badges
- `showToast(title, icon)` -> SweetAlert toast fallback to alert

### Duplicate check rule
Before insert, service checks if an item with same normalized id already exists.

If exists:
- item is not re-added
- returns `{ added: false, reason: 'exists' }`
- UI shows “This product is already in cart”

---

## 7. Header Cart Count Logic

Header has two cart badges (mobile + desktop) using class:
- `.gm-cart-count`

On every cart mutation:
- count = number of unique cart items
- badge text updates (`0,1,2,...`)
- badge hides when count is 0

Also synchronized for:
- same-tab updates (`gm-cart-updated` custom event)
- cross-tab updates (`storage` event)

---

## 8. Product Add Flow

### A) Home/Advanced/Description product card icons
Each icon now carries dataset attributes:
- `data-product-id`
- `data-product-name`
- `data-product-price`
- `data-product-image`

Global delegated click listener in `main.blade.php` handles:
1. detect clicked `.gm-cart-icon-badge`
2. read dataset
3. call `GroceMateCart.addItem(...)`
4. show success or duplicate warning toast
5. update header badges

### B) Description page add-to-cart button
Description page `addToLocalCart()` was aligned to use `GroceMateCart` too.
So both:
- icon add
- button add
follow same duplicate and badge rules.

---

## 9. Cart Page Synchronization Flow

Cart page script uses:

```js
const cartApi = window.GroceMateCart || null;
```

### Initialization
- If DOM cart items and storage differ, cart seeds/syncs storage from DOM ids.
- Quantity values are pushed into storage.
- Header badge is refreshed via `cartApi.updateBadges()`.

### Single item remove
When user clicks remove:
1. confirm prompt
2. animate card removal
3. remove DOM row
4. call `cartApi.removeItem(itemId)`
5. recalculate total
6. header count auto decreases

### Bulk remove
For selected items:
1. confirm prompt
2. remove each item from DOM
3. remove each id from storage
4. recalc total and update empty state
5. header count decreases accordingly

### Quantity change
`+` and `-`:
- update row qty
- call `cartApi.updateQty(itemId, qty)`
- recalculate total

---

## 10. Cart Summary Calculation (Current)

Current summary intentionally shows only:
- **Total**

Logic:
- total = sum of `price * qty` for checked items
- no extra discount line
- no delivery fee line (moved to checkout stage)

---

## 11. User Experience Rules Implemented

1. Add new product -> success toast + header count +1  
2. Add same product again -> warning toast “already in cart”, no count increase  
3. Remove product from cart page -> header count decreases instantly  
4. Remove all selected -> count decreases based on remaining items  
5. Empty cart -> badge hides (`display: none`)

---

## 12. Event-Driven Behavior Summary

### Trigger sources
- click on product cart icon
- click on description add button
- click remove in cart page
- quantity plus/minus in cart page
- tab storage sync

### Update channels
- `localStorage` write
- `gm-cart-updated` event
- `storage` event
- `updateBadges()` UI refresh

---

## 13. Why This Design Works for Current Phase

Advantages:
- fast and simple integration across multiple pages
- no server round-trip required for basic cart UX
- consistent behavior from one shared JS service
- easy to demo in viva as modular architecture

---

## 14. Known Limitations (Important for Viva)

1. Cart is browser-local, not account-linked.
2. Clearing browser storage removes cart.
3. Multi-device sync is not available.
4. CartController API methods are still placeholders for real persistence.
5. Current cart count is item-count (unique products), not total quantity sum.

Mentioning these in viva shows realistic engineering awareness.

---

## 15. Recommended Next Phase

1. Implement backend cart tables (`carts`, `cart_items`) linked to user/session.
2. Replace local-only operations with API calls:
   - add
   - update qty
   - remove
   - fetch cart
3. Add guest-cart to user-cart merge on login.
4. Make cart summary server-authoritative (tax, shipping, coupons).
5. Keep localStorage as temporary offline cache only.

---

## 16. Viva One-Liner
We implemented a shared, event-driven cart service using localStorage as the temporary source of truth, enabling dynamic add/remove/duplicate-check behavior and real-time header cart count synchronization across all frontend pages.

---

## 17. Viva Q&A (Detailed)

### Q1. How is cart data shared across pages?
Through a global `GroceMateCart` object loaded in `main.blade.php` and persistent `localStorage`.

### Q2. How do you prevent duplicate products?
`addItem()` checks normalized `id`; if already present, it blocks insert and shows warning.

### Q3. How does header count update instantly?
After each mutation, `updateBadges()` updates all `.gm-cart-count` elements.

### Q4. What triggers badge updates besides direct clicks?
Custom event `gm-cart-updated` and browser `storage` event for cross-tab sync.

### Q5. Why use delegated click for product cart icons?
Because product cards may be dynamic; delegation captures all matching icons reliably.

### Q6. What happens when same item is clicked again?
No duplicate insert; user sees “This product is already in cart”.

### Q7. Is quantity included in header count?
Currently no; count is number of unique items.

### Q8. How is quantity handled in cart page?
Qty buttons update DOM and call `GroceMateCart.updateQty(id, qty)`.

### Q9. How does remove affect header count?
Remove operations call `removeItem`, storage updates, then badge refresh reduces count.

### Q10. Why is delivery fee removed in cart summary?
Delivery is now applied only in checkout to avoid double counting.

### Q11. Why is discount row removed in summary?
Item prices already represent discounted values in cart display.

### Q12. What is the current source of truth?
`localStorage` key `gm_cart_items`.

### Q13. Is this implementation production-final?
Partially. UX is complete, but backend persistence and user-linked carts are pending.

### Q14. What is the main risk with localStorage cart?
Data is browser-specific and can be cleared manually.

### Q15. What backend enhancement would you implement first?
Create cart APIs and store cart by user/session in DB.

### Q16. How do you explain this architecture briefly?
Single shared cart service + event-driven UI updates + storage persistence.

### Q17. Why is this suitable for current project stage?
Fast implementation, easy testing, clear behavior, and clean upgrade path to backend.

### Q18. Final short viva summary
Cart operations are now dynamic and synchronized: add from product cards, prevent duplicates, maintain live header count, and decrease count on removal, all powered by a shared frontend cart service.
