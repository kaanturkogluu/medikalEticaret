<?php

namespace App\Services;

use App\Models\Product;
use App\Models\CategoryAttribute;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * AttributeService
 *
 * Handles attribute normalization, category-based variant detection,
 * and attribute persistence.
 *
 * Design principles:
 * - NO semantic transformation (no translation, no mapping)
 * - Normalize names: trim + lowercase
 * - Preserve values: trim + collapse whitespace (case preserved)
 * - In-memory cache for category_attributes (eliminates N+1 on bulk imports)
 */
class AttributeService
{
    /** @var array<int, Collection> In-memory cache for category attribute definitions */
    private array $categoryAttributeCache = [];

    // ─────────────────────────────────────────────────────────────────────────
    // NORMALIZATION
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Normalize raw marketplace attributes.
     *
     * Input: [['name' => ' Renk ', 'value' => 'Mavi  '], ...]
     *         or Trendyol format: [['attributeName' => 'Renk', 'attributeValue' => 'Mavi'], ...]
     *
     * Output: [['name' => 'renk', 'value' => 'Mavi'], ...]
     */
    public function normalizeAttributes(array $rawAttributes): array
    {
        $normalized = [];

        foreach ($rawAttributes as $attr) {
            $rawName  = (string) ($attr['name']  ?? $attr['attributeName']  ?? '');
            $rawValue = (string) ($attr['value'] ?? $attr['attributeValue'] ?? '');

            $name  = Str::lower(trim($rawName));
            $value = preg_replace('/\s+/', ' ', trim($rawValue));

            // Skip completely empty entries
            if ($name === '' || $value === '') {
                continue;
            }

            $normalized[] = [
                'name'  => $name,
                'value' => $value,
            ];
        }

        return $normalized;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CATEGORY-BASED VARIANT DETECTION
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Get CategoryAttribute definitions for a category (with in-memory cache).
     */
    public function getCategoryAttributes(int $categoryId): Collection
    {
        if (!isset($this->categoryAttributeCache[$categoryId])) {
            $this->categoryAttributeCache[$categoryId] = CategoryAttribute::where('category_id', $categoryId)->get();
        }

        return $this->categoryAttributeCache[$categoryId];
    }

    /**
     * Filter normalized attributes to only those defined as variant dimensions.
     *
     * Returns only attributes where category_attributes.is_variant = true.
     * Returns empty array if no category rules are defined (no variants).
     */
    public function getVariantAttributes(int $categoryId, array $normalizedAttributes): array
    {
        $catAttrs = $this->getCategoryAttributes($categoryId);

        if ($catAttrs->isEmpty()) {
            return []; // No rules → no variant grouping
        }

        $variantNames = $catAttrs
            ->where('is_variant', true)
            ->pluck('name')
            ->map(fn($n) => Str::lower(trim($n)))
            ->flip() // Use as a hash for O(1) lookup
            ->toArray();

        return array_values(array_filter(
            $normalizedAttributes,
            fn($attr) => isset($variantNames[$attr['name']])
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PERSISTENCE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Sync normalized attributes to a product.
     *
     * Strategy: delete + insert (idempotent, simple, correct for attributes).
     * Each attribute is flagged with is_variant based on the category rules.
     *
     * @param  Product  $product
     * @param  array    $normalizedAttributes  Output of normalizeAttributes()
     * @param  array    $variantAttrNames      Names that are variant dimensions (lowercase)
     */
    public function syncProductAttributes(
        Product $product,
        array $normalizedAttributes,
        array $variantAttrNames = []
    ): void {
        // Build a lookup set for O(1) is_variant checks
        $variantNameSet = array_flip(array_map('strtolower', $variantAttrNames));

        // Remove old attributes (variants get recomputed every sync)
        $product->productAttributes()->delete();

        foreach ($normalizedAttributes as $attr) {
            $isVariant = isset($variantNameSet[$attr['name']]);

            $product->productAttributes()->create([
                'name'       => $attr['name'],
                'value'      => $attr['value'],
                'is_variant' => $isVariant,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CACHE MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Clear the in-memory cache. Useful between test cases or long-running jobs.
     */
    public function clearCache(): void
    {
        $this->categoryAttributeCache = [];
    }
}
