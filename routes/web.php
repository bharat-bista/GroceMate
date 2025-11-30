<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\frontend\AccountController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\AdvancedController;
use App\Http\Controllers\frontend\CheckoutController;
use App\Http\Controllers\frontend\DescriptionController;
// Redirect root URL to login page
Route::get('/', [AccountController::class, 'login'])->name('page-login');

// Login routes
Route::get('/login', [AccountController::class, 'login'])->name('page-login');
Route::post('/login', [AccountController::class, 'store'])->name('login.post');

// home after login
Route::get('/home', function() {
    return view('frontend.home.index');
})->middleware('auth')->name('home');


Route::get('/advanced', [AdvancedController::class, 'advanced'])->name('advanced');
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::get('/description', [DescriptionController::class, 'description'])->name('description');