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
        \App\Models\Channel::firstOrCreate(
            ['slug' => 'website'],
            ['name' => 'Web Sitesi', 'active' => true]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Channel::where('slug', 'website')->delete();
    }
};
