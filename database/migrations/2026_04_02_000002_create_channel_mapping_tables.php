<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Trendyol, Hepsiburada, etc.
            $table->string('slug')->unique(); // trendyol, hepsiburada, n11, pttavm
            $table->json('settings')->nullable(); // configuration details (global)
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('channel_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('supplier_id')->nullable(); // useful for Trendyol/Hepsiburada
            $table->json('extra')->nullable(); // any extra field for integration
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('channel_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->string('external_id')->nullable(); // product barcode OR external ID
            $table->decimal('price', 15, 2)->nullable(); // channel-specific price
            $table->integer('stock')->nullable(); // channel-specific stock (if different)
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->default('pending');
            $table->text('sync_error')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'channel_id']);
        });

        Schema::create('channel_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->string('external_category_id');
            $table->timestamps();

            $table->unique(['category_id', 'channel_id']);
        });

        Schema::create('channel_brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->string('external_brand_id');
            $table->timestamps();

            $table->unique(['brand_id', 'channel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channel_brands');
        Schema::dropIfExists('channel_categories');
        Schema::dropIfExists('channel_products');
        Schema::dropIfExists('channel_credentials');
        Schema::dropIfExists('channels');
    }
};
