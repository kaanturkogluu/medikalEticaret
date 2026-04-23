<?php

namespace App\Services;

use App\Integrations\Marketplace\MarketplaceManager;
use App\Models\Channel;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected MarketplaceManager $manager,
        protected SyncService $syncService
    ) {}

    public function fetchAllChannelOrders(): void
    {
        Channel::where('active', true)->each(function (Channel $channel) {
            $this->fetchChannelOrders($channel);
            // Her kanal sonrası 1 saniye mola (Spam önleme)
            usleep(1000000);
        });
    }

    public function fetchChannelOrders(Channel $channel): void
    {
        $lockKey = "sync_orders_{$channel->slug}";
        
        // Eğer zaten bir senkronizasyon çalışıyorsa (Son 10 dk içinde başladıysa) durdur (Spam kilidi)
        if (Cache::has($lockKey)) {
            Log::warning("SYNC [ORDER] [ABORT] Zaten aktif veya yeni bir senkronizasyon yapıldı: {$channel->slug}");
            return;
        }

        try {
            // Kilidi 10 dakika boyunca tut (Butona ard arda basılsa bile API yorulmaz)
            Cache::put($lockKey, true, now()->addMinutes(10));

            $adapter = $this->manager->getAdapter($channel);
            $page = 0;
            $size = 50;
            $total = 0;
            $maxPages = 50; // Güvenlik kilidi

            do {
                $externalOrders = $adapter->fetchOrders($page, $size);
                
                if ($externalOrders->isEmpty()) break;

                foreach ($externalOrders as $orderData) {
                    $this->processOrder($channel, $orderData);
                    $total++;
                }

                $page++;
                
                // --- RATE LIMIT GÜVENLİĞİ ---
                usleep(500000); // 0.5 saniye mola

                if ($page >= $maxPages) break;

            } while ($externalOrders->count() === $size);

            Log::info("SYNC [ORDER] [TAMAM] Kanal [{$channel->slug}], Toplam: {$total}");

        } catch (\Exception $e) {
            Cache::forget($lockKey); // Hata durumunda kilidi aç ki tekrar denenebilsin
            Log::error("Failed to fetch orders for channel: {$channel->slug}: " . $e->getMessage());
        }
    }

    protected function processOrder(Channel $channel, array $orderData): void
    {
        $externalId = $orderData['orderNumber'] ?? 
                     $orderData['siparisNo'] ?? 
                     $orderData['id'] ?? null;

        if (!$externalId || Order::where('channel_id', $channel->id)->where('external_order_id', (string)$externalId)->exists()) {
            return;
        }

        DB::transaction(function () use ($channel, $orderData, $externalId) {
            // Handle different naming conventions between marketplaces
            $customerName = $orderData['customerfullName'] ?? 
                           (($orderData['musteriAdi'] ?? '') . ' ' . ($orderData['musteriSoyadi'] ?? '')) ?:
                           (($orderData['customerFirstName'] ?? '') . ' ' . ($orderData['customerLastName'] ?? ''));
            
            $totalPrice = $orderData['totalAmount'] ?? ($orderData['totalPrice'] ?? 0);
            
            // PTT Total Price Calculation
            if ($channel->slug === 'ptt' && isset($orderData['siparisUrunler'])) {
                $totalPrice = collect($orderData['siparisUrunler'])->sum('kdvDahilToplamTutar');
            }

            $status = $orderData['shipmentPackageStatus'] ?? 
                     ($orderData['siparisUrunler'][0]['siparisDurumu'] ?? ($orderData['status'] ?? 'created'));

            $orderDate = $orderData['orderDate'] ?? 
                        $orderData['islemTarihi'] ?? 
                        $orderData['creationDate'] ?? 
                        now();
            
            // Handle millisecond timestamps if necessary
            if (is_numeric($orderDate) && $orderDate > 1000000000000) {
                $orderDate = \Carbon\Carbon::createFromTimestampMs($orderDate);
            }

            $order = Order::create([
                'channel_id' => $channel->id,
                'external_order_id' => (string)$externalId,
                'customer_name' => trim($customerName) ?: 'Bilinmeyen Müşteri',
                'customer_email' => $orderData['customerEmail'] ?? ($orderData['eposta'] ?? null),
                'customer_phone' => $orderData['telefonNo'] ?? null,
                'total_price' => $totalPrice,
                'order_date' => $orderDate,
                'order_status' => strtolower($status),
                'address_info' => [
                    'address' => $orderData['siparisAdresi'] ?? ($orderData['shippingAddress']['address'] ?? null),
                    'city' => $orderData['siparisIli'] ?? ($orderData['shippingAddress']['city'] ?? null),
                    'district' => $orderData['siparisIlce'] ?? ($orderData['shippingAddress']['district'] ?? null),
                ],
                'raw_marketplace_data' => $orderData,
                'synced' => true,
            ]);

            $lines = $orderData['siparisUrunler'] ?? $orderData['lines'] ?? $orderData['items'] ?? [];

            foreach ($lines as $line) {
                $sku = $line['urunBarkod'] ?? $line['barcode'] ?? $line['stockCode'] ?? $line['sku'] ?? null;
                $product = Product::where('sku', $sku)->first();

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product?->id,
                    'external_product_id' => $sku,
                    'quantity' => $line['toplamIslemAdedi'] ?? ($line['quantity'] ?? 1),
                    'price' => $line['kdvDahilToplamTutar'] ?? ($line['price'] ?? 0),
                ]);

                if ($product) {
                    $product->decrement('stock', $line['quantity'] ?? 1);
                    // Automatically sync decreased stock to other channels
                    $this->syncService->syncProductStock($product);
                }
            }
        });
    }
}
