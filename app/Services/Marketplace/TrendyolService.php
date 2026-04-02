<?php

namespace App\Services\Marketplace;

use App\Integrations\Marketplace\MarketplaceManager;
use App\Models\Channel;
use App\Models\ChannelProduct;
use Illuminate\Support\Collection;

class TrendyolService
{
    public function __construct(protected MarketplaceManager $manager) {}

    /**
     * Get Trendyol adapter instance.
     */
    public function getAdapter(): \App\Integrations\Marketplace\MarketplaceInterface
    {
        $channel = Channel::where('slug', 'trendyol')->firstOrFail();
        return $this->manager->getAdapter($channel);
    }

    /**
     * Get orders for Trendyol with pagination.
     */
    public function getTrendyolOrders(int $page = 0): Collection
    {
        return $this->getAdapter()->fetchOrders($page);
    }

    /**
     * Sync stock for a specific channel product.
     */
    public function syncProductStockToTrendyol(ChannelProduct $channelProduct): bool
    {
        return $this->getAdapter()->syncStock($channelProduct);
    }

    /**
     * Fetch and print some info (example usage).
     */
    public function testIntegration(): void
    {
        $orders = $this->getTrendyolOrders();
        \Log::info("Sample Trendyol Orders Count: " . $orders->count());
    }
}
