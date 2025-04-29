<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

if (!$kullanici_adi) {
    http_response_code(403);
    die("Yetkisiz erişim!");
}

try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        http_response_code(400);
        die("Geçersiz kasa ID'si!");
    }

    $id = $_GET['id'];

    // Kasayı sil
    $sql = "DELETE FROM cash_registers WHERE id = :id";
    $stmt = $vt->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        http_response_code(500);
        die("Kasa silinirken bir hata oluştu.");
    }

} catch (PDOException $e) {
    http_response_code(500);
    die("Veritabanı hatası: " . $e->getMessage());
}
?>
