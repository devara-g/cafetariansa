<?php

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

// Midtrans Payment Gateway Webhook Notification
Route::post('/api/midtrans/notification', [MidtransCallbackController::class, 'handle'])->name('midtrans.notification');
