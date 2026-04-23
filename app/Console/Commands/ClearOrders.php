<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class ClearOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:clear {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sistemdeki tüm siparişleri ve sipariş kalemlerini temizler.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('Tüm siparişler ve bunlara bağlı ürün kalemleri kalıcı olarak silinecek. Emin misiniz?')) {
            $this->info('İşlem iptal edildi.');
            return 0;
        }

        try {
            DB::transaction(function () {
                $itemCount = OrderItem::count();
                $orderCount = Order::count();

                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                OrderItem::truncate();
                Order::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                $this->info("{$orderCount} sipariş ve {$itemCount} sipariş kalemi başarıyla temizlendi.");
            });
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->error('Hata oluştu: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
