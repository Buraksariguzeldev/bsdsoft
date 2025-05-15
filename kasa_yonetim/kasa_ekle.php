<?php
// Veritabanı bağlantısı
date_default_timezone_set('Europe/Istanbul');
try {
   include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
   $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   echo "Veritabanı bağlantı hatası: " . $e->getMessage();
}

// Kasa ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_name'])) {
   $register_name = $_POST['register_name'];
   $created_at = date('Y-m-d H:i:s'); // Kasa oluşturulma tarihi

   try {
      // Kasayı veritabanına ekleme işlemi
      $sql = "INSERT INTO cash_registers (register_name, created_at) VALUES (:register_name, :created_at)";
      $stmt = $vt->prepare($sql);
      $stmt->bindParam(':register_name', $register_name);
      $stmt->bindParam(':created_at', $created_at);

      if ($stmt->execute()) {
         $success_message = "Kasa başarıyla eklendi.";
      }
   } catch (PDOException $e) {
      // Hata mesajını kontrol et
      if ($e->getCode() == 23000) {
         // Integrity constraint violation (Duplicate entry)
         $error_message = "Bu kasa zaten mevcut. Lütfen farklı bir kasa adı girin.";
      } else {
         $error_message = "Hata: " . $e->getMessage();
      }
   }
}

include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
?>

<!DOCTYPE html>
<html lang="tr">
<head>

   <title>Kasa Ekle</title>

</head>
<body class="bg-light">

   <?php if (!$kullanici_adi): ?>

   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>

   <?php else : ?>

   <?php if (isset($success_message)): ?>
   <div class="alert alert-success mt-4" role="alert">
      <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
   </div>
   <?php elseif (isset($error_message)): ?>
   <div class="alert alert-danger mt-4" role="alert">
      <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
   </div>
   <?php endif; ?>

   
      <h5>
         <i class="bi bi-plus-circle"></i>
         Kasa Ekle
      </h5>

      <!-- Form Başlangıcı -->
      <form method="POST" class="shadow p-4 ">

         <?php
         include $_SERVER["DOCUMENT_ROOT"] .
         "/assets/src/functions/kasaadi.php";

         echo('<hr>');

         include $_SERVER["DOCUMENT_ROOT"] .
         "/assets/src/code/button.php";

         ?>


      </form>
      <!-- Form Bitişi -->




   <?php endif; ?>
</body>
</html>