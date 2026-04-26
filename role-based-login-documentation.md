# GroceMate — Role-Based Login Documentation

This document explains the complete role-based login system in `GroceMate`, including the purpose, flow, code structure, routes, security rules, and admin account management process.

---

## 1. Overview

The system supports **three user roles**:

- **Admin** → `role_id = 1`
- **Customer** → `role_id = 2`
- **Staff** → `role_id = 3`

The role determines where the user is redirected after login and which parts of the system they can access.

### Access Policy

| Role | Access |
|------|--------|
| Admin (`role_id = 1`) | Inventory, POS, Business Profile, Admin Accounts |
| Staff (`role_id = 3`) | Inventory + Ecommerce sections in admin panel (no POS, no Business/Admin settings) |
| Customer (`role_id = 2`) | Public frontend / home page only |

---

## 2. Core Role Logic

The role-based system depends on the `role_id` column in the `users` table.

### Role Mapping
- `1` = Admin
- `2` = Customer
- `3` = Staff

### Helper Methods in `User` Model

File: `app/Models/User.php`

Role helpers are available to simplify checks across the application:

```php
public function isAdmin(): bool
{
    return $this->role_id === 1;
}

public function isCustomer(): bool
{
    return $this->role_id === 2;
}

public function isStaff(): bool
{
    return $this->role_id === 3;
}

public function canAccessInventoryPanel(): bool
{
    return $this->isAdmin() || $this->isStaff();
}
```

### Why These Helpers Matter

Instead of repeatedly writing:

```php
if (auth()->user()->role_id === 1)
```

you can write:

```php
if (auth()->user()->isAdmin())
```

This makes controllers, views, and middleware easier to read and maintain.

---

## 3. Authentication Flow

File: `app/Http/Controllers/frontend/AccountController.php`

The login system supports:

- normal email/password login
- Google OAuth login
- customer registration with OTP verification

---

## 4. Normal Login Redirect Logic

When a user logs in with email and password, the system:

1. validates input
2. checks whether the email exists
3. checks whether the account is verified
4. verifies the password
5. logs the user in
6. redirects based on role

### Current Redirect Rules

```php
Auth::login($user, $request->remember);

if ($user->canAccessInventoryPanel()) {
    return redirect()->route('inventory.dashboard')->with('success', 'Login successful!');
}

return redirect()->route('home')->with('success', 'Login successful!');
```

### Result
- Admins and staff go to: `inventory.dashboard`
- Customers go to: `home`

---

## 5. Google OAuth Redirect Logic

The Google callback follows the same role-based redirect logic.

### Behavior
- if the user already exists and is verified, they are logged in
- if the user does not exist, a new customer account is created automatically with:
  - `role_id = 2`
  - `status = 'Y'`
- after login, redirect depends on role

### Redirect Logic

```php
Auth::login($user);

if ($user->canAccessInventoryPanel()) {
    return redirect()->route('inventory.dashboard')->with('success', 'Logged in with Google!');
}

return redirect()->route('home')->with('success', 'Logged in with Google!');
```

---

## 6. Customer Registration Flow

Customers register through the frontend registration form.

### Registration Behavior
When a new user registers:
- `role_id` is set to `2`
- `status` is set to `'N'`
- OTP is generated and emailed
- account is activated only after OTP verification

### Registration Insert

```php
$user = User::create([
    'full_name' => $data['full_name'],
    'gender'    => $data['gender'],
    'email'     => $data['email'],
    'password'  => $data['password'],
    'role_id'   => 2,
    'status'    => 'N',
]);
```

### After OTP Verification
Once the OTP is verified:
- `status` becomes `'Y'`
- user is logged in
- user is redirected to `home`

This ensures that normal self-registration can only create customer accounts.

---

## 7. Admin and Staff Route Protection

Files: `app/Http/Middleware/AdminMiddleware.php`, `app/Http/Middleware/AdminOrStaffMiddleware.php`

Hiding admin links in the UI is not enough. Real security must happen at the route level.

### Middleware Logic

```php
if (!auth()->check() || !auth()->user()->isAdmin()) {
    return redirect()->route('home')
        ->with('popup_error', 'You do not have permission to access the admin panel.');
}
```

### What It Does
- blocks guests
- blocks logged-in customers from admin panel sections
- allows admin + staff into inventory/ecommerce routes
- keeps POS/business/accounts routes admin-only

If a customer manually types a protected URL like:

```text
/inventory/dashboard
/pos/dashboard
/business
/admin/accounts
```

they are redirected back to `home`.

---

## 8. Middleware Registration

File: `bootstrap/app.php`

The middleware aliases are:

```php
$middleware->alias([
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'admin_or_staff' => \App\Http\Middleware\AdminOrStaffMiddleware::class,
]);
```

This allows route groups to use:

```php
->middleware(['auth', 'admin'])
->middleware(['auth', 'admin_or_staff']) // inventory + ecommerce
```

---

## 9. Protected Route Groups

File: `routes/web.php`

The application uses route protection by section:

### Inventory Module
```php
Route::middleware(['auth', 'admin_or_staff'])
    ->prefix('inventory')
    ->name('inventory.')
    ->group(function () {
        ...
    });
```

### POS Module
```php
Route::middleware(['auth', 'admin'])
    ->prefix('pos')
    ->name('pos.')
    ->group(function () {
        ...
    });
```

### Business Profile Module
```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/business', ...);
    Route::get('/business/create', ...);
    ...
});
```

### Admin Account Management
```php
Route::middleware(['auth', 'admin'])
    ->prefix('admin/accounts')
    ->name('admin.accounts.')
    ->group(function () {
        ...
    });
```

---

## 10. Admin Account Management

File: `app/Http/Controllers/Inventory/AdminAccountController.php`

Since public registration only creates customers, a secure separate admin/staff creation flow is required.

The system provides an **admin-only Accounts module** for managing admin and staff users.

### Features
- list all admin/staff accounts
- create new admin or staff via OTP verification
- delete admin/staff accounts
- prevent self-deletion

---

## 11. Admin Accounts Routes

The following route names exist:

| Route Name | Purpose |
|-----------|---------|
| `admin.accounts.index` | View admin list |
| `admin.accounts.create` | Show create admin page |
| `admin.accounts.sendOtp` | Submit admin creation form and send OTP |
| `admin.accounts.verifyOtp` | Verify OTP and create admin |
| `admin.accounts.destroy` | Delete an admin account |

---

## 12. Admin Creation Flow

### Step 1 — Open Create Admin Page
An existing admin visits:

```text
/admin/accounts/create
```

### Step 2 — Fill Account Details
The form collects:
- account type (admin or staff)
- full name
- email
- password
- password confirmation

### Step 3 — Store Pending Account in Session
The controller temporarily stores the new account data in session:

```php
session([
    'managed_account' => [
        'full_name' => $validated['full_name'],
        'email'     => $validated['email'],
        'password'  => $validated['password'],
        'role_id'   => $roleId,
    ]
]);
```

### Step 4 — Generate and Send OTP
The system:
- generates a 6-digit OTP
- stores it in `otp_resets`
- sends it to the selected account email using `PasswordOtpMail`

with:

```php
'purpose' => 'managed_account_register'
```

### Step 5 — Verify OTP
The existing admin enters the code received by the selected account owner.

### Step 6 — Create the New Account
If the OTP is correct, the system creates the user with:

```php
User::create([
    'full_name' => $accountData['full_name'],
    'email'     => $accountData['email'],
    'password'  => $accountData['password'],
    'gender'    => 'other',
    'role_id'   => $accountData['role_id'], // 1 admin, 3 staff
    'status'    => 'Y',
]);
```

### Final Result
- new account is inserted only after successful OTP verification
- session data is cleared
- OTP records are cleaned up

---

## 13. OTP Security Rules for Admin Creation

The admin creation flow includes several protections:

### Validation Checks
- OTP must exist
- OTP must not already be used
- OTP must not be expired
- OTP attempts must be fewer than 5

### Invalid OTP Handling
On wrong OTP:
- attempts counter is incremented
- account is not created

### Expired or Missing Session
If admin creation session data is missing:
- flow is canceled
- user is redirected back to restart the process

This protects against accidental partial account creation.

---

## 14. Admin and Staff Listing

File: `app/Http/Controllers/Inventory/AdminAccountController.php`

Admin and staff accounts are listed using:

```php
$accounts = User::whereIn('role_id', [1, 3])->latest()->get();
```

This ensures the Accounts list only shows admin/staff users.

---

## 15. Admin Deletion Rules

Admins can delete other admin/staff accounts, but not their own.

### Self-Delete Protection

```php
if ($user->id === auth()->id()) {
    return redirect()->route('admin.accounts.index')
        ->with('error', '❌ You cannot delete your own account.');
}
```

### Non-Admin Delete Protection

```php
if (!$user->isAdmin()) {
    return redirect()->route('admin.accounts.index')
        ->with('error', '❌ This user is not an admin.');
}
```

### Why This Matters
- prevents accidental lockout
- prevents deleting non-admin users from the admin module

---

## 16. Inventory Sidebar Integration

File: `resources/views/inventory/layouts/inventory.blade.php`

An `Accounts` section is shown in the sidebar only for admin users.

### Sidebar Condition

```php
@if(auth()->check() && auth()->user()->isAdmin())
```

### Sidebar Link

```php
<a class="{{ $navLinkClass($isAccountsGroup) }}"
   href="{{ route('admin.accounts.index') }}">
   Manage Access
</a>
```

This gives admins a clear way to access account management from the dashboard UI.

---

## 17. User Name Display Fix

The inventory layout previously referenced `name`, but the system stores the user’s display name in `full_name`.

### Fixed Display

```php
{{ auth()->user()->full_name ?? 'User' }}
```

This ensures the logged-in user’s real name appears in the top bar.

---

## 18. Security Summary

This implementation provides several important protections:

### Customer Safety
- public registration always creates customers only
- customers cannot assign themselves admin rights

### Route Security
- protected routes are guarded by `auth` + `admin`
- customers cannot access admin modules even if they guess URLs

### Verified Admin Emails
- new admin is not saved until OTP is verified
- ensures the new admin email is real and reachable

### Admin Stability
- self-deletion is blocked
- admin management is only available to admins

---

## 19. Files Involved

### Core Role Logic
- `app/Models/User.php`

### Login / Registration / OAuth
- `app/Http/Controllers/frontend/AccountController.php`

### Admin Middleware
- `app/Http/Middleware/AdminMiddleware.php`
- `bootstrap/app.php`

### Route Definitions
- `routes/web.php`

### Admin Account Management
- `app/Http/Controllers/Inventory/AdminAccountController.php`
- `resources/views/inventory/accounts/create.blade.php`
- `resources/views/inventory/accounts/index.blade.php`

### Inventory Sidebar
- `resources/views/inventory/layouts/inventory.blade.php`

### OTP Support
- `app/Models/OtpReset.php`
- `app/Mail/PasswordOtpMail.php`
- `resources/views/emails/otp.blade.php`

---

## 20. Route Summary

### Public / Customer Routes
- `/login`
- `/register`
- `/auth/google`
- `/home`

### Admin + Staff Routes
- `/inventory/*`

### Admin-Protected Routes
- `/pos/*`
- `/business/*`
- `/admin/accounts/*`

---

## 21. Testing and Verification

The implementation was validated with:

### Route Check
```bash
php artisan route:list
```

### Test Suite
```bash
php artisan test
```

Both executed successfully.

---

## 22. Practical End-to-End Example

### Customer Login Example
1. customer logs in
2. system validates credentials
3. customer is authenticated
4. `isAdmin()` returns `false`
5. customer is redirected to `/home`

### Admin Login Example
1. admin logs in
2. system validates credentials
3. admin is authenticated
4. `isAdmin()` returns `true`
5. admin is redirected to `/inventory/dashboard`

### Unauthorized Access Example
1. customer manually opens `/inventory/dashboard`
2. `AdminMiddleware` blocks access
3. customer is redirected to `/home`
4. error message is shown

### New Admin Creation Example
1. existing admin opens Accounts → Create
2. fills new admin info
3. OTP is sent to new admin email
4. OTP is entered and verified
5. system creates user with `role_id = 1`
6. new admin can now log in and access admin modules

---

## 23. Final Outcome

The system now supports a proper role-based access model where:

- Admins are securely separated from customers
- Admin-only modules are protected at the route level
- Customers remain restricted to the public-facing side
- New admins can only be created by existing admins through OTP verification
- Admin account management is integrated into the dashboard sidebar

This makes the role-based login system secure, maintainable, and aligned with the intended GroceMate workflow.
