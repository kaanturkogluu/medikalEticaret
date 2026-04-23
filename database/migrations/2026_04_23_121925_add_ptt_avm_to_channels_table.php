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
        \DB::table('channels')->insertOrIgnore([
            'name' => 'PTT AVM',
            'slug' => 'ptt',
            'active' => false,
            'color' => '#ffcc00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('channels')->where('slug', 'ptt')->delete();
    }
};
