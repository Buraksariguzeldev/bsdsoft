<?php include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
?>

<?php
// Veritabanı bağlantısı
date_default_timezone_set('Europe/Istanbul');
try {
   include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
   $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   echo "Veritabanı bağlantı hatası: " . $e->getMessage();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
   die("Geçersiz ürün ID'si!");
}

// Kasa düzenleme işlemi
if (isset($_GET['id'])) {
   $id = $_GET['id'];

   // Kasa bilgilerini al
   $sql = "SELECT * FROM cash_registers WHERE id = :id";
   $stmt = $vt->prepare($sql);
   $stmt->bindParam(':id', $id);
   $stmt->execute();
   $cash_register = $stmt->fetch(PDO::FETCH_ASSOC);

   if (!$cash_register) {
      die("Kasa bulunamadı.");
   }

   // Form gönderildiğinde güncelleme işlemi yapılır
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_name'])) {
      $register_name = $_POST['register_name'];
      $updated_at = date('Y-m-d H:i:s'); // Güncellenmiş tarih

      try {
         $sql = "UPDATE cash_registers SET register_name = :register_name, created_at = :updated_at WHERE id = :id";
         $stmt = $vt->prepare($sql);
         $stmt->bindParam(':register_name', $register_name);
         $stmt->bindParam(':updated_at', $updated_at);
         $stmt->bindParam(':id', $id);

         // Burada benzersiz kayıt hatasını yakalıyoruz
         if ($stmt->execute()) {
            // Başarı mesajı ve yönlendirme
            $success_message = "Kasa başarıyla güncellendi.";

            // JavaScript ile yönlendirme
            echo "<script type='text/javascript'>
                        setTimeout(function() {
                            window.location.href = 'kasa_listesi.php';
                        }, 1000); // 1 saniye sonra yönlendir
                    </script>";
         } else {
            $error_message = "Kasa güncellenirken bir hata oluştu.";
         }
      } catch (PDOException $e) {
         // Bu kısımda SQLSTATE[23000] hatasını yakalayıp, kullanıcıya anlamlı bir mesaj veriyoruz
         if ($e->getCode() == 23000) {
            $error_message = "Bu kasa zaten mevcut. Lütfen farklı bir isim girin.";
         } else {
            $error_message = "Hata: " . $e->getMessage();
         }
      }
   }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>

   <title>Kasa Düzenle</title>

</head>
<body>
   <?php if (!$kullanici_adi): ?>



   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>


   <?php else : ?>

      <h5>
         <i class="bi bi-wallet-fill"></i> 
         Kasa Düzenle
      </h5>

      <!-- Başarı ve Hata Mesajları -->
      <?php if (isset($success_message)): ?>
      <div class="alert alert-success mt-4" role="alert">
         <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
      </div>
      <?php elseif (isset($error_message)): ?>
      <div class="alert alert-danger mt-4" role="alert">
         <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
      </div>
      <?php endif; ?>

      <!-- Kasa Düzenleme Formu -->
      <form method="POST">

         <?php
         include $_SERVER["DOCUMENT_ROOT"] .
         "/assets/src/functions/kasaadi.php";

         echo('<hr>');

         include $_SERVER["DOCUMENT_ROOT"] .
         "/assets/src/code/button.php";

         ?>

      </form>

      <!-- Geri Dön Butonu -->
   </div>


   <?php endif; ?>
</body>
</html>