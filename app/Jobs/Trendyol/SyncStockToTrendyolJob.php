<?php

namespace App\Jobs\Trendyol;

use App\Integrations\Marketplace\MarketplaceManager;
use App\Models\ChannelProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncStockToTrendyolJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected ChannelProduct $channelProduct) {}

    /**
     * Execute the job.
     */
    public function handle(MarketplaceManager $manager): void
    {
        try {
            $adapter = $manager->getAdapter($this->channelProduct->channel);

            if ($this->channelProduct->channel->slug !== 'trendyol') {
                return;
            }

            Log::info("Trendyol stock sync job started for SKU: " . $this->channelProduct->product->sku);

            $success = $adapter->syncStock($this->channelProduct);

            if ($success) {
                $this->channelProduct->update([
                    'sync_status' => 'synced',
                    'sync_error' => null,
                    'updated_at' => now()
                ]);
                Log::info("Trendyol stock sync success for SKU: " . $this->channelProduct->product->sku);
            } else {
                $this->channelProduct->update([
                    'sync_status' => 'failed',
                    'sync_error' => 'Trendyol API: Request failed, check logs.'
                ]);
                Log::error("Trendyol stock sync failed for SKU: " . $this->channelProduct->product->sku);
            }

        } catch (\Exception $e) {
            Log::error("Trendyol sync job error - SKU " . $this->channelProduct->product->sku . ": " . $e->getMessage());

            $this->channelProduct->update([
                'sync_status' => 'failed',
                'sync_error' => $e->getMessage()
            ]);
            
            // Allow retry
            throw $e;
        }
    }
}
