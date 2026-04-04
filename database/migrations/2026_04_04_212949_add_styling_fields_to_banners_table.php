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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('title_color')->default('#FFFFFF');
            $table->string('subtitle_color')->default('#FFFFFF');
            $table->integer('title_size')->default(60);
            $table->integer('subtitle_size')->default(12);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['title_color', 'subtitle_color', 'title_size', 'subtitle_size']);
        });
    }
};
