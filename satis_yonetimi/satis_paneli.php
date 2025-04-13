<?php

include 'satis_tag.php';
?>
<style>
    .small-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
    #cash-register-info{
        display: flex;
        flex-direction: column;
    }
    .kasa-btn-group {
    display: flex;
    gap: 10px; 
    justify-content: center;
    align-items: center;
    flex-wrap: wrap; 
}
.kasa-btn-group .btn {
    padding: 0.5rem 1rem; 
    font-size: 0.9rem; 
}

    /* Kasa Seçimi için Stil */
    .sepet-ozeti {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .sepet-ozeti h4 {
        border-bottom: 1px solid #ced4da;
        padding-bottom: 0.75rem;
        margin-bottom: 1rem;
    }
    .sepet-listesi {
        max-height: 250px;
        overflow-y: auto;
        margin-bottom: 1rem;
    }
    .sepet-listesi .list-group-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    .toplam-tutar {
        font-size: 1.2rem;
        font-weight: bold;
        text-align: right;
        margin-top: 1rem;
        border-top: 1px solid #ced4da;
        padding-top: 0.75rem;
    }
    .kasa-secimi-formu .btn {
        margin-bottom: 0.5rem; /* Butonlar arası boşluk */
    }

</style>
<div id="cash-register-info" class="container mt-3">
    <div id="kasa-buttons" class="kasa-btn-group">
    </div>
</div>

<!DOCTYPE html>
<html lang="tr">
<head>
   <title>Kasa Sistemi</title>
</head>
<body>

<?php if (!$kullanici_adi): ?> <a href="../auth/login.php" class="btn btn-link text-decoration-none">
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
      
      <?php
      
      // Kasa Seçimi Kısmı
      
      // Sepeti al
      $sepet = isset($_SESSION['sepet']) ? $_SESSION['sepet'] : [];
      $toplam_tutar = 0;
      
      // Sepet boşsa veya geçerli değilse
      if (empty($sepet) || !is_array($sepet)) {
          echo '<p class="alert alert-warning">Sepetiniz boş. Lütfen ürün ekleyin.</p>';
      } else {
          // Toplam tutarı hesapla
          foreach ($sepet as $urun_id => $urun_detay) {
              if (isset($urun_detay['fiyat']) && isset($urun_detay['miktar'])) {
                  $toplam_tutar += $urun_detay['fiyat'] * $urun_detay['miktar'];
              }
          }
          
          // Kasaları veritabanından çek
          $kasalar = [];
          try {
              $stmt = $vt->query("SELECT id, register_name FROM cash_registers WHERE status = 1 ORDER BY register_name");
              $kasalar = $stmt->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
              error_log("Kasa seçimi - Kasa listesi alınamadı: " . $e->getMessage());
              echo '<p class="alert alert-danger">Kasalar yüklenirken bir hata oluştu.</p>';
          }
          
          // Kasa seçimi form gönderildiğinde
          if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kasa_id'])) {
              $secilen_kasa_id = intval($_POST['kasa_id']);
              
              // Seçilen kasanın geçerli olup olmadığını kontrol et
              $kasa_gecerli = false;
              foreach ($kasalar as $kasa) {
                  if ($kasa['id'] == $secilen_kasa_id) {
                      $kasa_gecerli = true;
                      break;
                  }
              }
              
              if ($kasa_gecerli) {
                  $_SESSION['secilen_kasa_id'] = $secilen_kasa_id;
                  // Kasa seçildikten sonra satışı tamamlama sayfasına yönlendir
                  header('Location: satisi_tamamla.php');
                  exit;
              } else {
                  echo '<p class="alert alert-danger">Geçersiz kasa seçimi.</p>';
              }
          }
          
          // Kasa Seçimi Formu
          ?>
          <hr>
          <div class="card shadow-sm">
              <div class="card-header text-center bg-light">
                  <h3 class="mb-0 h4"><i class="fas fa-cash-register me-2"></i>Kasa Seçimi</h3>
              </div>
              <div class="card-body p-4">
                  <?php if (!empty($kasalar)): ?>
                      <p class="text-center text-muted mb-3">Lütfen satış işlemini tamamlamak için bir kasa seçin.</p>
                      <form method="post" action="" class="kasa-secimi-formu">
                          <div class="d-grid gap-2">
                              <?php foreach ($kasalar as $kasa): ?>
                                  <button type="submit" name="kasa_id" value="<?php echo htmlspecialchars($kasa['id']); ?>" class="btn btn-outline-primary btn-lg">
                                      <i class="fas fa-desktop me-2"></i><?php echo htmlspecialchars($kasa['register_name']); ?>
                                  </button>
                              <?php endforeach; ?>
                          </div>
                      </form>
                  <?php else: ?>
                      <div class="alert alert-warning text-center">Aktif kasa bulunamadı. Lütfen sistem yöneticinizle iletişime geçin.</div>
                  <?php endif; ?>
              </div>
          </div>
          <?php
      }
      
      // Kasa Seçimi Kısmı Bitişi
      
      ?>

      <div class="payment-form border p-3">
         <form id="payment-form" method="POST" action="satisi_tamamla.php">
            <fieldset id="complete-sale-btn" class="btn-group w-100 mb-3" role="group">
               <button type="submit" name="payment_type" value="cash" class="btn btn-success small-btn" <?php echo $total <= 0 ? 'disabled' : ''; ?>>
                  <i class="fas fa-money-bill-wave"></i> Nakit Ödeme
               </button>
               <button type="submit" name="payment_type" value="card" class="btn btn-info small-btn" <?php echo $total <= 0 ? 'disabled' : ''; ?>>
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
   include 'kasa_bilgisi_getir.php';
   ?>

   <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
   <script src="assets/src/js/klavye.js"></script>

   <?php endif; ?>
</body>
</html>