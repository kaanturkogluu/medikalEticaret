<?php

namespace App\Providers;

use App\Integrations\Marketplace\MarketplaceManager;
use Illuminate\Support\ServiceProvider;

class MarketplaceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MarketplaceManager::class, function ($app) {
            return new MarketplaceManager();
        });
    }

    public function boot(): void
    {
        //
    }
}
