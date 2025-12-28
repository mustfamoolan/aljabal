<?php

use App\Http\Controllers\Representatives\AccountController;
use App\Http\Controllers\Representatives\DashboardController;
use App\Http\Controllers\Representatives\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Representative Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for representatives. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth:representative', 'representative'])->group(function () {
    // Dashboard
    Route::get('/representative/dashboard', [DashboardController::class, 'index'])->name('representative.dashboard');
    
    // Profile
    Route::get('/representative/profile', [\App\Http\Controllers\Representatives\ProfileController::class, 'index'])->name('representative.profile');
    
    // Account
    Route::prefix('representative/account')->name('representative.account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::post('/withdraw', [AccountController::class, 'withdraw'])->name('withdraw');
        Route::get('/transactions', [AccountController::class, 'transactions'])->name('transactions');
    });

    // Products
    Route::prefix('representative/products')->name('representative.products.')->group(function () {
        Route::get('/{product}', [\App\Http\Controllers\Representatives\ProductController::class, 'show'])->name('show');
        Route::get('/{product}/recommendations', [\App\Http\Controllers\Representatives\ProductController::class, 'getRecommendations'])->name('recommendations');
    });

    // Orders
    Route::prefix('representative/orders')->name('representative.orders.')->group(function () {
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/calculate-commission', [OrderController::class, 'calculateCommission'])->name('calculate-commission');
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });
});

