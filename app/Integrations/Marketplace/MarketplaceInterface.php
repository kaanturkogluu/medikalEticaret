<?php

namespace App\Integrations\Marketplace;

use App\Models\ChannelProduct;
use Illuminate\Support\Collection;

interface MarketplaceInterface
{
    /**
     * Set the credentials/settings for this marketplace.
     */
    public function setConfig(array $config): self;

    /**
     * Create a product on the marketplace.
     */
    public function createProduct(ChannelProduct $channelProduct): bool;

    /**
     * Update an existing product on the marketplace.
     */
    public function updateProduct(ChannelProduct $channelProduct): bool;

    /**
     * Sync stock of a single product.
     */
    public function syncStock(ChannelProduct $channelProduct): bool;

    /**
     * Sync price of a single product.
     */
    public function syncPrice(ChannelProduct $channelProduct): bool;

    /**
     * Fetch new orders from the marketplace.
     * 
     * @param int $page Optional page number for pagination
     * @return Collection Collection of Order data arrays
     */
    public function fetchOrders(int $page = 0): Collection;

    /**
     * Get marketplace identifier slug (trendyol, hepsiburada, etc.)
     */
    public function getIdentifier(): string;
}
