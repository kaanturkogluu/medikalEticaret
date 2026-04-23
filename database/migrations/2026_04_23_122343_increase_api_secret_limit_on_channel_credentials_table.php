<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('channel_credentials', function (Blueprint $table) {
            $table->string('api_secret', 512)->nullable()->change();
        });
    }


    public function down(): void
    {
        Schema::table('channel_credentials', function (Blueprint $table) {
            $table->string('api_secret', 255)->nullable()->change();
        });
    }
};
