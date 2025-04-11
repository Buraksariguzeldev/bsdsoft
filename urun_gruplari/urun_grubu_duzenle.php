<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');



if (!isset($_GET['id']) || empty($_GET['id'])) {
   die("Geçersiz ürün ID'si!");
}

if (isset($_GET['id'])) {
   try {
      include($_SERVER['DOCUMENT_ROOT'] .
         '/assets/src/config/vt_baglanti.php');

      // Ürün grubunun mevcut bilgilerini al
      $stmt = $vt->prepare("SELECT * FROM product_groups WHERE id = :id");
      $stmt->execute([':id' => $_GET['id']]);
      $group = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$group) {
         echo "Ürün grubu bulunamadı!";
         exit;
      }
   } catch (PDOException $e) {
      echo "Veri tabanına bağlanılamadı: " . $e->getMessage();
      exit;
   }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   try {
      include($_SERVER['DOCUMENT_ROOT'] .
         '/assets/src/config/vt_baglanti.php');

      // Ürün grubunu güncelle
      $stmt = $vt->prepare("UPDATE product_groups SET group_name = :group_name WHERE id = :id");
      $result = $stmt->execute([
         ':group_name' => $_POST['group_name'],
         ':id' => $_GET['id']
      ]);

      if ($result) {
         // Yönlendirme mesajı
         echo "Yönlendiriliyorsunuz...";

         echo "<script type='text/javascript'>
                    setTimeout(function() {
                        window.location.href = 'urun_grubu_listesi.php';
                    }, 1000); // 1 saniye sonra yönlendir
                  </script>";
         exit;
      } else {
         echo "Aynı isimden zaten var"; // Hata durumu
      }
   } catch (PDOException $e) {
      echo "Hata oluştu: " . $e->getMessage(); // Hata durumu
   }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

   <title>Ürün Grubu Düzenle</title>

</head>
<body>
   <?php if (!$kullanici_adi): ?>



   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>


   <?php else : ?>

   
      <h5>
         <i class="bi bi-pencil-square"></i> Ürün Grubu Düzenle
      </h5>

      <form method="POST">

         <?php
         include $_SERVER["DOCUMENT_ROOT"] .
         "/assets/src/functions/urungrubuadi.php";

         echo('<hr>');

         include $_SERVER["DOCUMENT_ROOT"] .
         "/assets/src/code/button.php";

         ?>

      </form>

     

   <?php endif; ?>
</body>
</html>