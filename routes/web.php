<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Public Routes
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/product/{product}', [\App\Http\Controllers\HomeController::class, 'show'])->name('product.show');
Route::get('/favorites', [\App\Http\Controllers\HomeController::class, 'favorites'])->name('favorites');

Route::get('/iletisim', function () {
    return view('contact');
})->name('contact');

Route::get('/p/{slug}', [\App\Http\Controllers\HomeController::class, 'page'])->name('page.show');

// Authentication Routes (Guest)
Route::middleware('guest')->group(function () {
    // User login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    // Admin login
    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    
    // Auth logic
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
    Route::post('/admin/login', [LoginController::class, 'authenticate']);

    // Registration
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

    // Social Auth
    Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialController::class, 'handleGoogleCallback']);
});

// Email verification (accessible even when logged in, before verified)
Route::get('/verify-email', [\App\Http\Controllers\Auth\RegisterController::class, 'showVerifyForm'])->name('verify.form');
Route::post('/verify-email', [\App\Http\Controllers\Auth\RegisterController::class, 'verify'])->name('verify.submit');
Route::post('/verify-email/resend', [\App\Http\Controllers\Auth\RegisterController::class, 'resend'])->name('verify.resend');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// User Panel Routes
Route::middleware(['auth', 'user'])->prefix('hesabim')->name('user.')->group(function () {
    Route::get('/', [\App\Http\Controllers\UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/siparislerim', [\App\Http\Controllers\UserController::class, 'orders'])->name('orders');
    Route::get('/siparislerim/{order}', [\App\Http\Controllers\UserController::class, 'orderShow'])->name('orders.show');
    Route::get('/adreslerim', [\App\Http\Controllers\UserController::class, 'addresses'])->name('addresses');
    Route::post('/adreslerim', [\App\Http\Controllers\UserController::class, 'addressStore'])->name('addresses.store');
    Route::delete('/adreslerim/{address}', [\App\Http\Controllers\UserController::class, 'addressDestroy'])->name('addresses.destroy');
    Route::get('/bilgilerim', [\App\Http\Controllers\UserController::class, 'profile'])->name('profile');
    Route::post('/bilgilerim', [\App\Http\Controllers\UserController::class, 'profileUpdate'])->name('profile.update');
    Route::post('/sifre-guncelle', [\App\Http\Controllers\UserController::class, 'passwordUpdate'])->name('password.update');
});

// Admin Routes (Protected)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products');
    Route::get('/products/{product}/edit', [\App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('admin.products.update');
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
    Route::get('/appearance', [\App\Http\Controllers\Admin\AppearanceController::class, 'index'])->name('admin.appearance');
    Route::get('/appearance/contact', [\App\Http\Controllers\Admin\AppearanceController::class, 'contact'])->name('admin.appearance.contact');
    Route::post('/appearance/contact', [\App\Http\Controllers\Admin\AppearanceController::class, 'updateContact'])->name('admin.appearance.contact.update');
    Route::get('/appearance/marketplaces', [\App\Http\Controllers\Admin\AppearanceController::class, 'marketplaces'])->name('admin.appearance.marketplaces');
    Route::post('/appearance/marketplaces', [\App\Http\Controllers\Admin\AppearanceController::class, 'updateMarketplaces'])->name('admin.appearance.marketplaces.update');
    Route::get('/appearance/social', [\App\Http\Controllers\Admin\AppearanceController::class, 'social'])->name('admin.appearance.social');
    Route::post('/appearance/social', [\App\Http\Controllers\Admin\AppearanceController::class, 'updateSocial'])->name('admin.appearance.social.update');
    Route::get('/appearance/general', [\App\Http\Controllers\Admin\AppearanceController::class, 'general'])->name('admin.appearance.general');
    Route::post('/appearance/general', [\App\Http\Controllers\Admin\AppearanceController::class, 'updateGeneral'])->name('admin.appearance.general.update');
    Route::get('/appearance/tab-switch', [\App\Http\Controllers\Admin\AppearanceController::class, 'tabSwitch'])->name('admin.appearance.tab_switch');
    Route::post('/appearance/tab-switch', [\App\Http\Controllers\Admin\AppearanceController::class, 'updateTabSwitch'])->name('admin.appearance.tab_switch.update');
    Route::group(['prefix' => 'appearance/banner', 'as' => 'admin.appearance.banner.'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BannerController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('store');
        Route::get('/{banner}/edit', [\App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('edit');
        Route::post('/{banner}/update', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('update');
        Route::delete('/{banner}/delete', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('destroy');
        Route::post('/{banner}/toggle', [\App\Http\Controllers\Admin\BannerController::class, 'toggle'])->name('toggle');
    });
    // Brand Management
    Route::group(['prefix' => 'brands', 'as' => 'admin.brands.'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\BrandController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BrandController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\BrandController::class, 'store'])->name('store');
        Route::get('/{brand}/edit', [\App\Http\Controllers\Admin\BrandController::class, 'edit'])->name('edit');
        Route::put('/{brand}/update', [\App\Http\Controllers\Admin\BrandController::class, 'update'])->name('update');
        Route::delete('/{brand}/delete', [\App\Http\Controllers\Admin\BrandController::class, 'destroy'])->name('destroy');
        Route::post('/{brand}/toggle', [\App\Http\Controllers\Admin\BrandController::class, 'toggleActive'])->name('toggle');
    });

    // Category Management
    Route::group(['prefix' => 'categories', 'as' => 'admin.categories.'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}/update', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}/delete', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{category}/toggle', [\App\Http\Controllers\Admin\CategoryController::class, 'toggleActive'])->name('toggle');
    });

    // Page Management (Agreements & Policies)
    Route::group(['prefix' => 'pages', 'as' => 'admin.pages.'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\PageController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\PageController::class, 'store'])->name('store');
        Route::get('/{page}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])->name('edit');
        Route::put('/{page}/update', [\App\Http\Controllers\Admin\PageController::class, 'update'])->name('update');
        Route::delete('/{page}/delete', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])->name('destroy');
        Route::post('/{page}/toggle', [\App\Http\Controllers\Admin\PageController::class, 'toggle'])->name('toggle');
    });

    Route::view('/settings', 'admin.settings')->name('admin.settings');
});
