<?php
session_start();

try {
  include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
// db pdo bu include yolunda bulunuyor 
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Müşteri bilgilerini al
$customerId = $_POST['customer_id'] ?? null;
$cashRegister = $_POST['cash_register'] ?? null;

// Seçili müşteri varsa session'a kaydet
if ($customerId) {
    $_SESSION['selected_customer'] = $customerId;

    // Müşteri bilgilerini veritabanından al
    $stmt = $vt->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$customerId]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($customer) {
        // Müşteri bilgilerini session'a kaydet
        $_SESSION['customer_info'] = [
            'id' => $customer['id'],
            'name' => $customer['name'],
            'phone' => $customer['phone'],
            'email' => $customer['email'],
            'address' => $customer['address'],
        ];
    }
} else {
    // Müşteri seçilmediyse session'dan müşteri bilgilerini temizle
    unset($_SESSION['selected_customer']);
    unset($_SESSION['customer_info']);
}

// Başarılı yanıt döndür
echo json_encode(['success' => true]);
?>