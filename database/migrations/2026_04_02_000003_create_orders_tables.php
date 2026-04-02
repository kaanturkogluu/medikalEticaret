<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->nullable()->constrained()->onDelete('set null');
            $table->string('external_order_id')->nullable(); // external Marketplace Order ID
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('total_price', 15, 2);
            $table->string('currency')->default('TRY');
            $table->string('order_status')->default('created');
            $table->json('address_info')->nullable();
            $table->json('raw_marketplace_data')->nullable(); // for debug/tracking
            $table->boolean('synced')->default(false); // check if order exists in local system/erp
            $table->timestamps();

            $table->unique(['channel_id', 'external_order_id']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('external_product_id')->nullable(); // barcode/external sku ref
            $table->integer('quantity');
            $table->decimal('price', 15, 2); // item price at order time
            $table->decimal('discount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
