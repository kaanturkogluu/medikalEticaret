<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncN11Orders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:n11-orders {--force : Senkronizasyon kilidini yok say ve zorla çalıştır}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'N11 pazaryerinden yeni siparişleri çeker ve sisteme kaydeder';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\OrderService $orderService)
    {
        $this->info('N11 Sipariş Senkronizasyonu başlatılıyor...');

        $channel = \App\Models\Channel::where('slug', 'n11')->first();

        if (!$channel) {
            $this->error('Hata: N11 kanalı bulunamadı!');
            return 1;
        }

        if ($this->option('force')) {
            $this->warn('Zorla çalıştırma modu aktif. Kilit kaldırılıyor...');
            \Illuminate\Support\Facades\Cache::forget("sync_orders_{$channel->slug}");
        }

        try {
            $orderService->fetchChannelOrders($channel);
            $this->info('N11 Sipariş Senkronizasyonu başarıyla tamamlandı.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Hata: ' . $e->getMessage());
            return 1;
        }
    }
}
