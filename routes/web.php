<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\frontend\AccountController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\AdvancedController;
use App\Http\Controllers\frontend\CheckoutController;
use App\Http\Controllers\frontend\DescriptionController;
use App\Http\Controllers\frontend\CartController;
use App\Http\Controllers\frontend\ContactController;
use App\Http\Controllers\frontend\OTPController;
use App\Http\Controllers\frontend\SliderController;
use App\Http\Controllers\frontend\OrderController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Inventory\DashboardController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\BrandController;
use App\Http\Controllers\Inventory\PurchaseController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\Inventory\AdminAccountController;
use App\Http\Controllers\Inventory\EcommerceProductController;
use App\Http\Controllers\Inventory\EcommerceBrandController;
use App\Http\Controllers\Inventory\EcommerceCategoryController;
use App\Http\Controllers\Inventory\EcommerceIncomeController;
use App\Http\Controllers\POS\SupplierPaymentController;
use App\Http\Controllers\POS\CustomerController;
use App\Http\Controllers\POS\InvoiceController;
use App\Http\Controllers\POS\IncomeController;
use App\Http\Controllers\POS\ExpenseController;
use App\Http\Controllers\POS\DashboardController as POSDashboardController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\DeliveryFeeSettingController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\AdminChatbotController;


// Khalti Payment Verification Route
Route::post('/khalti/verify', [SupplierPaymentController::class, 'verifyKhalti']);


// Redirect root URL to login page
Route::get('/', [AccountController::class, 'login'])->name('root');

// Login routes
Route::get('/login', [AccountController::class, 'login'])->name('page-login');
Route::get('/auth/login', [AccountController::class, 'login'])->name('login');
Route::post('/login', [AccountController::class, 'store'])->name('login.post');

// Logout route
Route::post('/logout', [AccountController::class, 'logout'])->name('logout')->middleware('auth');

// home after login
Route::get('/home', [HomeController::class, 'home'])->middleware('auth')->name('home');

Route::get('/advanced', [AdvancedController::class, 'advanced'])->name('advanced');
Route::get('/search/suggestions', [AdvancedController::class, 'suggestions'])->name('search.suggestions');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'removeItem'])->name('cart.remove');
Route::post('/cart/promo', [CartController::class, 'applyPromoCode'])->name('cart.promo');
Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/checkout/esewa/initiate', [CheckoutController::class, 'initiateEsewa'])->name('frontend.checkout.esewa.initiate');
Route::get('/checkout/esewa/callback', [CheckoutController::class, 'esewaCallback'])->name('frontend.checkout.esewa.callback');
Route::post('/checkout/order', [OrderController::class, 'store'])->name('frontend.order.store');

// Frontend order routes
Route::get('/orders', [OrderController::class, 'index'])->name('orders');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

// Admin order routes
Route::middleware(['auth', 'admin_or_staff'])
    ->prefix('inventory/orders')
    ->name('inventory.orders.')
    ->group(function () {
        Route::get('/', [OrderController::class, 'adminIndex'])->name('index');
        Route::get('/{order}', [OrderController::class, 'adminShow'])->name('show');
        Route::post('/{order}/send-message', [OrderController::class, 'sendCustomerMessage'])->name('send-message');
        Route::patch('/{order}/delivery-status', [OrderController::class, 'updateDeliveryStatus'])->name('delivery-status');
        Route::patch('/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('payment-status');
        Route::patch('/{order}/verify-slip', [OrderController::class, 'verifyPaymentSlip'])->name('verify-slip');
        Route::get('/export/{type}', [OrderController::class, 'export'])->name('export');
    });

// Admin contact message routes
Route::middleware(['auth', 'admin_or_staff'])
    ->prefix('inventory/contacts')
    ->name('inventory.contacts.')
    ->group(function () {
        Route::get('/', [ContactController::class, 'adminIndex'])->name('index');
        Route::get('/{contactMessage}', [ContactController::class, 'adminShow'])->name('show');
        Route::post('/{contactMessage}/send-message', [ContactController::class, 'sendMessage'])->name('send-message');
    });

Route::get('/description/{ecommerceProduct?}', [DescriptionController::class, 'description'])
    ->whereNumber('ecommerceProduct')
    ->name('description');

Route::get('/verify-otp', [OTPController::class, 'index'])->name('Otp.Form');

Route::get('/forgot-password', [ForgetPasswordController::class, 'showEmailForm'])->name('password.request');
Route::post('/forgot-password', [ForgetPasswordController::class, 'sendOtp'])->name('password.sendOtp');

Route::get('/verify-otp', [ForgetPasswordController::class, 'showOtpForm'])->name('password.otpForm');
Route::post('/verify-otp', [ForgetPasswordController::class, 'verifyOtp'])->name('password.verifyOtp');

Route::get('/reset-password', [ForgetPasswordController::class, 'showResetForm'])->name('password.resetForm');
Route::post('/reset-password', [ForgetPasswordController::class, 'resetPassword'])->name('password.reset');

Route::get('/register', [AccountController::class, 'register'])->name('register');
Route::post('/register', [AccountController::class, 'registerPost'])->name('register.sendOtp');

Route::get('/register/verify-otp/{user}', [AccountController::class, 'showRegisterOtpForm'])->name('register.otpForm');
Route::post('/register/verify-otp/{user}', [AccountController::class, 'verifyRegisterOtp'])->name('register.verifyOtp');

Route::get('/auth/google', [AccountController::class, 'googleRedirect'])->name('google.redirect');
Route::get('/auth/google/callback', [AccountController::class, 'googleCallback'])->name('google.callback');

Route::middleware(['auth', 'admin_or_staff'])
    ->prefix('inventory')
    ->name('inventory.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/products', [ProductController::class, 'index'])
            ->name('products.index');

        Route::get('/products/create', [ProductController::class, 'create'])
            ->name('products.create');

        Route::post('/products', [ProductController::class, 'store'])
            ->name('products.store');

        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
            ->name('products.edit');

        Route::put('/products/{product}', [ProductController::class, 'update'])
            ->name('products.update');

        Route::post('/products/{product}/toggle-listed', [ProductController::class, 'toggleListed'])
            ->name('products.toggle-listed');

        Route::get('/stock/{product}/batches', [ProductController::class, 'batches'])
            ->name('stock.batches');
        
        Route::resource('categories', CategoryController::class)->except(['show']);
        
        Route::resource('brands', BrandController::class)->except(['show']);
        
        // AJAX routes for searching and creating categories/brands inline (purchase form)
        // IMPORTANT: These must come BEFORE Route::resource to avoid being caught by {purchase} param
        Route::get('/purchases/search-products', [PurchaseController::class, 'searchProducts'])
            ->name('purchases.search-products');
        Route::get('/purchases/search-categories', [PurchaseController::class, 'searchCategories'])
            ->name('purchases.search-categories');
        Route::get('/purchases/search-brands', [PurchaseController::class, 'searchBrands'])
            ->name('purchases.search-brands');
        Route::post('/purchases/store-category', [PurchaseController::class, 'storeCategory'])
            ->name('purchases.store-category');
        Route::post('/purchases/store-brand', [PurchaseController::class, 'storeBrand'])
            ->name('purchases.store-brand');
        Route::get('/purchases/export/{type}',[PurchaseController::class, 'export'])->name('purchases.export');
        Route::get('/alerts/expiry', [PurchaseController::class,'expiryAlerts'])->name('alerts.expiry');
        Route::post('/chatbot/message', [AdminChatbotController::class, 'message'])->name('chatbot.message');
        
        // Purchase resource routes (must come AFTER custom routes)
        Route::resource('purchases', PurchaseController::class);
        Route::resource('invoices', InvoiceController::class);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    
        // Individual purchase export route
        Route::get('/purchases/{purchase}/export/{type}',[PurchaseController::class, 'exportIndividual'])->name('purchases.export-individual');
    
        // Test route for debugging date filtering
        Route::get('/purchases/test-date-filter', [PurchaseController::class, 'testDateFilter']);

        // E-commerce Products routes
        Route::resource('ecommerce-products', EcommerceProductController::class);
        Route::resource('sliders', SliderController::class);
        Route::delete('ecommerce-products/{ecommerce_product}/images/{image}', [EcommerceProductController::class, 'deleteImage'])->name('ecommerce-products.delete-image');
        Route::post('ecommerce-products/{ecommerce_product}/images/{image}/primary', [EcommerceProductController::class, 'setPrimaryImage'])->name('ecommerce-products.set-primary-image');

        // E-commerce Brands routes
        Route::get('ecommerce-brands', [EcommerceBrandController::class, 'index'])->name('ecommerce-brands.index');
        Route::get('ecommerce-brands/{brand}', [EcommerceBrandController::class, 'show'])->name('ecommerce-brands.show');

        // E-commerce Categories routes
        Route::get('ecommerce-categories', [EcommerceCategoryController::class, 'index'])->name('ecommerce-categories.index');
        Route::get('ecommerce-categories/{category}', [EcommerceCategoryController::class, 'show'])->name('ecommerce-categories.show');

        // E-commerce Income routes
        Route::get('ecommerce-income', [EcommerceIncomeController::class, 'index'])->name('ecommerce-income.index');
        Route::get('ecommerce-income/export/{type}', [EcommerceIncomeController::class, 'export'])->name('ecommerce-income.export');
    }
    
);

Route::middleware(['auth', 'admin'])
    ->prefix('pos')
    ->name('pos.')
    ->group(function () {

        Route::get('/dashboard', [POSDashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('customers', CustomerController::class)->except(['show']);
        
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        
        Route::resource('invoices', InvoiceController::class);
        
        Route::get('/invoices/bulk-export/{format}', [InvoiceController::class, 'bulkExport'])->name('invoices.bulk-export');
        
        Route::get('/invoices/{invoice}/export/{format}', [InvoiceController::class, 'export'])->name('invoices.export');
        
        Route::post('/invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');

        // Income routes
        Route::get('/income', [IncomeController::class, 'index'])->name('income.index');
        Route::get('/income/create', [IncomeController::class, 'create'])->name('income.create');
        Route::post('/income', [IncomeController::class, 'store'])->name('income.store');
        Route::get('/income/{income}/edit', [IncomeController::class, 'edit'])->name('income.edit');
        Route::put('/income/{income}', [IncomeController::class, 'update'])->name('income.update');
        Route::delete('/income/{income}', [IncomeController::class, 'destroy'])->name('income.destroy');

        
        // Income export routes
        Route::get('/income/export/{type}', [IncomeController::class, 'export'])->name('income.export');
        Route::get('/income/{income}/export/{type}', [IncomeController::class, 'exportIndividual'])->name('income.export-individual');

        // Expense routes
        Route::resource('expenses', ExpenseController::class);
        

        // Supplier Payment routes
        Route::resource('supplier-payments', SupplierPaymentController::class)->except(['show']);
        // Supplier Payment routes
        Route::resource('supplier-payments', SupplierPaymentController::class)->except(['show']);
        
        // Supplier Payment export routes (bulk only)
        Route::get('/supplier-payments/export/{type}', [SupplierPaymentController::class, 'export'])->name('supplier-payments.export');

        // ✅ ADD THESE HERE
        Route::post('/khalti/initiate', [SupplierPaymentController::class, 'initiateKhalti'])
            ->name('khalti.initiate');

        Route::post('/esewa/initiate', [SupplierPaymentController::class, 'initiateEsewa'])
            ->name('esewa.initiate');

    
    });

Route::middleware(['auth', 'admin'])
    ->prefix('admin/accounts')
    ->name('admin.accounts.')
    ->group(function () {
        Route::get('/', [AdminAccountController::class, 'index'])->name('index');
        Route::get('/create', [AdminAccountController::class, 'create'])->name('create');
        Route::post('/send-otp', [AdminAccountController::class, 'sendOtp'])->name('sendOtp');
        Route::post('/verify-otp', [AdminAccountController::class, 'verifyOtp'])->name('verifyOtp');
        Route::delete('/{user}', [AdminAccountController::class, 'destroy'])->name('destroy');
    });

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/business/create', [BusinessController::class, 'create'])->name('business.create');
    Route::post('/business/store', [BusinessController::class, 'store'])->name('business.store');
    Route::get('/business', [BusinessController::class, 'index'])->name('business.index');
    Route::get('/business/{business}', [BusinessController::class, 'show'])->name('business.show');
    Route::get('/business/{business}/export/{type}', [BusinessController::class, 'export'])->name('business.export');
    Route::get('/business/{business}/edit', [BusinessController::class, 'edit'])->name('business.edit');
    Route::put('/business/{business}', [BusinessController::class, 'update'])->name('business.update');
    Route::delete('/business/{business}', [BusinessController::class, 'destroy'])->name('business.destroy');
    Route::get('/business/{business}/image', [BusinessController::class, 'getImage'])->name('business.image');
    
    // Tax Settings Routes
    Route::prefix('taxes')->name('taxes.')->group(function () {
        Route::get('/', [TaxController::class, 'index'])->name('index');
        Route::get('/create', [TaxController::class, 'create'])->name('create');
        Route::post('/', [TaxController::class, 'store'])->name('store');
        Route::get('/{tax}/edit', [TaxController::class, 'edit'])->name('edit');
        Route::put('/{tax}', [TaxController::class, 'update'])->name('update');
        Route::delete('/{tax}', [TaxController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('settings/delivery-fees')->name('delivery-fees.')->group(function () {
        Route::get('/', [DeliveryFeeSettingController::class, 'index'])->name('index');
        Route::put('/', [DeliveryFeeSettingController::class, 'update'])->name('update');
    });
});

Route::get('/pos/khalti/callback', [SupplierPaymentController::class, 'khaltiCallback'])
    ->name('pos.khalti.callback');  // ✅ added 'pos.' prefix
    Route::post('/pos/khalti/verify', [SupplierPaymentController::class, 'verifyKhalti']);

// ✅ ADD HERE — outside auth middleware
Route::get('/pos/esewa/callback', [SupplierPaymentController::class, 'esewaCallback'])
    ->name('pos.esewa.callback');
