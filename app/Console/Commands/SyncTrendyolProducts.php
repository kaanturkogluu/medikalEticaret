<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Services\ProductImportService;
use Illuminate\Console\Command;

class SyncTrendyolProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:trendyol-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from Trendyol and sync with local database.';

    /**
     * Execute the console command.
     */
    public function handle(ProductImportService $service)
    {
        $this->info('Starting Trendyol product import...');

        $channel = Channel::where('slug', 'trendyol')->first();

        if (!$channel) {
            $this->error('Trendyol channel not found!');
            return 1;
        }

        $result = $service->importFromTrendyol($channel);

        $this->table(
            ['Processed', 'Inserted', 'Updated', 'Skipped'],
            [
                [
                    $result['processed'],
                    $result['inserted'],
                    $result['updated'],
                    $result['skipped']
                ]
            ]
        );

        if (!empty($result['errors'])) {
            $this->warn('Errors encountered:');
            foreach ($result['errors'] as $error) {
                $this->error("- " . $error);
            }
        }

        $this->info('Import completed successfully.');
        return 0;
    }
}
