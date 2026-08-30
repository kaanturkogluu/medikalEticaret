<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            Setting::updateOrCreate(
                ['key' => 'site_primary_color'],
                ['value' => '#059669']
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            Setting::updateOrCreate(
                ['key' => 'site_primary_color'],
                ['value' => '#f27a1a']
            );
        }
    }
};
