<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeactivateZeroStockProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:deactivate-zero-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sistemdeki stoku 0 olan ürünleri otomatik olarak satışa kapatır.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Stoku 0 olan ürünler kontrol ediliyor...');

        $updatedCount = \App\Models\Product::where('stock', '<=', 0)
            ->where('active', true)
            ->update(['active' => false]);

        if ($updatedCount > 0) {
            $this->info("Toplam {$updatedCount} ürün satışa kapatıldı.");
        } else {
            $this->info('Stoku 0 olup da satışa açık olan ürün bulunamadı.');
        }

        return 0;
    }
}
