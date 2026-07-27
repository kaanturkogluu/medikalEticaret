<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class CancelPendingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancels orders that have been in pending_payment status for more than 30 minutes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $websiteChannel = \App\Models\Channel::where('slug', 'website')->first();
        $websiteChannelId = $websiteChannel ? $websiteChannel->id : null;

        // 30 dakika: 3D Secure doğrulaması + iyzico ödeme süreci için yeterli süre
        $expiryTime = Carbon::now()->subMinutes(30);

        $query = Order::where('order_status', 'pending_payment')
            ->where('created_at', '<=', $expiryTime);

        if ($websiteChannelId) {
            $query->where('channel_id', $websiteChannelId);
        } else {
            // Fallback: If no website channel exists, assume null channel_id means website
            $query->whereNull('channel_id');
            $this->warn('Website channel not found in database, falling back to orders with null channel_id.');
        }

        $expiredOrders = $query->get();

        $count = $expiredOrders->count();

        if ($count === 0) {
            $this->info('No pending orders to cancel.');
            return 0;
        }

        foreach ($expiredOrders as $order) {
            $order->update([
                'order_status' => 'cancelled'
            ]);
            $this->info("Order #{$order->id} has been cancelled due to payment timeout.");
        }

        $this->info("Total {$count} orders cancelled.");

        return 0;
    }
}
