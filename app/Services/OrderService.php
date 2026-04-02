<?php

namespace App\Services;

use App\Integrations\Marketplace\MarketplaceManager;
use App\Models\Channel;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
        });
    }

    public function fetchChannelOrders(Channel $channel): void
    {
        try {
            $adapter = $this->manager->getAdapter($channel);
            $externalOrders = $adapter->fetchOrders();

            foreach ($externalOrders as $orderData) {
                $this->processOrder($channel, $orderData);
            }

        } catch (\Exception $e) {
            Log::error("Failed to fetch orders for channel: {$channel->slug}: " . $e->getMessage());
        }
    }

    protected function processOrder(Channel $channel, array $orderData): void
    {
        $externalId = $orderData['orderNumber'] ?? $orderData['id'];

        if (Order::where('channel_id', $channel->id)->where('external_order_id', (string)$externalId)->exists()) {
            return;
        }

        DB::transaction(function () use ($channel, $orderData, $externalId) {
            $order = Order::create([
                'channel_id' => $channel->id,
                'external_order_id' => (string)$externalId,
                'customer_name' => ($orderData['customerFirstName'] ?? '') . ' ' . ($orderData['customerLastName'] ?? ''),
                'customer_email' => $orderData['customerEmail'] ?? null,
                'total_price' => $orderData['totalPrice'] ?? 0,
                'order_status' => $orderData['status'] ?? 'created',
                'raw_marketplace_data' => $orderData,
            ]);

            $lines = $orderData['lines'] ?? $orderData['items'] ?? [];

            foreach ($lines as $line) {
                $sku = $line['barcode'] ?? $line['sku'] ?? null;
                $product = Product::where('sku', $sku)->first();

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product?->id,
                    'external_product_id' => $sku,
                    'quantity' => $line['quantity'] ?? 1,
                    'price' => $line['price'] ?? 0,
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
