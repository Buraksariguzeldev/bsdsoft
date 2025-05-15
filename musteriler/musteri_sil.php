<?php include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>
 


<?php


// Veritabanı bağlantısı
try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanına bağlanılamadı: " . $e->getMessage());
}

// Müşteri silme işlemi
if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // Güvenlik için int değer alınır

    try {
        $stmt = $vt->prepare("DELETE FROM customers WHERE id = ?");
        $stmt->execute([$id]);

        // Yönlendirme mesajı
        echo "<div class='alert alert-success mt-4'>
                <i class='fas fa-check-circle'></i> Müşteri başarıyla silindi. Yönlendiriliyorsunuz...
              </div>";

        // JavaScript ile yönlendirme
        echo "<script type='text/javascript'>
                setTimeout(function() {
                    window.location.href = 'musteriler.php';
                }, 1000); // 1 saniye sonra yönlendir
              </script>";

        exit;
    } catch (PDOException $e) {
        die("Müşteri silinirken hata oluştu: " . $e->getMessage());
    }
} else {
    die("Geçersiz veya eksik ID.");
}
?>
    <?php endif; ?>