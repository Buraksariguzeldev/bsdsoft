<?php

include 'satis_tag.php';

?>
<div id="cash-register-info" class="container mt-3"></div>

<!DOCTYPE html>
<html lang="tr">
<head>
   <title>Kasa Sistemi</title>
</head>
<body>

   <?php if (!$kullanici_adi): ?>

   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>

   <?php else : ?>


      <!-- Müşteri Seçimi -->
      <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/code/musterisec.php"; ?>

<hr>

      <!-- Hızlı Satış -->
      <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/code/hizlisatis.php"; ?>
      
      <hr>

  <form id="barcode-form" method="POST">
      <!-- Barkod Okuyucu -->
      <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/functions/barkodno.php"; ?>
      
      </form>
      
      <hr>
      
       <form id="name-search-form" method="POST">  
      <!-- Ürün Adı ile Arama -->
      <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/functions/urunadi.php"; ?>
      </form>

      <hr>
      
      <!-- Sepet tablosunu değiştirin -->
      <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/code/sepet.php"; ?>
      
      <hr>

      <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/code/tutar.php"; ?>

      <hr>

      <div class="payment-form border p-3">
         <form id="payment-form" method="POST" action="satisi_tamamla.php">
            <fieldset id="complete-sale-btn" class="btn-group w-100 mb-3" role="group">
               <button type="submit" name="payment_type" value="cash" class="btn btn-success" <?php echo $total <= 0 ? 'disabled' : ''; ?>>
                  <i class="fas fa-money-bill-wave"></i> Nakit Ödeme
               </button>
               <button type="submit" name="payment_type" value="card" class="btn btn-info" <?php echo $total <= 0 ? 'disabled' : ''; ?>>
                  <i class="fas fa-credit-card"></i> Kredi Kartı
               </button>
            </fieldset>

            <!-- Müşteriye Satış Yap Butonu -->
            <button type="button" id="customer-sale-btn" class="btn btn-primary w-100 mt-3" onclick="window.location.href='musteri_satisi.php'">
               <i class="fas fa-user"></i> Müşteriye Satış Yap
            </button>
         </form>
      </div>

   </div>

   <?php
   include 'satis_script.php';
   ?>

   <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
   <script src="assets/src/js/klavye.js"></script>

   <?php endif; ?>
</body>
</html>