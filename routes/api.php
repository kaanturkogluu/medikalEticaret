<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // --- Mobile App Authentication ---
    Route::post('/auth/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('/auth/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    
    // --- Mobile App Shop (Public) ---
    Route::get('/shop/banners', [\App\Http\Controllers\Api\V1\ShopController::class, 'banners']);
    Route::get('/shop/categories', [\App\Http\Controllers\Api\V1\ShopController::class, 'categories']);
    Route::get('/shop/products', [\App\Http\Controllers\Api\V1\ProductController::class, 'index']);
    Route::get('/shop/products/{slug}', [\App\Http\Controllers\Api\V1\ProductController::class, 'show']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
        Route::get('/auth/user', [\App\Http\Controllers\Api\V1\AuthController::class, 'user']);
        
        // --- Mobile App User & Checkout ---
        Route::get('/user/orders', [\App\Http\Controllers\Api\V1\UserController::class, 'orders']);
        Route::get('/user/addresses', [\App\Http\Controllers\Api\V1\UserController::class, 'addresses']);
        Route::post('/checkout', [\App\Http\Controllers\Api\V1\CheckoutController::class, 'store']);
    });

    // --- Admin Sync / Product Management (Legacy) ---
    Route::apiResource('products', ProductController::class);
    Route::post('sync/orders', [SyncController::class, 'syncOrders']);

    // --- Iyzico Callback ---
    Route::match(['get', 'post'], 'iyzico/callback', [\App\Http\Controllers\IyzicoController::class, 'callback'])->name('iyzico.callback');
});
