<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Carbon\Carbon::setLocale(config('app.locale'));
        setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'turkish');
        
        \Illuminate\Pagination\Paginator::useTailwind();

        view()->composer(['layouts.app', 'home'], function ($view) {
            $categories = \App\Models\Category::whereHas('products', function($q) {
                $q->where('active', true)->where('stock', '>', 0);
            })->withCount(['products' => function($q) {
                $q->where('active', true)->where('stock', '>', 0);
            }])->get();

            $navbarCategories = \App\Models\Category::where('is_navbar', true)
                ->where('active', true)
                ->orderBy('name')
                ->get();

            $view->with(compact('categories', 'navbarCategories'));
        });
    }
}
