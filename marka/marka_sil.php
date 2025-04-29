<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    $id = $_GET['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        die("Geçersiz marka ID'si.");
    }

    $stmt = $vt->prepare("DELETE FROM brands WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    echo "success";
} catch (PDOException $e) {
    http_response_code(500);
    die("Veri tabanı hatası: " . $e->getMessage());
}
?>
