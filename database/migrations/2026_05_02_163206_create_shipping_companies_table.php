<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('shipping_companies')) {
            Schema::create('shipping_companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('tracking_url')->nullable(); // Pattern like https://kargo.com/track?code=[TRACKING_CODE]
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_company_id')) {
                $table->foreignId('shipping_company_id')->nullable()->constrained('shipping_companies')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'tracking_code')) {
                $table->string('tracking_code')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_company_id']);
            $table->dropColumn(['shipping_company_id', 'tracking_code']);
        });
        Schema::dropIfExists('shipping_companies');
    }
};
