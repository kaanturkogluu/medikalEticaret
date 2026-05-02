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
                $table->string('name')->unique();
                $table->string('tracking_url', 500)->nullable(); 
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('shipping_companies', function (Blueprint $table) {
                if (!Schema::hasColumn('shipping_companies', 'tracking_url')) {
                    $table->string('tracking_url', 500)->nullable()->after('name');
                }
                
                // Handle active column
                if (!Schema::hasColumn('shipping_companies', 'active')) {
                    if (Schema::hasColumn('shipping_companies', 'is_active')) {
                        // If is_active exists, we can use it or rename it. 
                        // To be safe and simple, we'll just add 'active' if missing.
                        $table->boolean('active')->default(true);
                    } else {
                        $table->boolean('active')->default(true);
                    }
                }
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
