<?php

use App\Http\Controllers\Admin\AccountManagementController;
use App\Http\Controllers\Admin\EmployeeTypeController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderCommissionSettingsController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RepresentativeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WithdrawalRequestController;
use App\Http\Controllers\Admin\WithdrawalSettingsController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::post('/profile/update-image', [ProfileController::class, 'updateImage'])->name('admin.profile.update-image');

    // Users routes
    Route::resource('users', UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    Route::post('users/{user}/assign-role', [UserController::class, 'assignRole'])->name('admin.users.assign-role');
    Route::post('users/{user}/revoke-role', [UserController::class, 'revokeRole'])->name('admin.users.revoke-role');
    Route::post('users/{user}/assign-permission', [UserController::class, 'assignPermission'])->name('admin.users.assign-permission');
    Route::post('users/{user}/revoke-permission', [UserController::class, 'revokePermission'])->name('admin.users.revoke-permission');

    // Roles routes
    Route::resource('roles', RoleController::class)->names([
        'index' => 'admin.roles.index',
        'create' => 'admin.roles.create',
        'store' => 'admin.roles.store',
        'show' => 'admin.roles.show',
        'edit' => 'admin.roles.edit',
        'update' => 'admin.roles.update',
        'destroy' => 'admin.roles.destroy',
    ]);
    Route::post('roles/{role}/assign-permission', [RoleController::class, 'assignPermission'])->name('admin.roles.assign-permission');
    Route::post('roles/{role}/revoke-permission', [RoleController::class, 'revokePermission'])->name('admin.roles.revoke-permission');

    // Permissions routes
    Route::resource('permissions', PermissionController::class)->names([
        'index' => 'admin.permissions.index',
        'create' => 'admin.permissions.create',
        'store' => 'admin.permissions.store',
        'show' => 'admin.permissions.show',
        'edit' => 'admin.permissions.edit',
        'update' => 'admin.permissions.update',
        'destroy' => 'admin.permissions.destroy',
    ]);

    // Employee Types routes
    Route::resource('employee-types', EmployeeTypeController::class)->names([
        'index' => 'admin.employee-types.index',
        'create' => 'admin.employee-types.create',
        'store' => 'admin.employee-types.store',
        'show' => 'admin.employee-types.show',
        'edit' => 'admin.employee-types.edit',
        'update' => 'admin.employee-types.update',
        'destroy' => 'admin.employee-types.destroy',
    ]);
    Route::post('employee-types/{employee_type}/assign-role', [EmployeeTypeController::class, 'assignRole'])->name('admin.employee-types.assign-role');
    Route::post('employee-types/{employee_type}/revoke-role', [EmployeeTypeController::class, 'revokeRole'])->name('admin.employee-types.revoke-role');

    // Inventory routes - Note: GET routes are defined in web.php, POST/PUT/DELETE here
    Route::prefix('inventory')->group(function () {
        Route::post('products', [ProductController::class, 'store'])->name('admin.inventory.products.store');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('admin.inventory.products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('admin.inventory.products.destroy');
        Route::post('products/{product}/add-quantity', [ProductController::class, 'addQuantity'])->name('admin.inventory.products.add-quantity');

        Route::post('categories', [CategoryController::class, 'store'])->name('admin.inventory.categories.store');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('admin.inventory.categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('admin.inventory.categories.destroy');

        Route::post('suppliers', [SupplierController::class, 'store'])->name('admin.inventory.suppliers.store');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('admin.inventory.suppliers.update');
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('admin.inventory.suppliers.destroy');
    });

    // Representatives routes
    Route::resource('representatives', RepresentativeController::class)->names([
        'index' => 'admin.representatives.index',
        'create' => 'admin.representatives.create',
        'store' => 'admin.representatives.store',
        'show' => 'admin.representatives.show',
        'edit' => 'admin.representatives.edit',
        'update' => 'admin.representatives.update',
        'destroy' => 'admin.representatives.destroy',
    ]);

    // Account Management
    Route::prefix('accounts')->name('admin.accounts.')->group(function () {
        Route::get('/', [AccountManagementController::class, 'index'])->name('index');
        Route::get('/{representative}', [AccountManagementController::class, 'show'])->name('show');
        Route::post('/{representative}/add-balance', [AccountManagementController::class, 'addBalance'])->name('add-balance');
        Route::post('/{representative}/direct-withdraw', [AccountManagementController::class, 'directWithdraw'])->name('direct-withdraw');
        Route::get('/{representative}/transactions', [AccountManagementController::class, 'transactions'])->name('transactions');
    });

    // Withdrawal Requests
    Route::prefix('withdrawals')->name('admin.withdrawals.')->group(function () {
        Route::get('/', [WithdrawalRequestController::class, 'index'])->name('index');
        Route::get('/{withdrawalRequest}', [WithdrawalRequestController::class, 'show'])->name('show');
        Route::post('/{withdrawalRequest}/approve', [WithdrawalRequestController::class, 'approve'])->name('approve');
        Route::post('/{withdrawalRequest}/reject', [WithdrawalRequestController::class, 'reject'])->name('reject');
    });

    // Withdrawal Settings (kept for backward compatibility, redirects to general settings)
    Route::prefix('settings/withdrawal')->name('admin.settings.withdrawal.')->group(function () {
        Route::get('/', [WithdrawalSettingsController::class, 'index'])->name('index');
        Route::post('/general', [WithdrawalSettingsController::class, 'updateGeneral'])->name('update-general');
        Route::post('/exceptions', [WithdrawalSettingsController::class, 'createException'])->name('create-exception');
        Route::put('/exceptions/{withdrawalSetting}', [WithdrawalSettingsController::class, 'updateException'])->name('update-exception');
        Route::delete('/exceptions/{withdrawalSetting}', [WithdrawalSettingsController::class, 'deleteException'])->name('delete-exception');
    });

    // General Settings
    Route::prefix('general/settings')->name('general.settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\General\SettingsController::class, 'index'])->name('index');
        Route::post('/withdrawal/general', [\App\Http\Controllers\General\SettingsController::class, 'updateWithdrawalGeneral'])->name('withdrawal.update-general');
        Route::post('/withdrawal/exceptions', [\App\Http\Controllers\General\SettingsController::class, 'createWithdrawalException'])->name('withdrawal.create-exception');
        Route::put('/withdrawal/exceptions/{withdrawalSetting}', [\App\Http\Controllers\General\SettingsController::class, 'updateWithdrawalException'])->name('withdrawal.update-exception');
        Route::delete('/withdrawal/exceptions/{withdrawalSetting}', [\App\Http\Controllers\General\SettingsController::class, 'deleteWithdrawalException'])->name('withdrawal.delete-exception');
        Route::post('/gifts', [\App\Http\Controllers\General\SettingsController::class, 'storeGiftSetting'])->name('gifts.store');
        Route::put('/gifts/{giftSetting}', [\App\Http\Controllers\General\SettingsController::class, 'updateGiftSetting'])->name('gifts.update');
        Route::delete('/gifts/{giftSetting}', [\App\Http\Controllers\General\SettingsController::class, 'deleteGiftSetting'])->name('gifts.delete');
    });

    // Orders
    Route::prefix('orders')->name('admin.orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [OrderController::class, 'update'])->name('update');
    });

    // Tags Management
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class)->names([
        'index' => 'admin.tags.index',
        'create' => 'admin.tags.create',
        'store' => 'admin.tags.store',
        'edit' => 'admin.tags.edit',
        'update' => 'admin.tags.update',
        'destroy' => 'admin.tags.destroy',
    ]);
    Route::post('tags/check-duplicate', [\App\Http\Controllers\Admin\TagController::class, 'checkDuplicate'])->name('admin.tags.check-duplicate');

    // Order Commission Settings
    Route::prefix('settings/order-commission')->name('admin.settings.order-commission.')->group(function () {
        Route::get('/', [OrderCommissionSettingsController::class, 'index'])->name('index');
        Route::post('/', [OrderCommissionSettingsController::class, 'store'])->name('store');
        Route::put('/{id}', [OrderCommissionSettingsController::class, 'update'])->name('update');
        Route::delete('/{id}', [OrderCommissionSettingsController::class, 'destroy'])->name('destroy');
        
        // Exceptions
        Route::post('/exceptions', [OrderCommissionSettingsController::class, 'storeException'])->name('exceptions.store');
        Route::put('/exceptions/{id}', [OrderCommissionSettingsController::class, 'updateException'])->name('exceptions.update');
        Route::delete('/exceptions/{id}', [OrderCommissionSettingsController::class, 'destroyException'])->name('exceptions.destroy');
    });
});
