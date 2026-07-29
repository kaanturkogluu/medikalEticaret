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
        Schema::create('cari_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('tax_office')->nullable();
            $table->text('address')->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('cari_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cari_account_id')->constrained('cari_accounts')->onDelete('cascade');
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->string('type'); // 'debit' = Borç / Satış, 'credit' = Alacak / Tahsilat
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->dateTime('transaction_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cari_transactions');
        Schema::dropIfExists('cari_accounts');
    }
};
