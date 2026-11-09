<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartCheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ClassBookingController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\GuestCheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentReturnController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/payments/midtrans/notification', MidtransWebhookController::class)
    ->middleware('throttle:120,1')
    ->name('payments.midtrans.notification');
Route::get('/payments/finish', [PaymentReturnController::class, 'show'])
    ->defaults('result', 'finish')
    ->name('payments.finish');
Route::get('/payments/pending', [PaymentReturnController::class, 'show'])
    ->defaults('result', 'pending')
    ->name('payments.pending');
Route::get('/payments/error', [PaymentReturnController::class, 'show'])
    ->defaults('result', 'error')
    ->name('payments.error');
Route::post('/cart/{plan}', [CartController::class, 'store'])->whereNumber('plan')->name('cart.store');
Route::patch('/cart/{plan}', [CartController::class, 'update'])->whereNumber('plan')->name('cart.update');
Route::delete('/cart/{plan}', [CartController::class, 'destroy'])->whereNumber('plan')->name('cart.destroy');
Route::post('/cart/guest-checkout', [GuestCheckoutController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('cart.guest-checkout');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/reviews', [ReviewController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('reviews.store');

    Route::post('/classes/{session}/book', [ClassBookingController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('classes.book');

    Route::delete('/classes/bookings/{booking}', [ClassBookingController::class, 'cancel'])
        ->name('classes.bookings.cancel');

    Route::get('/cart/checkout', [CartCheckoutController::class, 'create'])->name('cart.checkout');
    Route::post('/cart/checkout', [CartCheckoutController::class, 'store'])->name('cart.checkout.store');
    Route::post('/payments/{payment}/start', [PaymentController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('payments.start');

    Route::get('/membership/checkout/{plan}', [MembershipController::class, 'checkoutForm'])->name('membership.checkout');
    Route::post('/membership/checkout/{plan}', [MembershipController::class, 'checkoutStore']);
    Route::get('/membership/invoice/{transaction}', [MembershipController::class, 'invoice'])->name('membership.invoice');
    Route::post('/membership/invoice/{transaction}/proof', [MembershipController::class, 'uploadProof'])
        ->name('membership.invoice.proof');
});
