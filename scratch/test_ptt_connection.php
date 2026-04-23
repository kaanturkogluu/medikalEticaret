<?php

use App\Models\Channel;
use App\Integrations\Marketplace\MarketplaceManager;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $channel = Channel::where('slug', 'ptt')->firstOrFail();
    $manager = app(MarketplaceManager::class);
    $adapter = $manager->getAdapter($channel);
    
    echo "Testing PTT AVM Connection...\n";
    $success = $adapter->testConnection();
    
    if ($success) {
        echo "Connection Successful!\n";
    } else {
        echo "Connection Failed!\n";
        // Check logs for more info
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
