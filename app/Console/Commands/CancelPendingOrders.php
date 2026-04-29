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
    protected $description = 'Cancels orders that have been in pending_payment status for more than 2 hours.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $websiteChannel = \App\Models\Channel::where('slug', 'website')->first();
        
        if (!$websiteChannel) {
            $this->error('Website channel not found.');
            return 1;
        }

        $expiryTime = Carbon::now()->subMinutes(5);

        $expiredOrders = Order::where('channel_id', $websiteChannel->id)
            ->where('order_status', 'pending_payment')
            ->where('created_at', '<=', $expiryTime)
            ->get();

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
