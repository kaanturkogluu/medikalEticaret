<?php

namespace App\Services;

use App\Jobs\SyncPriceJob;
use App\Jobs\SyncProductJob;
use App\Jobs\SyncStockJob;
use App\Models\ChannelProduct;
use App\Models\Product;

class SyncService
{
    public function syncProductStock(Product $product): void
    {
        $product->channelProducts()->where('sync_status', 'synced')->each(function (ChannelProduct $cp) {
            SyncStockJob::dispatch($cp);
        });
    }

    public function syncProductPrice(Product $product): void
    {
        $product->channelProducts()->where('sync_status', 'synced')->each(function (ChannelProduct $cp) {
            SyncPriceJob::dispatch($cp);
        });
    }

    public function syncProductDirectly(ChannelProduct $channelProduct): void
    {
        SyncProductJob::dispatch($channelProduct);
    }
}
