<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClassBookingController;
use App\Http\Controllers\ClassController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

    Route::post('/classes/{session}/book', [ClassBookingController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('classes.book');

    Route::delete('/classes/bookings/{booking}', [ClassBookingController::class, 'cancel'])
    ->name('classes.bookings.cancel');

    Route::get('/membership/checkout/{plan}', [MembershipController::class, 'checkoutForm'])->name('membership.checkout');
    Route::post('/membership/checkout/{plan}', [MembershipController::class, 'checkoutStore']);
    Route::get('/membership/invoice/{transaction}', [MembershipController::class, 'invoice'])->name('membership.invoice');
    Route::post('/membership/invoice/{transaction}/proof', [MembershipController::class, 'uploadProof'])
        ->name('membership.invoice.proof');
});