<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\frontend\AccountController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\AdvancedController;
use App\Http\Controllers\frontend\CheckoutController;
use App\Http\Controllers\frontend\DescriptionController;
use App\Http\Controllers\frontend\OTPController;
use App\Http\Controllers\Auth\ForgotPasswordController;

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

Route::get('/verify-otp', [OTPController::class, 'index'])->name('Otp.Form');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showEmailForm'])->name('showEmailForm');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.sendOtp');

Route::get('/verify-otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otpForm');
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verifyOtp');

Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.resetForm');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');