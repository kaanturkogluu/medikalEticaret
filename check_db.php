<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new PDO(
    "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4",
    $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$total = $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
echo "Toplam sipariş: $total\n";

$pending = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status IN ('pending_payment','unpaid')")->fetchColumn();
echo "Ödeme bekleyen: $pending\n\n";

$rows = $pdo->query('SELECT order_status, COUNT(*) as cnt FROM orders GROUP BY order_status ORDER BY cnt DESC')->fetchAll(PDO::FETCH_ASSOC);
echo "Status dağılımı:\n";
foreach ($rows as $r) {
    echo "  [{$r['order_status']}]: {$r['cnt']}\n";
}

// Excel'deki başarılı Conversation ID'leri listele
echo "\nExcel'deki başarılı Conv.ID'ler DB'de kontrolü:\n";
$testIds = [4072, 4062, 4060, 4050, 4040, 3000, 2000, 1000];
foreach ($testIds as $id) {
    $o = $pdo->query("SELECT id, customer_name, order_status, payment_method FROM orders WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
    if ($o) {
        echo "  ID $id: {$o['customer_name']} | status={$o['order_status']} | method={$o['payment_method']}\n";
    } else {
        echo "  ID $id: BULUNAMADI\n";
    }
}

// En son 5 siparişi göster
echo "\nSon 5 sipariş:\n";
$last5 = $pdo->query("SELECT id, customer_name, order_status, payment_method, total_price, created_at FROM orders ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
foreach ($last5 as $o) {
    echo "  ID={$o['id']} | {$o['customer_name']} | status={$o['order_status']} | method={$o['payment_method']} | {$o['total_price']} TL | {$o['created_at']}\n";
}

// pending_payment olanları listele
echo "\n'pending_payment' durumundaki tüm siparişler:\n";
$pending_orders = $pdo->query("SELECT id, customer_name, customer_email, customer_phone, order_status, total_price, created_at, payment_token FROM orders WHERE order_status IN ('pending_payment','unpaid') ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
if (empty($pending_orders)) {
    echo "  Hiç yok.\n";
} else {
    foreach ($pending_orders as $o) {
        echo "  ID={$o['id']} | {$o['customer_name']} | {$o['customer_email']} | status={$o['order_status']} | {$o['total_price']} TL | token={$o['payment_token']}\n";
    }
}
