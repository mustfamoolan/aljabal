<?php

use App\Http\Controllers\Admin\AccountManagementController;
use App\Http\Controllers\Admin\EmployeeTypeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RepresentativeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WithdrawalRequestController;
use App\Http\Controllers\Admin\WithdrawalSettingsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Representatives\Auth\LoginController as RepresentativeLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoutingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__ . '/auth.php';

// Representative login routes (no authentication required)
Route::get('/representative/login', [RepresentativeLoginController::class, 'create'])
    ->middleware('guest:representative')
    ->name('representative.login');

Route::post('/representative/login', [RepresentativeLoginController::class, 'store'])
    ->middleware('guest:representative');

Route::post('/representative/logout', [RepresentativeLoginController::class, 'destroy'])
    ->middleware('auth:representative')
    ->name('representative.logout');

// Refresh CSRF token endpoint
Route::get('/refresh-csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->middleware('web');

// Storage files route (for when symbolic link doesn't work)
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);

    if (!file_exists($filePath)) {
        abort(404);
    }

    $mimeType = mime_content_type($filePath);

    return response()->file($filePath, [
        'Content-Type' => $mimeType,
    ]);
})->where('path', '.*')->name('storage.file');

// Redirect root to admin login if not authenticated
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home.index');
    }
    if (auth()->guard('representative')->check()) {
        return redirect('/representative/dashboard');
    }
    return redirect('/admin/login');
});

// Exclude static files and system paths from view routing
Route::get('/service-worker.js', function () {
    abort(404);
});
Route::get('/manifest.json', function () {
    abort(404);
});
Route::get('/robots.txt', function () {
    abort(404);
});

// Exclude .well-known paths (Chrome DevTools, etc.)
Route::any('/.well-known/{path}', function () {
    abort(404);
})->where('path', '.*');

Route::group(['prefix' => '/', 'middleware' => ['auth', 'admin']], function () {
    // Specific routes for roles and permissions pages
    Route::get('users/role', [RoleController::class, 'index'])->name('users.role.list');
    Route::get('users/role/list', [RoleController::class, 'index'])->name('users.role.list.alt');
    Route::get('users/role/create', [RoleController::class, 'create'])->name('users.role.create');
    Route::get('users/role/{role}/edit', [RoleController::class, 'edit'])->name('users.role.edit');

    // Note: POST/PUT/DELETE routes are handled in routes/admin.php under /admin/roles

    Route::get('users/pages-permission', [PermissionController::class, 'index'])->name('users.pages-permission');
    Route::get('users/permissions/create', [PermissionController::class, 'create'])->name('users.permissions.create');
    Route::get('users/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('users.permissions.edit');

    Route::get('users/users/list', [UserController::class, 'index'])->name('users.users.list');

    // Users routes (admin/users)
    Route::get('admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::get('admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::get('admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');

    // Employee Types routes
    Route::get('admin/employee-types', [EmployeeTypeController::class, 'index'])->name('admin.employee-types.index');
    Route::get('admin/employee-types/create', [EmployeeTypeController::class, 'create'])->name('admin.employee-types.create');
    Route::get('admin/employee-types/{employee_type}/edit', [EmployeeTypeController::class, 'edit'])->name('admin.employee-types.edit');

    // Representatives routes
    Route::get('admin/representatives', [RepresentativeController::class, 'index'])->name('admin.representatives.index');
    Route::get('admin/representatives/create', [RepresentativeController::class, 'create'])->name('admin.representatives.create');
    Route::get('admin/representatives/{representative}', [RepresentativeController::class, 'show'])->name('admin.representatives.show');
    Route::get('admin/representatives/{representative}/edit', [RepresentativeController::class, 'edit'])->name('admin.representatives.edit');

    // Inventory routes
    Route::get('admin/inventory/products', [ProductController::class, 'index'])->name('inventory.products.index');
    Route::get('admin/inventory/products/grid', [ProductController::class, 'grid'])->name('inventory.products.grid');
    Route::get('admin/inventory/products/create', [ProductController::class, 'create'])->name('inventory.products.create');
    Route::get('admin/inventory/products/low-stock', [ProductController::class, 'getLowStock'])->name('inventory.products.low-stock');
    Route::get('admin/inventory/products/{product}', [ProductController::class, 'show'])->name('inventory.products.show');
    Route::get('admin/inventory/products/{product}/edit', [ProductController::class, 'edit'])->name('inventory.products.edit');
    Route::get('admin/inventory/products/{product}/add-quantity', [ProductController::class, 'addQuantity'])->name('inventory.products.add-quantity');

    Route::get('admin/inventory/categories', [CategoryController::class, 'index'])->name('inventory.categories.index');
    Route::get('admin/inventory/categories/create', [CategoryController::class, 'create'])->name('inventory.categories.create');
    Route::get('admin/inventory/categories/{category}/subcategories', [CategoryController::class, 'getSubcategories'])->name('inventory.categories.subcategories');
    Route::get('admin/inventory/categories/{category}', [CategoryController::class, 'show'])->name('inventory.categories.show');
    Route::get('admin/inventory/categories/{category}/edit', [CategoryController::class, 'edit'])->name('inventory.categories.edit');

    Route::get('admin/inventory/suppliers', [SupplierController::class, 'index'])->name('inventory.suppliers.index');
    Route::get('admin/inventory/suppliers/create', [SupplierController::class, 'create'])->name('inventory.suppliers.create');
    Route::get('admin/inventory/suppliers/{supplier}', [SupplierController::class, 'show'])->name('inventory.suppliers.show');
    Route::get('admin/inventory/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('inventory.suppliers.edit');

    // Account Management routes
    Route::get('admin/accounts', [AccountManagementController::class, 'index'])->name('admin.accounts.index');
    Route::get('admin/accounts/{representative}', [AccountManagementController::class, 'show'])->name('admin.accounts.show');

    // Withdrawal Requests routes
    Route::get('admin/withdrawals', [WithdrawalRequestController::class, 'index'])->name('admin.withdrawals.index');
    Route::get('admin/withdrawals/{withdrawalRequest}', [WithdrawalRequestController::class, 'show'])->name('admin.withdrawals.show');

    // Withdrawal Settings routes
    Route::get('admin/settings/withdrawal', [WithdrawalSettingsController::class, 'index'])->name('admin.settings.withdrawal.index');

    // General Settings routes are handled in routes/admin.php

    // Orders routes (admin)
    Route::get('admin/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('admin/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
    Route::get('admin/orders/{order}/edit', [\App\Http\Controllers\Admin\OrderController::class, 'edit'])->name('admin.orders.edit');

    // Order Commission Settings routes
    Route::get('admin/settings/order-commission', [\App\Http\Controllers\Admin\OrderCommissionSettingsController::class, 'index'])->name('admin.settings.order-commission.index');

    // Tags routes (GET only, POST/PUT/DELETE in admin.php)
    Route::get('admin/tags', [\App\Http\Controllers\Admin\TagController::class, 'index'])->name('admin.tags.index');
    Route::get('admin/tags/create', [\App\Http\Controllers\Admin\TagController::class, 'create'])->name('admin.tags.create');
    Route::get('admin/tags/{tag}/edit', [\App\Http\Controllers\Admin\TagController::class, 'edit'])->name('admin.tags.edit');

    // Notifications routes
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/{notification}/unread', [NotificationController::class, 'markAsUnread'])->name('notifications.unread');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Home page route
    Route::get('home', [HomeController::class, 'index'])->name('home.index');

    // POST/PUT/DELETE routes are handled in routes/admin.php

    // Exclude representative and admin routes from catch-all routing (admin routes are handled in routes/admin.php)
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])
        ->where('first', '^(?!representative|admin).*')
        ->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])
        ->where('first', '^(?!representative|admin).*')
        ->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])
        ->where('any', '^(?!api|service-worker|manifest|robots|representative|admin).*')
        ->name('any');
});

