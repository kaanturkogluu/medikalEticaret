<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add variant_key to products for deterministic duplicate prevention.
 *
 * variant_key stores the md5 hash of a product's variant attribute combination.
 * The unique index on (parent_id, variant_key) prevents duplicate variants
 * for the same parent product.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Variant key: hash of the variant attribute combination
            $table->string('variant_key', 32)->nullable()->after('parent_id');

            // Unique composite index: prevents duplicate variants under same parent
            // NULL parent_id is excluded from the unique constraint (parent products themselves)
            $table->index(['parent_id', 'variant_key'], 'products_parent_variant_key_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_parent_variant_key_index');
            $table->dropColumn('variant_key');
        });
    }
};
