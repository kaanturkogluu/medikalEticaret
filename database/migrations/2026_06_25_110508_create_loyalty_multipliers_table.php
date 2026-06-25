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
        Schema::create('loyalty_multipliers', function (Blueprint $table) {
            $table->id();
            $table->integer('duration_days')->comment('Kaç günlük periyotta');
            $table->integer('order_count')->comment('Hedef alışveriş sayısı');
            $table->decimal('multiplier', 5, 2)->comment('Kazanılacak puan çarpanı');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_multipliers');
    }
};
