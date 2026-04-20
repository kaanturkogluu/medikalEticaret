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
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * ProductImportService
 *
 * Production-grade, idempotent sync pipeline for Trendyol → Local DB.
 *
 * Pipeline (per product):
 *  1.  Guard: skip unapproved / blacklisted / incomplete
 *  2.  Channel mapping: resolve brand_id + category_id
 *  3.  Attribute normalization (trim, lowercase names, preserve values)
 *  4.  Variant attribute detection (via category_attributes table)
 *  5.  Variant key generation (deterministic MD5 hash)
 *  6.  Parent product: findOrCreate by productMainId (non-destructive update)
 *  7.  Parent enrichment: fill brand_id / category_id if empty
 *  8.  Variant upsert: by SKU (idempotent)
 *  9.  Variant key deduplication: update variant_key on product record
 * 10.  Link variant to parent
 * 11.  Attribute persistence (with is_variant flag)
 * 12.  Channel bridge: upsert channel_brands + channel_categories
 * 13.  Channel product: upsert channel_products bridge record
 * 14.  Image sync
 *
 * Error strategy:
 * - Per-product try/catch + transaction: one failure does NOT stop the batch
 * - All failures are logged with context and surfaced in the summary
 */
class ProductImportService
{
    protected int $insertedCount = 0;
    protected int $updatedCount = 0;
    protected int $skippedCount = 0;
    protected array $errors = [];

    public function __construct(
        protected TrendyolAdapter $adapter,
        protected AttributeService $attributeService,
        protected VariantService $variantService
    ) {
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ENTRY POINT
    // ─────────────────────────────────────────────────────────────────────────

    public function importFromTrendyol(Channel $channel): array
    {
        $this->resetCounters();

        $credential = $channel->credential;
        $this->adapter->setConfig([
            'api_key' => $credential->api_key,
            'api_secret' => $credential->api_secret,
            'supplier_id' => $credential->supplier_id,
        ]);

        Log::info('SYNC [START]', [
            'channel' => $channel->slug,
            'supplier_id' => $credential->supplier_id,
        ]);

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
                    $this->processProduct($channel, $productData);
                }

                $page++;
                if ($page >= 500 || $products->count() < $size) {
                    $hasMore = false;
                }

                usleep(30000); // 300ms rate-limit guard

            } catch (\Exception $e) {
                Log::error('SYNC [PAGE_ERROR]', ['page' => $page, 'error' => $e->getMessage()]);
                $this->errors[] = "Page {$page}: " . $e->getMessage();
                $hasMore = false;
            }
        }

        Log::info('SYNC [DONE]', $this->getSummary());

        return $this->getSummary();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PER-PRODUCT PIPELINE
    // ─────────────────────────────────────────────────────────────────────────

    protected function processProduct(Channel $channel, array $data): void
    {
        // ── STEP 1: Guard ─────────────────────────────────────────────────────
        $sku = trim((string) ($data['productMainId'] ?? $data['productCode'] ?? ''));
        $externalId = trim((string) ($data['id'] ?? ''));

        if (!$sku || !$externalId) {
            $this->skippedCount++;
            Log::debug('SYNC [SKIP] Missing sku/externalId');
            return;
        }
        if (($data['approved'] ?? false) === false) {
            $this->skippedCount++;
            return;
        }
        if (($data['blacklisted'] ?? false) === true) {
            $this->skippedCount++;
            Log::debug("SYNC [SKIP] Blacklisted SKU: {$sku}");
            return;
        }

        $existingProduct = \App\Models\Product::withTrashed()->where('sku', $sku)->first();
        if ($existingProduct && $existingProduct->trashed()) {
            $this->skippedCount++;
            Log::debug("SYNC [SKIP] Deleted locally SKU: {$sku}");
            return;
        }

        try {
            $isNew = false;

            DB::transaction(function () use ($channel, $data, $sku, $externalId, &$isNew) {

                // ── STEP 2: Channel mapping ───────────────────────────────────
                [$categoryId, $brandId] = $this->resolveChannelMappings($channel, $data);

                // ── STEP 3-4: Normalize + detect variant attributes ───────────
                $normalized = $this->attributeService->normalizeAttributes($data['attributes'] ?? []);
                $variantAttrs = $categoryId
                    ? $this->attributeService->getVariantAttributes($categoryId, $normalized)
                    : [];
                $variantAttrNames = array_column($variantAttrs, 'name');

                // ── STEP 5: Generate variant key ──────────────────────────────
                $variantKey = $this->variantService->generateVariantKey($variantAttrs);

                Log::debug('SYNC [VARIANT_KEY]', [
                    'sku' => $sku,
                    'label' => $this->variantService->variantLabel($variantAttrs),
                    'key' => $variantKey,
                ]);

                // ── STEPS 6-7: Parent resolution + enrichment ─────────────────
                $mainId = (string) ($data['productMainId'] ?? $sku);
                $parent = $this->variantService->findOrCreateParent($mainId, $data);
                $this->variantService->enrichParent($parent, $data, $brandId, $categoryId);

                // ── STEP 8-9: Variant upsert (by SKU) + variant_key ──────────
                $product = $this->upsertVariantProduct($data, $sku, $categoryId, $brandId, $variantKey);
                $isNew = $product->wasRecentlyCreated;

                // ── STEP 10: Link to parent ───────────────────────────────────
                if ($product->id !== $parent->id) {
                    $this->variantService->linkToParent($product, $parent);
                }

                // ── STEP 11: Attribute sync ───────────────────────────────────
                $this->attributeService->syncProductAttributes($product, $normalized, $variantAttrNames);

                // ── STEP 12: Channel bridge tables ────────────────────────────
                $this->syncChannelBridgeTables($channel, $data, $brandId, $categoryId);

                // ── STEP 13: Channel product upsert ───────────────────────────
                $this->upsertChannelProduct($channel, $product, $data, $externalId);

                // ── STEP 14: Images ───────────────────────────────────────────
                $this->syncImages($product, $data['images'] ?? []);
            });

            if ($isNew) {
                $this->insertedCount++;
            } else {
                $this->updatedCount++;
            }

            if (($this->insertedCount + $this->updatedCount) % 10 === 0) {
                Log::info('Processed 10 more products...');
            }

        } catch (\Exception $e) {
            $this->errors[] = "SKU {$sku}: " . $e->getMessage();
            Log::error('SYNC [PRODUCT_FAIL]', [
                'sku' => $sku,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolve local brand_id and category_id from channel mapping + database.
     * Returns [?int $categoryId, ?int $brandId]
     */
    protected function resolveChannelMappings(Channel $channel, array $data): array
    {
        // Brand
        $brandId = null;
        $brandName = trim((string) ($data['brand'] ?? ''));
        if ($brandName) {
            $brand = Brand::where('name', $brandName)->first();
            if ($brand) {
                $brandId = $brand->id;
            } else {
                Log::warning('SYNC [BRAND_NOT_FOUND]', ['name' => $brandName]);
            }
        }

        // Category
        $categoryId = null;
        $externalCatId = $data['pimCategoryId'] ?? null;
        $catName = trim((string) ($data['categoryName'] ?? ''));

        $category = Category::where('external_id', $externalCatId)
            ->orWhere(function ($q) use ($catName) {
                $q->where('name', $catName)
                    ->orWhere('slug', Str::slug($catName));
            })
            ->first();

        if ($category) {
            $categoryId = $category->id;
        } else {
            Log::warning('SYNC [CATEGORY_NOT_FOUND]', [
                'name' => $catName,
                'external_id' => $externalCatId,
            ]);
        }

        return [$categoryId, $brandId];
    }

    /**
     * Upsert the variant (child) product by SKU.
     * Also stores variant_key on the product record for fast deduplication.
     */
    protected function upsertVariantProduct(
        array $data,
        string $sku,
        ?int $categoryId,
        ?int $brandId,
        string $variantKey
    ): Product {
        $salePrice = (float) ($data['salePrice'] ?? 0);
        $listPrice = (float) ($data['listPrice'] ?? 0);
        $price = $salePrice > 0 ? $salePrice : ($listPrice > 0 ? $listPrice : 0);

        return Product::updateOrCreate(
            ['sku' => $sku],
            [
                'name' => $data['title'] ?? 'No Title',
                'description' => $data['description'] ?? '',
                'barcode' => $data['barcode'] ?? $sku,
                'price' => $price,
                'stock' => $data['quantity'] ?? 0,
                'brand_id' => $brandId,
                'brand_name' => $data['brand'] ?? null,
                'category_id' => $categoryId,
                'category_name' => $data['categoryName'] ?? null,
                'external_id' => (string) ($data['id'] ?? ''),
                'platform_listing_id' => $data['platformListingId'] ?? null,
                'product_content_id' => $data['productContentId'] ?? null,
                'supplier_id' => (string) ($data['supplierId'] ?? ''),
                'raw_marketplace_data' => $data,
                'marketplace' => 'trendyol',
                'variant_key' => $variantKey,
                'active' => true,
            ]
        );
    }

    /**
     * Register brand & category in the channel bridge tables (idempotent).
     */
    protected function syncChannelBridgeTables(Channel $channel, array $data, ?int $brandId, ?int $categoryId): void
    {
        if ($brandId && !empty($data['brandId'])) {
            ChannelBrand::firstOrCreate(
                ['channel_id' => $channel->id, 'external_brand_id' => (string) $data['brandId']],
                ['brand_id' => $brandId]
            );
        }

        if ($categoryId && !empty($data['pimCategoryId'])) {
            ChannelCategory::firstOrCreate(
                ['channel_id' => $channel->id, 'external_category_id' => (string) $data['pimCategoryId']],
                ['category_id' => $categoryId]
            );
        }
    }

    /**
     * Upsert channel_products bridge record (unique per channel_id + product_id).
     */
    protected function upsertChannelProduct(Channel $channel, Product $product, array $data, string $externalId): void
    {
        $salePrice = (float) ($data['salePrice'] ?? 0);
        $listPrice = (float) ($data['listPrice'] ?? 0);

        ChannelProduct::updateOrCreate(
            ['channel_id' => $channel->id, 'product_id' => $product->id],
            [
                'external_id' => $externalId,
                'price' => $salePrice > 0 ? $salePrice : ($listPrice > 0 ? $listPrice : 0),
                'stock' => $data['quantity'] ?? 0,
                'sync_status' => 'synced',
                'extra' => [
                    'platformListingId' => $data['platformListingId'] ?? null,
                    'productContentId' => $data['productContentId'] ?? null,
                    'supplierId' => $data['supplierId'] ?? null,
                    'barcode' => $data['barcode'] ?? null,
                ],
            ]
        );
    }

    /**
     * Sync product images (volatile → delete + insert).
     */
    protected function syncImages(Product $product, array $images): void
    {
        if (!class_exists(ProductImage::class))
            return;

        $product->productImages()->delete();

        foreach ($images as $img) {
            $url = is_array($img) ? ($img['url'] ?? null) : $img;
            if ($url) {
                $product->productImages()->create(['url' => $url]);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // COUNTERS
    // ─────────────────────────────────────────────────────────────────────────

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
