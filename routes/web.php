<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::view('/products', 'admin.products')->name('admin.products');
    Route::view('/orders', 'admin.orders')->name('admin.orders');
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
