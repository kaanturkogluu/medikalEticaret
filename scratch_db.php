<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$images = Illuminate\Support\Facades\DB::table('product_images')->orderBy('id', 'desc')->take(2)->get();
print_r($images);
