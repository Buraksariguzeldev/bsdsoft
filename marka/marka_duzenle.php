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

   // Markayı al
   $stmt = $vt->prepare("SELECT * FROM brands WHERE id = :id");
   $stmt->execute([':id' => $id]);
   $brand = $stmt->fetch(PDO::FETCH_ASSOC);

   if (!$brand) {
      die("Marka bulunamadı.");
   }

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $brand_name = $_POST['brand_name'];

      // Markayı güncelle
      $stmt = $vt->prepare("UPDATE brands SET brand_name = :brand_name WHERE id = :id");
      $stmt->execute([':brand_name' => $brand_name, ':id' => $id]);

      // Başarı mesajı ve yönlendirme
      echo "<div class='alert alert-success' role='alert'>
        Marka başarıyla düzenlendi! Yönlendiriliyorsunuz...
      </div>";

      // Yönlendirme işlemi
      echo "<script type='text/javascript'>
            setTimeout(function() {
                window.location.href = 'marka_listesi.php';
            }, 2000); // 2 saniye sonra yönlendir
          </script>";
      exit;
   }
} catch (PDOException $e) {
   die("Veri tabanına bağlanılamadı: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

   <title>Marka Düzenle</title>

</head>
<body>
   <?php if (!$kullanici_adi): ?>



   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>


   <?php else : ?>
   
      <h5>
         <i class="bi bi-building-gear"></i> 
         Marka Düzenle
      </h5>
      
      <form method="post" class="mt-4">


         <?php
         include $_SERVER["DOCUMENT_ROOT"] .
         "/assets/src/functions/markaadi.php";

         echo('<hr>');

         include $_SERVER["DOCUMENT_ROOT"] .
         "/assets/src/code/button.php";

         ?>

      </form>

      <br>
      <a href="marka_listesi.php" class="btn btn-info">
         <i class="fas fa-arrow-left"></i> Markalarına Geri Dön
      </a>
   </d>
   <?php endif; ?>
</body>
</html>