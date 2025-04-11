<?php
session_start();

include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');



// ID al
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo "Geçersiz ürün ID'si!";
    exit;
}

$productId = intval($_POST['id']);

// Ürün gruba bağlı mı?
$stmt = $vt->prepare("SELECT COUNT(*) FROM products WHERE product_group = :group_id");
$stmt->execute([':group_id' => $productId]);
$productCount = $stmt->fetchColumn();

if ($productCount > 0) {
    echo "Bu ürün gruba bağlı olduğu için silinemez!";
    exit;
}

// Ürünü sil
$stmt = $vt->prepare("DELETE FROM products WHERE id = :id");
if ($stmt->execute([':id' => $productId])) {
    echo "success";
} else {
    echo "Ürün silinirken hata oluştu.";
}
?>