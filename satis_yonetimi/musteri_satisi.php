<?php
// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');

// Türkiye saat dilimini ayarla
date_default_timezone_set('Europe/Istanbul');

// Türkçe tarih formatı
function turkishDateFormat($timestamp = null) {
    $date = new DateTime();
    $date->setTimestamp($timestamp ?? time());
    return $date->format('Y-m-d H:i:s'); // MySQL tarih formatı
}

session_start();

try {
   include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php'); // MySQL bağlantısını sağlayan dosya
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Gerekli oturum verilerini kontrol et
if (!isset($_SESSION['selected_customer'], $_SESSION['selected_cash_register'])) {
    die("Gerekli oturum verileri eksik.");
}

$customer_id = $_SESSION['selected_customer'];
$selected_cash_register = $_SESSION['selected_cash_register'];
$cart = $_SESSION['carts'][$selected_cash_register] ?? [];

// Sepet boşsa hata döndür
if (empty($cart)) {
    die("Sepet boş, işlem gerçekleştirilemedi.");
}

// Toplam tutarı hesapla
$total_amount = array_reduce($cart, function ($carry, $item) {
    $price = $item['price'] ?? 0;
    $quantity = $item['is_kg'] ? ($item['weight'] ?? 0) : ($item['quantity'] ?? 0);
    return $carry + ($price * $quantity);
}, 0);

// Müşteri adı al
$customerStmt = $vt->prepare("SELECT name FROM customers WHERE id = ?");
$customerStmt->execute([$customer_id]);
$customer = $customerStmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    die("Müşteri bulunamadı.");
}

try {
    $vt->beginTransaction();

    // Müşteri satış işlemi
    $stmt = $vt->prepare("INSERT INTO customer_sales (customer_id, total_amount, sale_date) VALUES (?, ?, ?)");
    $stmt->execute([$customer_id, $total_amount, turkishDateFormat()]);
    $customer_sale_id = $vt->lastInsertId();  // Burada customer_sale_id'yi alın

    // Satış detayları ve satış izleme işlemleri
    $detailStmt = $vt->prepare("
        INSERT INTO customer_sales_details 
        (customer_sale_id, product_id, product_name, quantity, price, is_kg, total_price) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($cart as $item) {
        $quantity = $item['is_kg'] ? ($item['weight'] ?? 0) . ' kg' : ($item['quantity'] ?? 0) . ' adet';
        $total_price = $item['price'] * ($item['is_kg'] ? $item['weight'] : $item['quantity']);
        $detailStmt->execute([
            $customer_sale_id,  // Yeni customer_sale_id
            $item['id'] ?? null,
            $item['name'] ?? '',
            $quantity,
            $item['price'] ?? 0,
            $item['is_kg'] ?? 0,
            $total_price
        ]);
    }

    // Satış takibi
    $trackingStmt = $vt->prepare("
        INSERT INTO sales_tracking (sale_date, sale_type, customer_name, customer_sale_id, sale_id, total_amount) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $trackingStmt->execute([
        turkishDateFormat(),
        'customer',  // Satış türü
        $customer['name'],  // Müşteri adı
        $customer_sale_id,  // Müşteri satış ID'si
        null,  // Sale ID burada NULL olmalı
        $total_amount
    ]);

    $vt->commit();

    // Sepeti temizle
    unset($_SESSION['carts'][$selected_cash_register]);

    // Başarılı mesaj ve yönlendirme
    header("Location: satis_paneli.php?success=1");
    exit();

} catch (Exception $e) {
    $vt->rollBack();
    error_log("Satış hatası: " . $e->getMessage());
    die("Satış kaydedilemedi: " . $e->getMessage());
}
?>