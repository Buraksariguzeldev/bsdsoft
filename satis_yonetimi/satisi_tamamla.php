<?php
session_start();
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');
date_default_timezone_set('Europe/Istanbul');

// Veritabanı bağlantısını yap
try {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
    die("Veritabanı bağlantı hatası!");
}

// Sadece POST isteği geldiğinde çalıştır
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $register_id = $_SESSION['selected_cash_register'] ?? null;
        $total_amount = $_POST['total_amount'] ?? 0;
        $cart = $_SESSION['carts'][$register_id] ?? [];

        if (!$register_id || empty($cart)) {
            throw new Exception("Geçersiz satış işlemi!");
        }

        $vt->beginTransaction(); // Transaction başlat

        // Satış kaydını ekle
        $stmt = $vt->prepare("INSERT INTO sales (register_id, total_amount, sale_date, sale_code) 
                              VALUES (:register_id, :total_amount, NOW(), :sale_code)");
        $sale_code = uniqid('sale_');
        $stmt->execute([
            ':register_id' => $register_id,
            ':total_amount' => $total_amount,
            ':sale_code' => $sale_code
        ]);
        $sale_id = $vt->lastInsertId(); // Yeni eklenen satışın ID'si

        // Ürünleri ekle (id'yi manuel vermeye gerek yok)
        $stmt = $vt->prepare("INSERT INTO sales_products (sale_id, product_id, quantity, unit_price, total_price, sale_code, is_kg) 
                              VALUES (:sale_id, :product_id, :quantity, :unit_price, :total_price, :sale_code, :is_kg)");

        foreach ($cart as $item) {
            $is_kg = isset($item['is_kg']) ? (int)$item['is_kg'] : 0;
            $quantity = $is_kg ? $item['weight'] : $item['quantity'];
            $total_price = $item['price'] * $quantity;

            $stmt->execute([
                ':sale_id' => $sale_id,
                ':product_id' => $item['id'],
                ':quantity' => $quantity,
                ':unit_price' => $item['price'],
                ':total_price' => $total_price,
                ':sale_code' => $sale_code,
                ':is_kg' => $is_kg
            ]);
        }

        // Satış takip kaydını ekle
        $stmt = $vt->prepare("INSERT INTO sales_tracking (sale_date, sale_type, customer_name, customer_sale_id, sale_id, total_amount) 
                              VALUES (NOW(), 'sales', :customer_name, NULL, :sale_id, :total_amount)");
        $stmt->execute([
            ':customer_name' => $_SESSION['customer']['name'] ?? 'Müşterisiz Satış',
            ':sale_id' => $sale_id,
            ':total_amount' => $total_amount
        ]);

        $vt->commit(); // Transaction'ı tamamla

        unset($_SESSION['carts'][$register_id]); // Sepeti temizle

        header("Location: satis_paneli.php?success=1");
        exit;

    } catch (Exception $e) {
        $vt->rollBack();
        error_log("Satış işlemi sırasında hata: " . $e->getMessage());
        header("Location: satis_paneli.php?error=1");
        exit;
    }
}

header("Location: satis_paneli.php");
exit;
?>