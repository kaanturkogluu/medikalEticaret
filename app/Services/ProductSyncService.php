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
    protected ?array $categoryMap = null;
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
            // Brand Mapping
            $brandId = null;
            $brandName = $data['brand'] ?? null;
            if ($brandName) {
                $brandId = Brand::updateOrCreate(['name' => $brandName], ['active' => true])->id;
            }

            // Category Mapping
            $categoryId = null;
            $externalCatId = $data['pimCategoryId'] ?? null;
            $categoryName = trim($data['categoryName'] ?? 'Genel');

            if ($externalCatId) {
                $category = $this->getOrCreateCategoryByExternalId($externalCatId, $categoryName);
                $categoryId = $category->id;
            } elseif ($categoryName) {
                $category = Category::updateOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    ['name' => $categoryName, 'active' => true]
                );
                $categoryId = $category->id;
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

    /**
     * Get or create a category and all its parents recursively using JSON mapping.
     */
    protected function getOrCreateCategoryByExternalId($externalId, string $defaultName): Category
    {
        $externalId = (int)$externalId;

        // 1. Check DB first
        $category = Category::where('external_id', $externalId)->first();
        if ($category) return $category;

        // 2. Load and lookup in JSON
        $this->loadCategoryMap();
        $catData = $this->categoryMap[$externalId] ?? null;

        $actualName = $catData['name'] ?? $defaultName;
        $localParentId = null;

        if ($catData && !empty($catData['parentId'])) {
            $parentExternalId = (int)$catData['parentId'];
            $parentData = $this->categoryMap[$parentExternalId] ?? null;
            $parentName = $parentData['name'] ?? 'Genel Alt';
            
            $parentCategory = $this->getOrCreateCategoryByExternalId($parentExternalId, $parentName);
            $localParentId = $parentCategory->id;
        }

        return Category::create([
            'name' => $actualName,
            'slug' => Str::slug($actualName),
            'external_id' => $externalId,
            'parent_id' => $localParentId,
            'active' => true
        ]);
    }

    /**
     * Load the Trendyol category tree from JSON and flatten it for direct lookup.
     */
    protected function loadCategoryMap(): void
    {
        if ($this->categoryMap !== null) return;

        $path = base_path('trendyol-categories.json');
        if (!File::exists($path)) {
            Log::warning("Category mapping JSON not found: {$path}");
            $this->categoryMap = [];
            return;
        }

        try {
            $data = json_decode(File::get($path), true);
            $this->categoryMap = [];
            $this->flattenCategories($data['categories'] ?? []);
        } catch (\Exception $e) {
            Log::error("Failed to parse category JSON: " . $e->getMessage());
            $this->categoryMap = [];
        }
    }

    /**
     * Helper to flatten recursive subCategories into a flat ID-keyed map.
     */
    protected function flattenCategories(array $categories): void
    {
        foreach ($categories as $cat) {
            $this->categoryMap[(int)$cat['id']] = [
                'name' => $cat['name'],
                'parentId' => $cat['parentId'] ?? null
            ];
            if (!empty($cat['subCategories'])) {
                $this->flattenCategories($cat['subCategories']);
            }
        }
    }
}
