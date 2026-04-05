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
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('views')->default(0)->unsigned()->after('stock');
        });

        // Veri tabanındaki mevcut ürünlere 0 ile 1000 arası rastgele görüntülenme ata
        \DB::table('products')->get()->each(function ($product) {
            \DB::table('products')->where('id', $product->id)->update([
                'views' => rand(0, 1000)
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('views');
        });
    }
};
