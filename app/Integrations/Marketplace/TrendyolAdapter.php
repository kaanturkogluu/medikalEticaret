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

    protected string $baseUrl = 'https://api.trendyol.com/sapigw/suppliers';

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Common request helper to handle auth, user-agent and logging.
     * Incorporates rate-limiting protection and standard headers.
     */
    protected function request(string $method, string $endpoint, array $data = []): Response
    {
        $supplierId = $this->config['supplier_id'];
        $url = "{$this->baseUrl}/{$supplierId}/{$endpoint}";

        $userAgent = "{$supplierId} - SelfIntegration";

        Log::info("TRENDYOL [REQUEST_AUTH_DEBUG]", [
            'api_key' => $this->config['api_key'],
            'supplier_id' => $supplierId,
            'api_secret' => $this->config['api_secret'],
            'url' => $url
        ]);

        $request = Http::withBasicAuth($this->config['api_key'], $this->config['api_secret'])
            ->withHeaders([
                'User-Agent' => $userAgent,
                'Accept' => 'application/json'
            ])
            ->timeout(30)
            ->retry(3, 1000); // Retry with 1s delay

        $response = match (strtolower($method)) {
            'post' => $request->post($url, $data),
            'put' => $request->put($url, $data),
            'get' => $request->get($url, $data),
            default => throw new \Exception("Unsupported HTTP method: {$method}"),
        };

        if (!$response->successful()) {
            Log::error("Trendyol API Error [{$url}]: " . $response->body());
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
                    'productMainId' => $channelProduct->product->sku,
                    'brandId' => (int) $this->getMarketplaceBrandId($channelProduct->product->brand_id),
                    'categoryId' => (int) $this->getMarketplaceCategoryId($channelProduct->product->category_id),
                    'quantity' => $channelProduct->stock ?? $channelProduct->product->stock,
                    'stockCode' => $channelProduct->product->sku,
                    'dimensionalWeight' => 1,
                    'description' => $channelProduct->product->description ?? $channelProduct->product->name,
                    'currencyType' => 'TRY',
                    'listPrice' => (double) ($channelProduct->price ?? $channelProduct->product->price),
                    'salePrice' => (double) ($channelProduct->price ?? $channelProduct->product->price),
                    'cargoCompanyId' => 1,
                    'images' => [
                        ['url' => 'https://picsum.photos/800/800']
                    ],
                    'attributes' => []
                ]
            ]
        ];

        $response = $this->request('POST', 'v2/products', $payload);

        if ($response->successful()) {
            $batchRequestId = $response->json()['batchRequestId'] ?? null;
            $channelProduct->update([
                'extra' => array_merge($channelProduct->extra ?? [], ['batchRequestId' => $batchRequestId])
            ]);
            return true;
        }

        return false;
    }

    public function updateProduct(ChannelProduct $channelProduct): bool
    {
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
        $supplierId = $this->config['supplier_id'];
        $url = "https://apigw.trendyol.com/integration/order/sellers/{$supplierId}/orders";

        $response = Http::withBasicAuth($this->config['api_key'], $this->config['api_secret'])
            ->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => "{$supplierId} - SelfIntegration"
            ])
            ->timeout(30)
            ->get($url, [
                'page' => $page,
                'size' => $size,
                'orderByDirection' => 'DESC'
            ]);

        if ($response->successful()) {
            return collect($response->json()['content'] ?? []);
        }

        return collect([]);
    }

    public function fetchProducts(int $page = 0, int $size = 50, bool $approved = true): Collection
    {
        $supplierId = $this->config['supplier_id'];
        $url = "https://apigw.trendyol.com/integration/product/sellers/{$supplierId}/products";

        Log::info("TRENDYOL [FETCH_PRODUCTS_AUTH_DEBUG]", [
            'api_key' => $this->config['api_key'],
            'supplier_id' => $supplierId,
            'url' => $url
        ]);

        $response = Http::withBasicAuth($this->config['api_key'], $this->config['api_secret'])
            ->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => "{$supplierId} - SelfIntegration"
            ])
            ->timeout(30)
            ->get($url, [
                'page' => $page,
                'size' => $size
            ]);

        if ($response->successful()) {
            return collect($response->json()['content'] ?? []);
        }

        return collect([]);
    }

    public function getIdentifier(): string
    {
        return 'trendyol';
    }

    public function testConnection(): bool
    {
        try {
            $response = $this->request('GET', 'orders', ['size' => 1]);

            Log::info("TRENDYOL [TEST_CONNECTION] Status: " . $response->status() . " Response: " . $response->body());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("TRENDYOL [TEST_CONNECTION] [EXCEPTION]: " . $e->getMessage());
            return false;
        }
    }

    protected function getMarketplaceBrandId(?int $brandId): ?int
    {
        if (!$brandId)
            return null;
        return \App\Models\ChannelBrand::where('brand_id', $brandId)
            ->whereHas('channel', fn($q) => $q->where('slug', 'trendyol'))
            ->value('external_brand_id') ?? 1000;
    }

    protected function getMarketplaceCategoryId(?int $categoryId): ?int
    {
        if (!$categoryId)
            return null;
        return \App\Models\ChannelCategory::where('category_id', $categoryId)
            ->whereHas('channel', fn($q) => $q->where('slug', 'trendyol'))
            ->value('external_category_id');
    }
}
