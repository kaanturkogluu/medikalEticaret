<?php

namespace App\Integrations\Marketplace;

use App\Models\ChannelProduct;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N11Adapter implements MarketplaceInterface
{
    protected array $config;

    protected string $baseUrl = 'https://api.n11.com';

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Get HTTP client instance configured for N11 API
     */
    protected function client()
    {
        return Http::withHeaders([
            'appkey' => $this->config['api_key'],
            'appsecret' => $this->config['api_secret'],
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->timeout(30);
    }

    /**
     * Common request helper
     */
    protected function request(string $method, string $endpoint, array $data = []): Response
    {
        $url = "{$this->baseUrl}/{$endpoint}";

        Log::info("N11 API Request [{$method}] [{$url}]: " . json_encode($data));

        $request = $this->client();

        $response = match (strtolower($method)) {
            'post' => $request->post($url, $data),
            'put' => $request->put($url, $data),
            'get' => $request->get($url, $data),
            default => throw new \Exception("Unsupported HTTP method: {$method}"),
        };

        Log::info("N11 API Response [{$url}]: " . $response->body());

        if (!$response->successful()) {
            Log::error("N11 API Error [{$url}]: " . $response->body());
        }

        return $response;
    }

    public function createProduct(ChannelProduct $channelProduct): bool
    {
        // Placeholder for future implementation
        return false;
    }

    public function updateProduct(ChannelProduct $channelProduct): bool
    {
        // Placeholder for future implementation
        return false;
    }

    public function syncStock(ChannelProduct $channelProduct): bool
    {
        // Placeholder for future implementation
        return false;
    }

    public function syncPrice(ChannelProduct $channelProduct): bool
    {
        // Placeholder for future implementation
        return false;
    }

    public function fetchOrders(int $page = 0, int $size = 50): Collection
    {
        // N11 Rest API uses timestamps in milliseconds for dates.
        // If not specified, default to last 15 days as per documentation.
        $startDate = now()->subDays(15)->timestamp * 1000;
        $endDate = now()->timestamp * 1000;

        $endpoint = "rest/delivery/v1/shipmentPackages";

        $params = [
            'startDate' => $startDate,
            'endDate' => $endDate,

            'page' => $page,
            'size' => $size,
            'orderByDirection' => 'ASC'
        ];

        $response = $this->request('GET', $endpoint, $params);


        if ($response->successful()) {
            $data = $response->json();
            return collect($data['content'] ?? []);
        }

        return collect([]);
    }

    public function fetchProducts(int $page = 0, int $size = 50, bool $approved = true): Collection
    {
        $endpoint = "ms/product-query";

        $params = [
            'page' => $page,
            'size' => $size,
            'productStatus' => $approved ? 'Active' : 'InApproval'
        ];

        $response = $this->request('GET', $endpoint, $params);

        if ($response->successful()) {
            $data = $response->json();
            return collect($data['content'] ?? []);
        }

        return collect([]);
    }

    public function getIdentifier(): string
    {
        return 'n11';
    }

    public function testConnection(): bool
    {
        try {
            $response = $this->fetchOrders(0, 1);
            return true;
        } catch (\Exception $e) {
            Log::error("N11 [TEST_CONNECTION] [EXCEPTION]: " . $e->getMessage());
            return false;
        }
    }
}

