<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Public Routes
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/product/{product}', [\App\Http\Controllers\HomeController::class, 'show'])->name('product.show');
Route::get('/favorites', [\App\Http\Controllers\HomeController::class, 'favorites'])->name('favorites');

// Authentication Routes (Guest)
Route::middleware('guest')->group(function () {
    // User login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    // Admin login
    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    
    // Auth logic
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
    Route::post('/admin/login', [LoginController::class, 'authenticate']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes (Protected)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products');
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders');
    Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
    Route::get('/test-products', [\App\Http\Controllers\Admin\OrderController::class, 'testProducts'])->name('admin.test-products');
    Route::view('/sync/stock', 'admin.sync.stock')->name('admin.sync.stock');
    Route::view('/sync/price', 'admin.sync.price')->name('admin.sync.price');
    
    // Marketplace Management
    Route::get('/marketplaces', [\App\Http\Controllers\Admin\ChannelController::class, 'index'])->name('admin.marketplaces');
    Route::get('/marketplaces/{channel}/edit', [\App\Http\Controllers\Admin\ChannelController::class, 'edit'])->name('admin.marketplaces.edit');
    Route::put('/marketplaces/{channel}', [\App\Http\Controllers\Admin\ChannelController::class, 'update'])->name('admin.marketplaces.update');
    Route::post('/marketplaces/{channel}/test', [\App\Http\Controllers\Admin\ChannelController::class, 'test'])->name('admin.marketplaces.test');

    Route::view('/logs', 'admin.logs')->name('admin.logs');
    Route::view('/settings', 'admin.settings')->name('admin.settings');
});
