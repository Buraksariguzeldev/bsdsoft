<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
date_default_timezone_set('Europe/Istanbul');
setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'turkish');

// Veritabanı bağlantısı
try {
   include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
   $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   die("Veritabanına bağlanılamadı: " . $e->getMessage());
}

// Satış ID'yi al
$sale_id = $_GET['sale_id'] ?? null;

if (!$sale_id) {
   die("Satış ID'si bulunamadı.");
}

// Satış ve ürün detaylarını sorgula
$query = "
  SELECT
    s.id AS sale_id,  -- sales tablosundaki id sütunu kullanılıyor
    s.total_amount,
    s.sale_date,
    s.sale_code,
    p.product_name,
    sp.quantity,
    sp.unit_price,
    sp.total_price
FROM sales s
LEFT JOIN sales_products sp ON s.id = sp.sale_id  -- sales tablosundaki id ile eşleştirme yapılıyor
LEFT JOIN products p ON sp.product_id = p.id
WHERE s.id = :sale_id
";

try {
   $stmt = $vt->prepare($query);
   $stmt->execute([':sale_id' => $sale_id]);
   $sale_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   die("Veritabanı hatası: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

   <title>Satış Detayları</title>


</head>
<body>
   <?php if (!$kullanici_adi): ?>



   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>


   <?php else : ?>

   <div class="container mt-5">
      <h1 class="mb-4">Satış Detayları - Satış ID: <?= htmlspecialchars($sale_id) ?></h1>

      <?php if ($sale_details): ?>
      <!-- Satış Ürünleri Tablosu -->
      <div class="table-responsive">
         <table class="table table-bordered table-striped">
            <thead class="table-dark">
               <tr>
                  <th>Ürün Adı</th>
                  <th>Miktar</th>
                  <th>Birim Fiyatı</th>
                  <th>Toplam Fiyat</th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($sale_details as $detail): ?>
               <tr>
                  <td><?= htmlspecialchars($detail['product_name']) ?></td>
                  <td><?= htmlspecialchars($detail['quantity']) ?></td>
                  <td><?= number_format($detail['unit_price'], 2) ?> ₺</td>
                  <td><?= number_format($detail['total_price'], 2) ?> ₺</td>
               </tr>
               <?php endforeach; ?>
            </tbody>
         </table>
      </div>

      <!-- Satış Özeti -->
      <div class="mt-4">
         <h3 class="mb-3">Satış Özeti</h3>
         <table class="table table-bordered">
            <tr>
               <th>Toplam Satış Tutarı</th>
               <td><?= number_format($sale_details[0]['total_amount'], 2) ?> ₺</td>
            </tr>
            <tr>
               <th>Satış Kodu</th>
               <td><?= htmlspecialchars($sale_details[0]['sale_code']) ?></td>
            </tr>
            <tr>
               <th>Satış Tarihi</th>
               <td><?= (new DateTime($sale_details[0]['sale_date']))->format('d/m/Y H:i:s') ?></td>
            </tr>
         </table>
      </div>
      <?php else : ?>
      <div class="alert alert-warning" role="alert">
         Bu satışa ait detaylar bulunamadı.
      </div>
      <?php endif; ?>
   </div>

   <!-- Bootstrap JS (Opsiyonel) -->


   <?php endif; ?>
</body>
</html>