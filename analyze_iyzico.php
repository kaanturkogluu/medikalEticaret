<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// ── 1. EXCEL OKU ────────────────────────────────────────────────────────────
$xlsxPath = __DIR__ . '/transactions_28012026_27072026 (2).xlsx';
$spreadsheet = IOFactory::load($xlsxPath);
$sheet       = $spreadsheet->getActiveSheet();
$rows        = $sheet->toArray(null, true, true, true);

$headers = array_map('trim', $rows[1] ?? []);
$transactions = [];
foreach ($rows as $rowNum => $row) {
    if ($rowNum === 1) continue;
    $rec = [];
    foreach ($row as $col => $val) {
        $key = trim($headers[$col] ?? $col);
        $rec[$key] = is_string($val) ? trim($val) : $val;
    }
    if (empty(array_filter($rec))) continue;
    $transactions[] = $rec;
}

// ── 2. VERİTABANI ───────────────────────────────────────────────────────────
$pdo = new PDO(
    "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4",
    $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query("SELECT * FROM orders ORDER BY id");
$orders = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $o) {
    $orders[$o['id']] = $o;
}

// ── 3. KARŞILAŞTIRMA ────────────────────────────────────────────────────────
// Iyzico'da BAŞARILI olan ama sistemde:
// - pending_payment, unpaid → hiç işlemlenmemiş
// - cancelled, iptal       → ödeme olmasına rağmen iptal görünüyor
// - pending                → henüz işlenmemiş
// bunları sorunlu say

$sorunluDurumlar = ['pending_payment', 'unpaid', 'cancelled', 'iptal', 'pending'];

$sorunlular     = [];
$normalOlanlar  = 0;
$hicBulunamayan = [];

foreach ($transactions as $t) {
    $txOdemeDurumu = $t['Ödeme Durumu'] ?? '';
    $txConvId      = (int)trim($t['Conversation ID'] ?? 0);
    $txAd          = trim(($t['Alıcı Adı'] ?? '') . ' ' . ($t['Alıcı Soyadı'] ?? ''));
    $txTutar       = $t['Tahsil Edilen Tutar'] ?? '';
    $txTarih       = $t['Oluşturma Tarihi'] ?? '';
    $txOdemeNo     = ltrim($t['Ödeme Numarası'] ?? '', "\t ");
    $txSon4        = $t['Kartın Son 4 Hanesi'] ?? '';
    $txKartSahibi  = $t['Kart Sahibinin Adı'] ?? '';
    $txIadeStatus  = $t['İptal / İade Durumu'] ?? '';
    $txParaBirimi  = $t['Para Birimi'] ?? 'TRY';

    // Sadece başarılı & iade edilmemiş ödemelere bak
    if (strtolower($txOdemeDurumu) !== 'başarılı') continue;
    if (strtolower($txIadeStatus) === 'iade edildi') continue;

    if (!$txConvId) continue;

    if (!isset($orders[$txConvId])) {
        $hicBulunamayan[] = [
            'conv_id'     => $txConvId,
            'musteri_adi' => $txAd,
            'tutar'       => $txTutar,
            'tarih'       => $txTarih,
            'odeme_no'    => $txOdemeNo,
            'kart_son4'   => $txSon4,
            'kart_sahibi' => $txKartSahibi,
        ];
        continue;
    }

    $o        = $orders[$txConvId];
    $dbStatus = strtolower(trim($o['order_status'] ?? ''));

    if (in_array($dbStatus, $sorunluDurumlar)) {
        $sorunlular[] = [
            'sorun_turu'      => match(true) {
                in_array($dbStatus, ['pending_payment','unpaid','pending']) => '⚠️  ÖDEME ALINMADI OLARAK GÖRÜNÜYOR',
                in_array($dbStatus, ['cancelled','iptal'])                 => '❌ İPTAL EDİLMİŞ AMA ÖDEME ALINDI',
                default                                                    => '❓ BİLİNMEYEN DURUM',
            },
            'siparis_id'      => $o['id'],
            'musteri_adi'     => $o['customer_name'],
            'musteri_email'   => $o['customer_email'],
            'musteri_telefon' => $o['customer_phone'],
            'db_durumu'       => $o['order_status'],
            'db_tutari'       => number_format((float)$o['total_price'], 2, '.', '') . ' TL',
            'iyzico_tutari'   => $txTutar . ' ' . $txParaBirimi,
            'odeme_tarihi'    => $txTarih,
            'iyzico_odeme_no' => $txOdemeNo,
            'kart_son4'       => $txSon4,
            'kart_sahibi'     => $txKartSahibi,
            'iade_durumu'     => $txIadeStatus,
        ];
    } else {
        $normalOlanlar++;
    }
}

// ── 4. SONUÇ ────────────────────────────────────────────────────────────────
echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║         İYZİCO ÖDEME ANALİZİ — SORUNLU KAYITLAR                   ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

$totalSuccess = count($sorunlular) + $normalOlanlar + count($hicBulunamayan);
echo "Iyzico'da başarılı işlem sayısı : $totalSuccess\n";
echo "Sistemde düzgün görünen         : $normalOlanlar\n";
echo "SORUNLU (aşağıda detay)         : " . count($sorunlular) . "\n";
echo "Sistemde hiç bulunamayan        : " . count($hicBulunamayan) . "\n\n";

if (!empty($sorunlular)) {
    echo "══════════════════════════════════════════════════════════════════════\n";
    echo " SORUNLU SİPARİŞLER\n";
    echo "══════════════════════════════════════════════════════════════════════\n\n";
    $i = 1;
    foreach ($sorunlular as $p) {
        echo "┌─── [{$i}] {$p['sorun_turu']}\n";
        echo "│  Sipariş ID       : {$p['siparis_id']}\n";
        echo "│  Müşteri Adı      : {$p['musteri_adi']}\n";
        echo "│  E-posta          : {$p['musteri_email']}\n";
        echo "│  Telefon          : {$p['musteri_telefon']}\n";
        echo "│  Sistemdeki Durum : {$p['db_durumu']}\n";
        echo "│  Sistemdeki Tutar : {$p['db_tutari']}\n";
        echo "│  Iyzico Tutarı    : {$p['iyzico_tutari']}\n";
        echo "│  Ödeme Tarihi     : {$p['odeme_tarihi']}\n";
        echo "│  Iyzico Ödeme No  : {$p['iyzico_odeme_no']}\n";
        echo "│  Kart Son 4 Hane  : {$p['kart_son4']}\n";
        echo "│  Kart Sahibi      : {$p['kart_sahibi']}\n";
        echo "│  İade Durumu      : {$p['iade_durumu']}\n";
        echo "└──────────────────────────────────────────────────────────────────\n\n";
        $i++;
    }
}

if (!empty($hicBulunamayan)) {
    echo "══════════════════════════════════════════════════════════════════════\n";
    echo " SİSTEMDE KAYDEDILMEMIŞ ÖDEMELER (Conv.ID sistemde yok)\n";
    echo "══════════════════════════════════════════════════════════════════════\n\n";
    foreach ($hicBulunamayan as $p) {
        echo "  Conv.ID={$p['conv_id']} | {$p['musteri_adi']} | {$p['tutar']} TL | {$p['tarih']}\n";
        echo "    Ödeme No: {$p['odeme_no']} | Kart Son 4: {$p['kart_son4']} | Kart Sahibi: {$p['kart_sahibi']}\n\n";
    }
}
