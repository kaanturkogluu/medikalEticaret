<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode')->nullable()->after('sku');
            $table->string('brand_name')->nullable()->after('name');
            $table->string('category_name')->nullable()->after('brand_name');
            $table->json('attributes')->nullable()->after('stock');
            $table->json('raw_marketplace_data')->nullable()->after('attributes');
            $table->string('marketplace_status')->nullable()->after('raw_marketplace_data');
            $table->string('marketplace')->nullable()->after('marketplace_status');
            $table->string('external_id')->nullable()->after('marketplace');
            $table->string('platform_listing_id')->nullable()->after('external_id');
            $table->string('product_content_id')->nullable()->after('platform_listing_id');
            $table->string('supplier_id')->nullable()->after('product_content_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('slug');
        });

        // Also ensure images and attributes tables exist
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('url')->nullable();
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_attributes')) {
            Schema::create('product_attributes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('name')->nullable();
                $table->string('value')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'barcode', 'brand_name', 'category_name', 'attributes', 
                'raw_marketplace_data', 'marketplace_status', 'marketplace', 
                'external_id', 'platform_listing_id', 'product_content_id', 'supplier_id'
            ]);
        });
        
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_attributes');
    }
};
