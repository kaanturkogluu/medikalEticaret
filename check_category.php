<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$categories = \App\Models\Category::whereDoesntHave('children')
    ->whereHas('products', function ($q) {
        $q->where('active', true)->where('stock', '>', 0);
    })
    ->withCount(['products' => function ($q) {
        $q->where('active', true)->where('stock', '>', 0);
    }])
    ->with('parent')
    ->orderBy('name')
    ->get();

echo "Toplam leaf kategori (urunlu): " . $categories->count() . PHP_EOL;
echo "---" . PHP_EOL;
foreach ($categories as $c) {
    $parentStr = $c->parent ? '[' . $c->parent->name . '] > ' : '';
    echo "  {$parentStr}{$c->name} ({$c->products_count} urun)" . PHP_EOL;
}

// Eski sorgu ile karşılaştır
echo PHP_EOL . "=== ESKİ SORGU (parent dahil tüm kategoriler) ===" . PHP_EOL;
$old = \App\Models\Category::whereHas('products', function ($q) {
    $q->where('active', true)->where('stock', '>', 0);
})->count();
echo "Eski toplam: $old" . PHP_EOL;
