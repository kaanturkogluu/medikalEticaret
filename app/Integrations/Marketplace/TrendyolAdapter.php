<?php

namespace App\Integrations\Marketplace;

use App\Models\ChannelProduct;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrendyolAdapter implements MarketplaceInterface
{
    protected array $config;
    protected string $baseUrl = 'https://api.trendyol.com/sapigw';

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Common request helper to handle auth, user-agent and logging.
     */
    protected function request(string $method, string $endpoint, array $data = []): Response
    {
        $supplierId = $this->config['supplier_id'];
        $url = "{$this->baseUrl}/suppliers/{$supplierId}/{$endpoint}";
        
        $userAgent = "{$supplierId} - AntigravitySelfIntegrated";

        $request = Http::withBasicAuth($this->config['api_key'], $this->config['api_secret'])
            ->withHeaders(['User-Agent' => $userAgent])
            ->timeout(30)
            ->retry(3, 100);

        Log::info("Trendyol API Request: {$method} {$url}", ['data' => $data]);

        $response = match (strtolower($method)) {
            'post' => $request->post($url, $data),
            'put' => $request->put($url, $data),
            'get' => $request->get($url, $data),
            default => throw new \Exception("Unsupported HTTP method: {$method}"),
        };

        if (!$response->successful()) {
            Log::error("Trendyol API Error: {$response->status()}", [
                'endpoint' => $endpoint,
                'body' => $response->body(),
                'payload' => $data
            ]);
        } else {
            Log::debug("Trendyol API Response: {$response->status()}", ['body' => $response->json()]);
        }

        return $response;
    }

    public function createProduct(ChannelProduct $channelProduct): bool
    {
        $payload = [
            'items' => [
                [
                    'barcode' => $channelProduct->product->sku,
                    'title' => $channelProduct->product->name,
                    'productMainId' => $channelProduct->product->sku, // Often same as SKU
                    'brandId' => (int) $this->getMarketplaceBrandId($channelProduct->product->brand_id),
                    'categoryId' => (int) $this->getMarketplaceCategoryId($channelProduct->product->category_id),
                    'quantity' => $channelProduct->stock ?? $channelProduct->product->stock,
                    'stockCode' => $channelProduct->product->sku,
                    'dimensionalWeight' => 1, // Default or map from DB
                    'description' => $channelProduct->product->description ?? $channelProduct->product->name,
                    'currencyType' => 'TRY',
                    'listPrice' => (double) ($channelProduct->price ?? $channelProduct->product->price),
                    'salePrice' => (double) ($channelProduct->price ?? $channelProduct->product->price),
                    'cargoCompanyId' => 1, // Map from config/DB
                    'images' => [
                        ['url' => 'https://picsum.photos/800/800'] // Placeholder logic
                    ],
                    'attributes' => [] // Required attributes logic should be here
                ]
            ]
        ];

        $response = $this->request('POST', 'v2/products', $payload);

        if ($response->successful()) {
            $batchRequestId = $response->json()['batchRequestId'] ?? null;
            // Update channel_product extra info
            $channelProduct->update([
                'extra' => array_merge($channelProduct->extra ?? [], ['batchRequestId' => $batchRequestId])
            ]);
            return true;
        }

        return false;
    }

    public function updateProduct(ChannelProduct $channelProduct): bool
    {
        // Trendyol v2/products handles both create and update depending on barcode match
        return $this->createProduct($channelProduct);
    }

    public function syncStock(ChannelProduct $channelProduct): bool
    {
        $payload = [
            'items' => [
                [
                    'barcode' => $channelProduct->product->sku,
                    'quantity' => $channelProduct->stock ?? $channelProduct->product->stock
                ]
            ]
        ];

        $response = $this->request('POST', 'products/price-and-inventory', $payload);

        return $response->successful();
    }

    public function syncPrice(ChannelProduct $channelProduct): bool
    {
        $payload = [
            'items' => [
                [
                    'barcode' => $channelProduct->product->sku,
                    'listPrice' => (double) ($channelProduct->price ?? $channelProduct->product->price),
                    'salePrice' => (double) ($channelProduct->price ?? $channelProduct->product->price)
                ]
            ]
        ];

        $response = $this->request('POST', 'products/price-and-inventory', $payload);

        return $response->successful();
    }

    public function fetchOrders(int $page = 0, int $size = 50): Collection
    {
        $params = [
            'page' => $page,
            'size' => $size,
            'orderByDirection' => 'DESC'
        ];

        $response = $this->request('GET', 'orders', $params);

        if ($response->successful()) {
            return collect($response->json()['content'] ?? []);
        }

        return collect([]);
    }

    public function getIdentifier(): string
    {
        return 'trendyol';
    }

    protected function getMarketplaceBrandId(?int $brandId): ?int
    {
        if (!$brandId) return null;
        return \App\Models\ChannelBrand::where('brand_id', $brandId)
            ->whereHas('channel', fn($q) => $q->where('slug', 'trendyol'))
            ->value('external_brand_id') ?? 1000; // 1000 is often 'Other' in Trendyol
    }

    protected function getMarketplaceCategoryId(?int $categoryId): ?int
    {
        if (!$categoryId) return null;
        return \App\Models\ChannelCategory::where('category_id', $categoryId)
            ->whereHas('channel', fn($q) => $q->where('slug', 'trendyol'))
            ->value('external_category_id');
    }
}
