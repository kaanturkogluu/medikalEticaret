<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enum yerine string kullanarak problemi çözüyoruz
        DB::statement("ALTER TABLE coupons MODIFY COLUMN type VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alırken enum eski haline getirilebilir, ancak şu anlık enum olmaması daha güvenli.
    }
};
