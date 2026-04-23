<?php

namespace App\Integrations\Marketplace;

use App\Models\ChannelProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PttAdapter implements MarketplaceInterface
{
    protected array $config;

    protected string $baseUrl = 'https://integration-api.pttavm.com/api/v1';

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    protected function client()
    {
        return Http::withHeaders([
            'Api-Key' => $this->config['api_key'],
            'Access-Token' => $this->config['api_secret'],
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->timeout(30);
    }

    public function createProduct(ChannelProduct $channelProduct): bool
    {
        // Not implemented yet
        return false;
    }

    public function updateProduct(ChannelProduct $channelProduct): bool
    {
        // Not implemented yet
        return false;
    }

    public function syncStock(ChannelProduct $channelProduct): bool
    {
        // Not implemented yet
        return false;
    }

    public function syncPrice(ChannelProduct $channelProduct): bool
    {
        // Not implemented yet
        return false;
    }

    public function fetchOrders(int $page = 0, int $size = 50): Collection
    {
        try {
            $startDate = now()->subDays(30)->format('Y-m-d');
            $endDate = now()->format('Y-m-d');

            $response = Http::withHeaders([
                'Api-Key' => $this->config['api_key'],
                'access-token' => $this->config['api_secret'],
                'X-Correlation-Id' => (string) \Illuminate\Support\Str::uuid(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->get("{$this->baseUrl}/orders/search", [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'isActiveOrders' => 'false'
            ]);

            if ($response->successful()) {
                return collect($response->json() ?? []);
            }

            Log::error("PTT AVM Fetch Orders Error", [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
                'url' => "{$this->baseUrl}/orders/search"
            ]);
            
            return collect([]);
        } catch (\Exception $e) {
            Log::error("PTT AVM Fetch Orders Exception", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }

    public function fetchProducts(int $page = 0, int $size = 50, bool $approved = true): Collection
    {
        return collect([]);
    }

    public function getIdentifier(): string
    {
        return 'ptt';
    }

    public function testConnection(): bool
    {
        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->config['api_key'],
                'access-token' => $this->config['api_secret'],
                'X-Correlation-Id' => (string) \Illuminate\Support\Str::uuid(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->get("{$this->baseUrl}/shipping/cargo-profiles");
            
            if (!$response->successful()) {
                Log::error("PTT AVM API Connection Test Failed", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("PTT AVM API Exception", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
