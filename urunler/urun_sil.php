       <?php include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php'); if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>


<?php
session_start();

// Giriş kontrolü


// Veri tabanı bağlantısı
try {
   include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
} catch (PDOException $e) {
    die("Veri tabanına bağlanılamadı: " . $e->getMessage());
}

// Ürün ID'sini al
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Geçersiz ürün ID'si!");
}

$productId = $_GET['id'];

// Ürüne ait bilgileri çek (resim dosyasını silmek için)
$stmt = $vt->prepare("SELECT image_path FROM products WHERE id = :id");
$stmt->execute([':id' => $productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Ürün bulunamadı!");
}

// Veri tabanından ürünü sil
$stmt = $vt->prepare("DELETE FROM products WHERE id = :id");
$stmt->execute([':id' => $productId]);

// Resim dosyasını sil
if (!empty($product['image_path']) && file_exists($product['image_path'])) {
    unlink($product['image_path']); // Dosyayı sil
}

// 1 saniye bekle
sleep(1);

// Yönlendirme mesajı
echo '<div class="container mt-5">
        <div class="alert alert-success text-center">
            <i class="fas fa-check-circle"></i> Ürün başarıyla silindi!
        </div>
        <div class="text-center">
            <p>Yönlendiriliyorsunuz...</p>
        </div>
      </div>';

// JavaScript ile yönlendirme yap
echo "<script type='text/javascript'>
        setTimeout(function() {
            window.location.href = 'urun_listesi.php';
        }, 1000); // 1 saniye sonra yönlendir
      </script>";

exit;
?>
    <?php endif; ?>