<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminStandController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\CanteenController;
use App\Http\Controllers\MidtransCallbackController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CanteenController::class, 'index'])->name('home');
Route::get('/menu', [CanteenController::class, 'menu'])->name('menu');

Route::get('/notifikasi', function () {
    return view('welcome', ['activePage' => 'notifications']);
})->name('notifications');

Route::get('/kontak', function () {
    return view('welcome', ['activePage' => 'messages']);
})->name('contact');

Route::get('/favorit', function () {
    return view('welcome', ['activePage' => 'bookmarks']);
})->name('favorites');

Route::get('/info', function () {
    return view('welcome', ['activePage' => 'loop']);
})->name('info');

// Order endpoints (Cart checkout & status tracking)
Route::post('/api/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/api/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/api/orders/{orderNumber}/payment-status', [OrderController::class, 'paymentStatus'])->name('orders.payment-status');
Route::post('/api/orders/{orderNumber}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
Route::post('/api/orders/{orderNumber}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

// Midtrans Payment Gateway Webhook Notification
Route::post('/api/midtrans/notification', [MidtransCallbackController::class, 'handle'])->name('midtrans.notification');

// ===============================================
// ADMIN MANAGEMENT ROUTES (/admin)
// ===============================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/quick-login', [AdminAuthController::class, 'quickLogin'])->name('quick-login');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected Admin Panel
    Route::middleware('admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Orders Management
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::match(['put', 'patch'], '/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::get('/orders/{order}/print', [AdminOrderController::class, 'printReceipt'])->name('orders.print');
        Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

        // Menus Management
        Route::get('/menus', [AdminMenuController::class, 'index'])->name('menus.index');
        Route::post('/menus', [AdminMenuController::class, 'store'])->name('menus.store');
        Route::put('/menus/{menu}', [AdminMenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [AdminMenuController::class, 'destroy'])->name('menus.destroy');
        Route::post('/menus/{menu}/toggle-available', [AdminMenuController::class, 'toggleAvailability'])->name('menus.toggle-available');
        Route::post('/menus/{menu}/badge/{badgeType}', [AdminMenuController::class, 'toggleBadge'])->name('menus.toggle-badge');

        // Stands Management
        Route::get('/stands', [AdminStandController::class, 'index'])->name('stands.index');
        Route::post('/stands', [AdminStandController::class, 'store'])->name('stands.store');
        Route::put('/stands/{stand}', [AdminStandController::class, 'update'])->name('stands.update');
        Route::delete('/stands/{stand}', [AdminStandController::class, 'destroy'])->name('stands.destroy');
        Route::post('/stands/{stand}/toggle-open', [AdminStandController::class, 'toggleOpen'])->name('stands.toggle-open');

        // Categories Management
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // Transactions & Financials
        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
        Route::post('/transactions/{payment}/mark-paid', [AdminTransactionController::class, 'markPaid'])->name('transactions.mark-paid');

        // Users Management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
