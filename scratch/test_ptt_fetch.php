<?php

use App\Models\Channel;
use App\Integrations\Marketplace\MarketplaceManager;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $channel = Channel::where('slug', 'ptt')->firstOrFail();
    $manager = app(MarketplaceManager::class);
    $adapter = $manager->getAdapter($channel);
    
    echo "Fetching PTT Orders...\n";
    $orders = $adapter->fetchOrders();
    
    echo "Response Data:\n";
    echo json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
