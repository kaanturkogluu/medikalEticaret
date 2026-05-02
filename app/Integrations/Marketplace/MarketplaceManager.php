<?php

namespace App\Integrations\Marketplace;

use App\Models\Channel;
use Exception;

class MarketplaceManager
{
    public function getAdapter(Channel $channel): MarketplaceInterface
    {
        $adapterClass = $this->resolveAdapterClass($channel->slug);

        if (!class_exists($adapterClass)) {
            throw new Exception("Marketplace adapter not found for: {$channel->slug}");
        }

        // Web sitesi kanalı için kimlik bilgisi kontrolüne gerek yok
        if ($channel->slug === 'website') {
            return (new $adapterClass())->setConfig([]);
        }

        $credential = $channel->credential;

        if (!$credential) {
            throw new Exception("Credentials missing for channel: {$channel->name}");
        }

        $config = [
            'api_key' => $credential->api_key,
            'api_secret' => $credential->api_secret,
            'supplier_id' => $credential->supplier_id,
        ];

        return (new $adapterClass())->setConfig($config);
    }

    protected function resolveAdapterClass(string $slug): string
    {
        return match ($slug) {
            'trendyol' => TrendyolAdapter::class,
            'n11' => N11Adapter::class,
            'ptt' => PttAdapter::class,
            'website' => LocalAdapter::class,
            // 'hepsiburada' => HepsiburadaAdapter::class,
            default => throw new Exception("Unknown marketplace: {$slug}"),
        };
    }
}
