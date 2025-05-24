<?php
session_start();

// Navbar dahil et
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

try {
    // Veritabanı bağlantısını dahil et
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

    // Marka ID'sini al
    $id = $_GET['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        die("Geçersiz marka ID'si.");
    }

    // Markayı veri tabanından sil
    $stmt = $vt->prepare("DELETE FROM brands WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Silme işlemi başarılıysa kullanıcıya mesaj göster
echo "<div class='alert alert-success' role='alert'>
        Marka başarıyla silindi Yönlendiriliyorsunuz...
      </div>";

    // Yönlendirme işlemi
    echo "<script type='text/javascript'>
            setTimeout(function() {
                window.location.href = 'marka_listesi.php';
            }, 2000); // 2 saniye sonra yönlendir
          </script>";

} catch (PDOException $e) {
    die("Veri tabanı hatası: " . $e->getMessage());
}
?>