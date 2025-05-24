<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
include '../assets/src/php/return_to.php';

$message = '';
$messageType = '';

try {
   include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $brand_name = trim($_POST['brand_name']);

      // Aynı marka adının olup olmadığını kontrol et
      $check = $vt->prepare("SELECT COUNT(*) FROM brands WHERE LOWER(brand_name) = LOWER(:brand_name)");
      $check->execute([':brand_name' => $brand_name]);
      $exists = $check->fetchColumn();

      if ($exists) {
         $message = "Bu marka zaten kayıtlı!";
         $messageType = "error";
      } else {
         // Yeni markayı ekle
         $stmt = $vt->prepare("INSERT INTO brands (brand_name) VALUES (:brand_name)");
         $stmt->execute([':brand_name' => $brand_name]);
         $message = "Yeni marka başarıyla eklendi!";
         $messageType = "success";
      }
   }

} catch (PDOException $e) {
   $message = "Veri tabanı hatası: " . $e->getMessage();
   $messageType = "error";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>

   <title>Yeni Marka Ekle</title>

</head>
<body>
   <?php if (!$kullanici_adi): ?>



   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>


   <?php else : ?>

      <h5>
         <i class="bi bi-plus-circle"></i> Yeni Marka Ekle
      </h5>



      <form method="post" class="border p-4">

         <?php
         include $_SERVER["DOCUMENT_ROOT"] .
         "/assets/src/functions/markaadi.php";

         echo('<hr>');

         include $_SERVER["DOCUMENT_ROOT"] .
         "/assets/src/code/button.php";

         ?>

      </form>

      <?php if (isset($_SESSION['previous_page'])): ?>
      <a href="<?php echo $_SESSION['previous_page']; ?>" class="btn btn-link mt-3 d-block text-center">
         <i class="fas fa-arrow-left me-2"></i> Geri Dön
      </a>

      <?php endif; ?>
      <?php endif; ?>
   </div>
</body>
</html>