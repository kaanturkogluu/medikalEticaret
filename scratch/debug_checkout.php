<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// Load a sample product
$product = Product::first();
if (!$product) {
    die("No product found in DB\n");
}

$data = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'phone' => '05300000000',
    'city' => 'Istanbul',
    'district' => 'Kadikoy',
    'neighborhood' => 'Moda',
    'address' => 'Test Address 123',
    'payment_method' => 'eft',
    'cart_items' => [
        ['id' => $product->id, 'qty' => 1]
    ]
];

echo "Attempting to create order...\n";

try {
    $controller = new \App\Http\Controllers\CheckoutController();
    $request = new Request([], $data);
    $request->setMethod('POST');
    
    // We can't easily call 'validate' on a manually instantiated controller/request in a simple script without full bootstrap
    // so let's just run the inner logic
    
    $response = $controller->store($request);
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response body: " . $response->getContent() . "\n";

} catch (\Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
