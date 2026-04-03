<?php

namespace App\Services;

use App\Integrations\Marketplace\TrendyolAdapter;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Channel;
use App\Models\Product;
use App\Models\ChannelProduct;
use App\Models\ChannelBrand;
use App\Models\ChannelCategory;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductImportService
{
    protected int $insertedCount = 0;
    protected int $updatedCount = 0;
    protected int $skippedCount = 0;
    protected array $errors = [];
    protected ?array $categoryMap = null;

    public function __construct(
        protected TrendyolAdapter $adapter
    ) {}

    /**
     * Start the import process for a specific channel (Trendyol, etc.)
     */
    public function importFromTrendyol(Channel $channel): array
    {
        $this->resetCounters();
        
        $credential = $channel->credential;
        $this->adapter->setConfig([
            'api_key' => $credential->api_key,
            'api_secret' => $credential->api_secret,
            'supplier_id' => $credential->supplier_id,
        ]);

        Log::info("SYNC [PRODUCT] [START] Channel: {$channel->slug}, Supplier: {$credential->supplier_id}");

        $page = 0;
        $size = 50;
        $hasMore = true;

        while ($hasMore) {
            try {
                $products = $this->adapter->fetchProducts($page, $size);

                if ($products->isEmpty()) {
                    $hasMore = false;
                    break;
                }

                foreach ($products as $productData) {
                    $this->processChannelProduct($channel, $productData);
                }

                $page++;
                if ($page >= 500 || $products->count() < $size) {
                    $hasMore = false;
                }

                usleep(300000); // 0.3 sec rate limit protection

            } catch (\Exception $e) {
                Log::error("SYNC [PRODUCT] [ERROR] Page {$page}: " . $e->getMessage());
                $this->errors[] = "Page {$page}: " . $e->getMessage();
                $hasMore = false;
            }
        }

        return $this->getSummary();
    }

    /**
     * Process a single product and map it to a specific channel.
     */
    protected function processChannelProduct(Channel $channel, array $data): void
    {
        $sku = $data['productMainId'] ?? $data['productCode'] ?? null;
        $externalId = (string) ($data['id'] ?? '');

        if (!$sku || !$externalId) {
            $this->skippedCount++;
            return;
        }

        // FILTERING RULES
        if (($data['approved'] ?? false) === false || ($data['blacklisted'] ?? false) === true) {
            $this->skippedCount++;
            return;
        }

        try {
            $isNew = false;
            DB::transaction(function () use ($channel, $data, $sku, $externalId, &$isNew) {
                // 1. GLOBAL PRODUCT UPSERT (Central Identity)
                $product = $this->upsertGlobalProduct($data, $sku);
                $isNew = $product->wasRecentlyCreated;

                // 2. BRAND & CATEGORY MAPPING (Normalize and Link)
                $this->syncChannelMappings($channel, $product, $data);

                // 3. CHANNEL-SPECIFIC PRODUCT UPSERT (The Bridge Table)
                $this->upsertChannelProduct($channel, $product, $data, $externalId);

                // 4. SYNC IMAGES & ATTRIBUTES
                $this->syncAttributes($product, $data['attributes'] ?? []);
                $this->syncImages($product, $data['images'] ?? []);
            });

            if ($isNew) {
                $this->insertedCount++;
            } else {
                $this->updatedCount++;
            }

            if (($this->insertedCount + $this->updatedCount) % 10 === 0) Log::info("Processed 10 more products...");
        } catch (\Exception $e) {
            $this->errors[] = "SKU {$sku}: " . $e->getMessage();
            Log::error("SYNC [PRODUCT] [FAIL] SKU: {$sku} - " . $e->getMessage());
        }
    }

    /**
     * Create or update the Global Product record.
     * Core rule: Do not overwrite certain global fields if they are manually edited (optional).
     */
    protected function upsertGlobalProduct(array $data, string $sku): Product
    {
        $salePrice = $data['salePrice'] ?? 0;
        $listPrice = $data['listPrice'] ?? 0;

        return Product::updateOrCreate(
            ['sku' => $sku],
            [
                'name' => $data['title'] ?? 'No Title',
                'description' => $data['description'] ?? '',
                'barcode' => $data['barcode'] ?? $sku,
                // Global price/stock can be kept as a general fallback
                'price' => ($salePrice > 0) ? $salePrice : ($listPrice > 0 ? $listPrice : 0),
                'stock' => $data['quantity'] ?? 0,
                'raw_marketplace_data' => $data, // Full JSON for debugging/future reference
                'active' => true,
                'marketplace' => 'trendyol' // Latest source
            ]
        );
    }

    /**
     * Create or update the bridge table that links a product to a marketplace.
     */
    protected function upsertChannelProduct(Channel $channel, Product $product, array $data, string $externalId): void
    {
        $salePrice = $data['salePrice'] ?? 0;
        $listPrice = $data['listPrice'] ?? 0;

        ChannelProduct::updateOrCreate(
            [
                'channel_id' => $channel->id,
                'product_id' => $product->id,
            ],
            [
                'external_id' => $externalId,
                'external_sku' => $product->sku,
                'price' => ($salePrice > 0) ? $salePrice : ($listPrice > 0 ? $listPrice : 0),
                'stock' => $data['quantity'] ?? 0,
                'sync_status' => 'synced',
                'extra' => [
                    'platformListingId' => $data['platformListingId'] ?? null,
                    'productContentId' => $data['productContentId'] ?? null,
                    'supplierId' => $data['supplierId'] ?? null,
                    'barcode' => $data['barcode'] ?? null,
                ]
            ]
        );
    }

    /**
     * Link global brand/category with channel-specific versions.
     */
    protected function syncChannelMappings(Channel $channel, Product $product, array $data): void
    {
        // Brand Normalization
        $brandName = trim($data['brand'] ?? 'Unknown');
        $brand = Brand::firstOrCreate(['name' => $brandName], ['slug' => Str::slug($brandName)]);
        $product->update(['brand_id' => $brand->id]);

        if (!empty($data['brandId'])) {
            ChannelBrand::firstOrCreate(
                ['channel_id' => $channel->id, 'external_brand_id' => (string)$data['brandId']],
                ['brand_id' => $brand->id]
            );
        }

        // Category Normalization
        $externalCatId = $data['pimCategoryId'] ?? null;
        $catName = trim($data['categoryName'] ?? 'Genel');
        
        if ($externalCatId) {
            $category = $this->getOrCreateCategoryByExternalId($externalCatId, $catName);
            $product->update(['category_id' => $category->id]);

            ChannelCategory::firstOrCreate(
                ['channel_id' => $channel->id, 'external_category_id' => (string)$externalCatId],
                ['category_id' => $category->id]
            );
        } else {
            // Fallback if no external ID
            $category = Category::firstOrCreate(
                ['name' => $catName],
                ['slug' => Str::slug($catName), 'active' => true]
            );
            $product->update(['category_id' => $category->id]);
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
        if ($category) {
            return $category;
        }

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
        if ($this->categoryMap !== null) {
            return;
        }

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

    protected function syncAttributes(Product $product, array $attributes): void
    {
        if (!class_exists(ProductAttribute::class)) return;
        $product->productAttributes()->delete();
        foreach ($attributes as $attr) {
            $product->productAttributes()->create([
                'name' => $attr['attributeName'] ?? 'Bilinmeyen',
                'value' => $attr['attributeValue'] ?? '-',
            ]);
        }
    }

    protected function syncImages(Product $product, array $images): void
    {
        if (!class_exists(ProductImage::class)) return;
        $product->productImages()->delete();
        foreach ($images as $img) {
            $url = is_array($img) ? ($img['url'] ?? null) : $img;
            if ($url) {
                $product->productImages()->create(['url' => $url]);
            }
        }
    }

    protected function resetCounters(): void
    {
        $this->insertedCount = 0;
        $this->updatedCount = 0;
        $this->skippedCount = 0;
        $this->errors = [];
    }

    protected function getSummary(): array
    {
        return [
            'processed' => $this->insertedCount + $this->updatedCount + $this->skippedCount,
            'inserted' => $this->insertedCount,
            'updated' => $this->updatedCount,
            'skipped' => $this->skippedCount,
            'errors' => $this->errors,
        ];
    }
}
