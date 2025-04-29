<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

if (!$kullanici_adi) {
    http_response_code(403);
    die("Yetkisiz erişim!");
}

try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    
    // ID kontrolü
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        http_response_code(400);
        die("Geçersiz grup ID'si!");
    }

    $groupId = intval($_GET['id']);

    // Gruba bağlı ürün var mı kontrolü
    $stmt = $vt->prepare("SELECT COUNT(*) FROM products WHERE product_group = :group_id");
    $stmt->execute([':group_id' => $groupId]);
    $productCount = $stmt->fetchColumn();

    if ($productCount > 0) {
        die("Bu gruba bağlı ürünler olduğu için silinemez!");
    }

    // Grubu sil
    $stmt = $vt->prepare("DELETE FROM product_groups WHERE id = :id");
    if ($stmt->execute([':id' => $groupId])) {
        echo "success";
    } else {
        http_response_code(500);
        die("Grup silinirken bir hata oluştu.");
    }

} catch (PDOException $e) {
    http_response_code(500);
    die("Veritabanı hatası: " . $e->getMessage());
}
?>
