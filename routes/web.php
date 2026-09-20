<?php

use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GymController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

// Halaman publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/gyms', [GymController::class, 'index'])->name('gyms.index');
Route::get('/gyms/{gym:slug}', [GymController::class, 'show'])->name('gyms.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// SEMENTARA. Diganti MapController di Step 6.
// Sebelumnya: Route::view('/map', 'map')->name('map');
Route::get('/map', [MapController::class, 'index'])->name('map');

// Hanya untuk yang BELUM login
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

// Hanya untuk yang SUDAH login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

    Route::post('/gyms/{gym:slug}/reviews', [ReviewController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('reviews.store');
});