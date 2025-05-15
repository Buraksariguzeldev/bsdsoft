<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
include '../assets/src/php/return_to.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   try {
      include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

      // Veritabanına yeni ürün grubu ekleme
      $stmt = $vt->prepare("INSERT INTO product_groups (group_name) VALUES (:group_name)");
      $result = $stmt->execute([':group_name' => $_POST['group_name']]);

      if ($result) {
         $message = "Yeni ürün grubu başarıyla eklendi!";
         $messageType = "success";
      }
   } catch (PDOException $e) {
      // Hata mesajını yakala ve özelleştir
      if ($e->getCode() == 23000) {
         // Duplicate entry hatası
         $message = "Bu ürün grubu zaten mevcut. Lütfen farklı bir ad giriniz.";
         $messageType = "error";
      } else {
         $message = "Beklenmeyen bir hata oluştu: " . $e->getMessage();
         $messageType = "error";
      }
   }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

   <title>Yeni Ürün Grubu Ekle</title>
</head>
<body>
   <?php if (!$kullanici_adi): ?>
   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>
   <?php else : ?>
   <h5>
      <i class="bi bi-plus-square"></i> Yeni Ürün Grubu Ekle
   </h5>

   <!-- Mesajları Göster -->
   <?php if ($message): ?>
   <div class="alert <?php echo $messageType == 'success' ? 'alert-success' : 'alert-danger'; ?> d-flex align-items-center" role="alert">
      <i class="fas <?php echo $messageType == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-2"></i>
      <span><?php echo $message; ?></span>
   </div>
   <?php endif; ?>

   <form method="POST" class="border p-4 rounded shadow-sm">
      <?php
      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/urungrubuadi.php";

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
</body>
</html>