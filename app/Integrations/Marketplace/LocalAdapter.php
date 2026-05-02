<?php

namespace App\Integrations\Marketplace;

use App\Models\ChannelProduct;
use Illuminate\Support\Collection;

class LocalAdapter implements MarketplaceInterface
{
    protected array $config = [];

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function createProduct(ChannelProduct $channelProduct): bool
    {
        return true;
    }

    public function updateProduct(ChannelProduct $channelProduct): bool
    {
        return true;
    }

    public function syncStock(ChannelProduct $channelProduct): bool
    {
        return true;
    }

    public function syncPrice(ChannelProduct $channelProduct): bool
    {
        return true;
    }

    public function fetchOrders(int $page = 0, int $size = 50): Collection
    {
        return collect();
    }

    public function fetchProducts(int $page = 0, int $size = 50, bool $approved = true): Collection
    {
        return collect();
    }

    public function getIdentifier(): string
    {
        return 'website';
    }

    public function testConnection(): bool
    {
        return true;
    }
}
