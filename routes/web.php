<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\frontend\AccountController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\AdvancedController;
use App\Http\Controllers\frontend\CheckoutController;
use App\Http\Controllers\frontend\DescriptionController;
use App\Http\Controllers\frontend\OTPController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Inventory\DashboardController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\PurchaseController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\POS\CustomerController;
use App\Http\Controllers\POS\InvoiceController;
use App\Http\Controllers\BusinessController;

Route::get('/business/create', [BusinessController::class, 'create'])->name('business.create');
Route::post('/business/store', [BusinessController::class, 'store'])->name('business.store');
Route::get('/business', [BusinessController::class, 'index'])->name('business.index');
Route::get('/business/{business}/edit', [BusinessController::class, 'edit'])->name('business.edit');
Route::put('/business/{business}', [BusinessController::class, 'update'])->name('business.update');
Route::delete('/business/{business}', [BusinessController::class, 'destroy'])->name('business.destroy');
Route::get('/business/{business}/image', [BusinessController::class, 'getImage'])->name('business.image');


// Redirect root URL to login page
Route::get('/', [AccountController::class, 'login'])->name('page-login');

// Login routes
Route::get('/login', [AccountController::class, 'login'])->name('page-login');
Route::get('/auth/login', [AccountController::class, 'login'])->name('login');
Route::post('/login', [AccountController::class, 'store'])->name('login.post');

// home after login
Route::get('/home', function() {
    return view('frontend.home.index');
})->middleware('auth')->name('home');

Route::get('/advanced', [AdvancedController::class, 'advanced'])->name('advanced');
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::get('/description', [DescriptionController::class, 'description'])->name('description');

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

Route::middleware(['auth'])
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
        
        Route::resource('categories', CategoryController::class)->except(['show']);
        
        // Purchase routes - using resource route with custom additional routes
        Route::resource('purchases', PurchaseController::class);
        Route::get('/purchases/search-products', [PurchaseController::class, 'searchProducts'])
            ->name('purchases.search-products');
        
        Route::get('/alerts/expiry', [PurchaseController::class,'expiryAlerts'])->name('alerts.expiry');
        Route::resource('invoices', InvoiceController::class);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::get('/purchases/export/{type}',[PurchaseController::class, 'export'])->name('purchases.export');
    
        // Individual purchase export route
        Route::get('/purchases/{purchase}/export/{type}',[PurchaseController::class, 'exportIndividual'])->name('purchases.export-individual');
    
        // Test route for debugging date filtering
        Route::get('/purchases/test-date-filter', [PurchaseController::class, 'testDateFilter']);

    }
    
);

Route::middleware(['auth'])
    ->prefix('pos')
    ->name('pos.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('pos.dashboard');
        })->name('dashboard');

        Route::resource('customers', CustomerController::class)->except(['show']);
        
        Route::resource('invoices', InvoiceController::class);
        
        Route::get('/invoices/bulk-export/{format}', [InvoiceController::class, 'bulkExport'])->name('invoices.bulk-export');
        
        Route::get('/invoices/{invoice}/export/{format}', [InvoiceController::class, 'export'])->name('invoices.export');

    });

