<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Product Management
    Route::apiResource('products', ProductController::class);

    // Sync Management
    Route::post('sync/orders', [SyncController::class, 'syncOrders']);

    // Iyzico Callback (No session/CSRF handled here)
    Route::match(['get', 'post'], 'iyzico/callback', [\App\Http\Controllers\IyzicoController::class, 'callback'])->name('iyzico.callback');
});
