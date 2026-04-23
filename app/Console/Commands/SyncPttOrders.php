<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncPttOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ptt-orders {--force : Senkronizasyon kilidini yok say ve zorla çalıştır}';

    protected $description = 'PTT AVM pazaryerinden yeni siparişleri çeker ve sisteme kaydeder';

    public function handle(\App\Services\OrderService $orderService)
    {
        $this->info('PTT AVM Sipariş Senkronizasyonu başlatılıyor...');

        $channel = \App\Models\Channel::where('slug', 'ptt')->first();

        if (!$channel) {
            $this->error('Hata: PTT AVM kanalı bulunamadı!');
            return 1;
        }

        if ($this->option('force')) {
            $this->warn('Zorla çalıştırma modu aktif. Kilit kaldırılıyor...');
            \Illuminate\Support\Facades\Cache::forget("sync_orders_{$channel->slug}");
        }

        try {
            $orderService->fetchChannelOrders($channel);
            $this->info('PTT AVM Sipariş Senkronizasyonu başarıyla tamamlandı.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Hata: ' . $e->getMessage());
            return 1;
        }
    }
}
