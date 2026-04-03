<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

/**
 * VariantService
 *
 * Manages the parent-child product hierarchy.
 *
 * Responsibilities:
 * - Deterministic parent resolution (findOrCreateParent)
 * - Non-destructive parent data enrichment from variants
 * - Variant deduplication via variant_key + parent_id
 * - Linking variants to their parent product
 */
class VariantService
{
    public function __construct(
        protected VariantKeyGenerator $keyGenerator
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // PARENT MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Find or create the parent product for a group of variants.
     *
     * Rules:
     * - Parent is identified by SKU = productMainId
     * - Parent always has parent_id = NULL
     * - If parent exists: apply non-destructive updates (fill only empty fields)
     * - If parent does not exist: create a shell from variant data
     */
    public function findOrCreateParent(string $mainId, array $variantData): Product
    {
        $parent = Product::where('sku', $mainId)
            ->whereNull('parent_id')
            ->first();

        if (!$parent) {
            $salePrice = (float) ($variantData['salePrice'] ?? 0);
            $listPrice = (float) ($variantData['listPrice']  ?? 0);

            $parent = Product::create([
                'sku'           => $mainId,
                'name'          => $variantData['title'] ?? $variantData['name'] ?? ('Ürün: ' . $mainId),
                'description'   => $variantData['description'] ?? null,
                'brand_name'    => $variantData['brand'] ?? null,
                'category_name' => $variantData['categoryName'] ?? null,
                'price'         => $salePrice > 0 ? $salePrice : ($listPrice > 0 ? $listPrice : 0),
                'stock'         => $variantData['quantity'] ?? 0,
                'active'        => true,
                // brand_id / category_id filled later after channel mapping
            ]);

            Log::info('VARIANT [PARENT_CREATED]', [
                'sku'    => $mainId,
                'source' => 'sync',
            ]);
        } else {
            // Non-destructive: only fill fields that are genuinely empty
            $this->enrichParentNonDestructive($parent, $variantData);
        }

        return $parent;
    }

    /**
     * Enrich parent with data from a variant without overwriting existing values.
     */
    public function enrichParent(Product $parent, array $variantData, ?int $brandId, ?int $categoryId): void
    {
        $updates = [];

        if ($brandId && !$parent->brand_id) {
            $updates['brand_id'] = $brandId;
        }
        if ($categoryId && !$parent->category_id) {
            $updates['category_id'] = $categoryId;
        }
        if (!empty($updates)) {
            $parent->update($updates);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VARIANT MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Find an existing variant under a parent by variant_key.
     * Uses the database column for performance (avoids attribute table join).
     */
    public function findExistingVariant(Product $parent, string $variantKey): ?Product
    {
        return Product::where('parent_id', $parent->id)
            ->where('variant_key', $variantKey)
            ->first();
    }

    /**
     * Link a variant product to its parent.
     * No-op if already linked correctly.
     */
    public function linkToParent(Product $variant, Product $parent): void
    {
        if ($variant->parent_id !== $parent->id) {
            $variant->update([
                'parent_id'   => $parent->id,
            ]);

            Log::debug('VARIANT [LINKED_TO_PARENT]', [
                'variant_id' => $variant->id,
                'parent_id'  => $parent->id,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // KEY GENERATION
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Generate a deterministic variant key from variant attributes.
     */
    public function generateVariantKey(array $variantAttributes): string
    {
        return $this->keyGenerator->generate($variantAttributes);
    }

    /**
     * Get the singleton key (for products with no variant attributes).
     */
    public function singletonKey(): string
    {
        return $this->keyGenerator->singletonKey();
    }

    /**
     * Get a human-readable label for logging.
     */
    public function variantLabel(array $variantAttributes): string
    {
        return $this->keyGenerator->label($variantAttributes);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function enrichParentNonDestructive(Product $parent, array $variantData): void
    {
        $updates = [];

        $placeholderName = 'Ürün: ' . $parent->sku;
        if (empty($parent->name) || $parent->name === $placeholderName) {
            $candidate = $variantData['title'] ?? $variantData['name'] ?? null;
            if ($candidate) $updates['name'] = $candidate;
        }

        if (empty($parent->description) && !empty($variantData['description'])) {
            $updates['description'] = $variantData['description'];
        }

        if (empty($parent->brand_name) && !empty($variantData['brand'])) {
            $updates['brand_name'] = $variantData['brand'];
        }

        if (empty($parent->category_name) && !empty($variantData['categoryName'])) {
            $updates['category_name'] = $variantData['categoryName'];
        }

        if (!empty($updates)) {
            $parent->update($updates);
        }
    }
}
