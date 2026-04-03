<?php

namespace App\Services;

use App\Integrations\Marketplace\MarketplaceManager;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Channel;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductSyncService
{
    public function __construct(protected MarketplaceManager $manager) {}

    /**
     * Synchronize all active channels products safely.
     */
    public function syncAllChannels(): void
    {
        Channel::where('active', true)->each(function (Channel $channel) {
            $this->syncChannelProducts($channel);
            // Saniye bazlı bekleme (Her kanal arası 2 sn mola)
            sleep(2);
        });
    }

    /**
     * Synchronize a specific marketplace channel's products with rate limiting.
     */
    public function syncChannelProducts(Channel $channel): void
    {
        Log::info("SYNC [PRODUCT] [START] Channel: {$channel->name}");
        
        try {
            $adapter = $this->manager->getAdapter($channel);
            
            $page = 0;
            $size = 50;
            $count = 0;
            $maxPages = 200; // Emniyet sınırı

            do {
                $externalProducts = $adapter->fetchProducts($page, $size);
                
                if ($externalProducts->isEmpty()) break;

                foreach ($externalProducts as $productData) {
                    $this->processProduct($channel, $productData);
                    $count++;
                }

                Log::info("SYNC [PRODUCT] [PAGE] Sayfa: {$page} tamamlandı.");
                
                $page++;

                // --- ÖNEMLİ: SPAM ÖNLEYİCİ MOLA ---
                // Trendyol Rate Limit Korunması için her sayfadan sonra 1 saniye bekliyoruz.
                usleep(1000000); 

                if ($page >= $maxPages) {
                    Log::warning("SYNC [PRODUCT] Maksimum sayfa sınırına (200) ulaşıldı, döngü kesildi.");
                    break;
                }

            } while ($externalProducts->count() === $size);

            Log::info("SYNC [PRODUCT] [BİTTİ] Toplam: {$count} ürün.");

        } catch (\Exception $e) {
            Log::error("SYNC [PRODUCT] [HATA] Kanal [{$channel->slug}]: " . $e->getMessage());
        }
    }

    protected function processProduct(Channel $channel, array $data): void
    {
        try {
            // Brand Normalization
            $brandId = null;
            $brandName = trim($data['brand'] ?? 'Unknown');
            $externalBrandId = $data['brandId'] ?? null;
            
            $brand = Brand::where('name', $brandName)->first();

            if ($brand) {
                $brandId = $brand->id;

                if ($externalBrandId) {
                    \App\Models\ChannelBrand::firstOrCreate(
                        ['channel_id' => $channel->id, 'external_brand_id' => (string)$externalBrandId],
                        ['brand_id' => $brand->id]
                    );
                }
            } else {
                Log::warning("Brand sync skipped: [{$brandName}] not found in database.");
            }

            // Category Mapping
            $categoryId = null;
            $externalCatId = $data['pimCategoryId'] ?? null;
            $categoryName = trim($data['categoryName'] ?? 'Genel');

            $category = Category::where('external_id', $externalCatId)
                ->orWhere(function($q) use ($categoryName) {
                    $q->where('name', $categoryName)
                      ->orWhere('slug', Str::slug($categoryName));
                })
                ->first();

            if ($category) {
                $categoryId = $category->id;
                
                if ($externalCatId) {
                    \App\Models\ChannelCategory::firstOrCreate(
                        ['channel_id' => $channel->id, 'external_category_id' => (string)$externalCatId],
                        ['category_id' => $category->id]
                    );
                }
            } else {
                Log::warning("Category sync skipped: [{$categoryName}] (External ID: {$externalCatId}) not found in database.");
            }

            // Product Upsert
            $pData = [
                'barcode' => $data['barcode'],
                'name' => $data['title'] ?? $data['productName'],
                'price' => (double) ($data['salePrice'] ?? $data['listPrice'] ?? 0),
                'stock' => (int) ($data['quantity'] ?? 0),
                'brand_id' => $brandId,
                'category_id' => $categoryId,
                'brand_name' => $brandName,
                'category_name' => $categoryName,
                'attributes' => $data['attributes'] ?? [],
                'raw_marketplace_data' => $data,
                'active' => $data['approved'] ?? true,
                'marketplace_status' => ($data['approved'] ?? true) ? 'approved' : 'rejected'
            ];

            $product = Product::updateOrCreate(['sku' => $data['stockCode']], $pData);

            // Channel Mapping
            $product->channels()->syncWithoutDetaching([
                $channel->id => [
                    'external_id' => $data['barcode'],
                    'price' => (double) ($data['salePrice'] ?? null),
                    'stock' => (int) ($data['quantity'] ?? null),
                    'sync_status' => 'synced',
                    'extra' => ['batchRequestId' => $data['batchRequestId'] ?? null]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("SYNC [PRODUCT] [DETAY-HATA] " . ($data['stockCode'] ?? 'Unknown') . ": " . $e->getMessage());
        }
    }
}
