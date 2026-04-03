<?php

namespace App\Services;

use Illuminate\Support\Str;

class AttributeNormalizationService
{
    /**
     * Normalize product attributes according to simple rules:
     * 1. Trim name and value.
     * 2. Lowercase name.
     * 3. Preserve value case.
     * 4. No translation or filtering.
     */
    public function normalize(int $categoryId, array $attributes): array
    {
        $normalizedAttributes = [];

        foreach ($attributes as $attr) {
            $rawName = $attr['name'] ?? $attr['attributeName'] ?? '';
            $rawValue = $attr['value'] ?? $attr['attributeValue'] ?? '';

            $normalizedAttributes[] = [
                'name' => Str::lower(trim($rawName)),
                'value' => trim($rawValue)
            ];
        }

        return [
            'categoryId' => $categoryId,
            'attributes' => $normalizedAttributes
        ];
    }
}
