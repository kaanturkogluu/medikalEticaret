<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Shipping Companies tablosunu kontrol et ve eksikleri tamamla
        if (Schema::hasTable('shipping_companies')) {
            Schema::table('shipping_companies', function (Blueprint $table) {
                if (!Schema::hasColumn('shipping_companies', 'tracking_url')) {
                    $table->string('tracking_url', 500)->nullable()->after('name');
                }
                
                if (!Schema::hasColumn('shipping_companies', 'active')) {
                    // Eğer is_active varsa onu kullanabiliriz ama sistem 'active' bekliyor
                    $table->boolean('active')->default(true);
                }
            });
        } else {
            // Eğer tablo hiç yoksa (beklenmedik durum) oluştur
            Schema::create('shipping_companies', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('tracking_url', 500)->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Orders tablosundaki eksik kargo ilişkilerini tamamla
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
        // Geri alma işlemi riskli olduğu için boş bırakıyoruz veya sadece eklenenleri siliyoruz
    }
};
