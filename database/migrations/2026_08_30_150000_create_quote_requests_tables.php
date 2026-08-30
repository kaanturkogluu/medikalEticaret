<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('quote_no')->unique();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('type')->default('bulk_order'); // bulk_order, donation, corporate, general
            $table->text('customer_note')->nullable();
            $table->decimal('estimated_total', 12, 2)->default(0);
            $table->decimal('offered_total', 12, 2)->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, offered, converted, completed, rejected
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('created_product_id')->nullable()->index();
            $table->string('custom_payment_link')->nullable();
            $table->timestamps();
        });

        Schema::create('quote_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_request_id')->constrained('quote_requests')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->string('product_image')->nullable();
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('offered_unit_price', 10, 2)->nullable();
            $table->decimal('total_price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_request_items');
        Schema::dropIfExists('quote_requests');
    }
};
