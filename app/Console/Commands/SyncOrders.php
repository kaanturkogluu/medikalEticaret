<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class SyncOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch orders from all active marketplaces and save them to the database.';

    /**
     * Execute the console command.
     */
    public function handle(OrderService $orderService)
    {
        $this->info('Starting order synchronization...');
        $orderService->fetchAllChannelOrders();
        $this->info('Order synchronization completed successfully.');
    }
}
