<?php

namespace App\Jobs;

use App\Integrations\Marketplace\MarketplaceManager;
use App\Models\ChannelProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ChannelProduct $channelProduct;

    public function __construct(ChannelProduct $channelProduct)
    {
        $this->channelProduct = $channelProduct;
    }

    public function handle(MarketplaceManager $manager): void
    {
        try {
            $adapter = $manager->getAdapter($this->channelProduct->channel);
            
            // if external_id exists, it means update, otherwise create
            if ($this->channelProduct->external_id) {
                $success = $adapter->updateProduct($this->channelProduct);
            } else {
                $success = $adapter->createProduct($this->channelProduct);
            }

            if ($success) {
                $this->channelProduct->update([
                    'sync_status' => 'synced',
                    'sync_error' => null
                ]);
            } else {
                $this->channelProduct->update([
                    'sync_status' => 'failed',
                    'sync_error' => 'API response: Creation/Update Failed'
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Product sync failed for SKU {$this->channelProduct->product->sku}: " . $e->getMessage());

            $this->channelProduct->update([
                'sync_status' => 'failed',
                'sync_error' => $e->getMessage()
            ]);
        }
    }
}
