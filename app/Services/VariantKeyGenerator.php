<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * VariantKeyGenerator
 *
 * Generates a fully deterministic, stable hash key from a set of variant attributes.
 *
 * Guarantees:
 * - Same attribute combination ALWAYS produces the same key
 * - Attribute ORDER does not matter (attributes are sorted alphabetically)
 * - Case and whitespace variations are normalized before hashing
 * - Null/empty values are handled safely
 *
 * Design notes:
 * - Names  → trim + lowercase (for case-insensitive matching)
 * - Values → trim + collapse multiple whitespace (preserve original casing for display)
 * - Sorted alphabetically by normalized name before building the canonical string
 */
class VariantKeyGenerator
{
    /**
     * Generate a stable MD5 key from variant attributes.
     *
     * @param  array  $variantAttributes  [['name' => '...', 'value' => '...'], ...]
     * @return string  32-char lowercase MD5 hex string
     */
    public function generate(array $variantAttributes): string
    {
        if (empty($variantAttributes)) {
            // Products with no variant attributes get a reserved "singleton" key
            return md5('__no_variants__');
        }

        $normalized = $this->normalizeForHashing($variantAttributes);

        if (empty($normalized)) {
            return md5('__no_variants__');
        }

        $canonical = $this->buildCanonicalString($normalized);

        return md5($canonical);
    }

    /**
     * Normalize attributes for hashing:
     * - Trim + lowercase names
     * - Trim + collapse internal whitespace on values (preserve casing)
     * - Remove entries with empty name or value
     */
    private function normalizeForHashing(array $attributes): array
    {
        $normalized = [];

        foreach ($attributes as $attr) {
            $name  = Str::lower(trim((string) ($attr['name']  ?? '')));
            $value = preg_replace('/\s+/', ' ', trim((string) ($attr['value'] ?? '')));

            if ($name === '' || $value === '') {
                continue; // Skip empty/incomplete attributes
            }

            $normalized[] = ['name' => $name, 'value' => $value];
        }

        return $normalized;
    }

    /**
     * Build a canonical string by sorting attributes alphabetically by name,
     * then joining as "name:value|" pairs.
     *
     * Example output: "beden:XL|renk:Mavi|"
     */
    private function buildCanonicalString(array $normalizedAttributes): string
    {
        // Sort by name to eliminate order dependency
        usort($normalizedAttributes, fn($a, $b) => strcmp($a['name'], $b['name']));

        return implode('', array_map(
            fn($attr) => "{$attr['name']}:{$attr['value']}|",
            $normalizedAttributes
        ));
    }

    /**
     * Generate a human-readable label for logging/debugging.
     * NOT used as a key — for display only.
     */
    public function label(array $variantAttributes): string
    {
        if (empty($variantAttributes)) {
            return '(no variants)';
        }

        $normalized = $this->normalizeForHashing($variantAttributes);

        usort($normalized, fn($a, $b) => strcmp($a['name'], $b['name']));

        return implode(', ', array_map(
            fn($a) => "{$a['name']}={$a['value']}",
            $normalized
        ));
    }

    /**
     * Reserved key for products that have no variant attributes.
     */
    public function singletonKey(): string
    {
        return md5('__no_variants__');
    }
}
