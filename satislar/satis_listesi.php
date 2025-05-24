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

// Tarih aralığı kontrolü
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

// Tarih formatlarını kontrol et ve dönüştür
if ($start_date) {
   $start_date = DateTime::createFromFormat('Y-m-d\TH:i', $start_date);
   if ($start_date) {
      $start_date->setTime(0, 0, 0);
      $start_date = $start_date->format('Y-m-d H:i:s');
   } else {
      $start_date = null;
   }
}

if ($end_date) {
   $end_date = DateTime::createFromFormat('Y-m-d\TH:i', $end_date);
   if ($end_date) {
      $end_date->setTime(23, 59, 59);
      $end_date = $end_date->format('Y-m-d H:i:s');
   } else {
      $end_date = null;
   }
}

// Tarih filtresi kontrolü
$date_filter = $_GET['date_filter'] ?? '';

if ($date_filter === 'all') {
   $start_date = null;
   $end_date = null;
} elseif ($date_filter === 'today') {
   $today = new DateTime();
   $start_date = $today->format('Y-m-d') . ' 00:00:00';
   $end_date = $today->format('Y-m-d') . ' 23:59:59';
}

// Satışları sorgula
$query = "
 SELECT
    s.id AS sale_id,
    s.total_amount,
    s.sale_date,
    s.sale_code
FROM sales s
WHERE (:start_date IS NULL OR s.sale_date >= :start_date)
  AND (:end_date IS NULL OR s.sale_date <= :end_date)
ORDER BY s.sale_date ASC
";

try {
   $stmt = $vt->prepare($query);
   $stmt->execute([
      ':start_date' => $start_date,
      ':end_date' => $end_date
   ]);
   $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   die("Veritabanı hatası: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

   <title>Tarihsel Satış Raporu</title>
   <!-- Bootstrap CSS -->

</head>
<body>
   <?php if (!$kullanici_adi): ?>
   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>


   <?php else : ?>
   
      <h5 class="mb-4">
      <i class="bi bi-calendar-check"></i>
         Tarihsel Satış Raporu
      </h5>

      <!-- Tarih aralığı seçimi -->
      <form method="get" class="mb-4">
         <div class="form-group">
            <label for="date_filter">Tarih Seçimi</label>
            <select name="date_filter" id="date_filter" class="form-select" onchange="handleDateFilter(this.value)">
               <option value="">Tarih Seçin</option>
               <option value="all" <?= $date_filter === 'all' ? 'selected' : '' ?>>Tüm Zamanlar</option>
               <option value="today" <?= $date_filter === 'today' ? 'selected' : '' ?>>Bugün</option>
               <option value="custom" <?= $date_filter === 'custom' ? 'selected' : '' ?>>Özel Tarih Aralığı</option>
            </select>
         </div>

         <div id="customDateInputs" style="display: <?= $date_filter === 'custom' ? 'block' : 'none' ?>;">
            <div class="form-group">
               <label for="start_date">Başlangıç Tarihi ve Saati:</label>
               <input type="datetime-local" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
            </div>

            <div class="form-group">
               <label for="end_date">Bitiş Tarihi ve Saati:</label>
               <input type="datetime-local" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
            </div>
         </div>

         <button type="submit" class="btn btn-primary mt-3">Filtrele</button>
      </form>

      <hr>

      <!-- Satışlar Tablosu -->
      <?php if ($sales): ?>
      <table class="table table-bordered table-striped">
         <thead>
            <tr>
               <th>Satış ID</th>
               <th>Toplam Tutar</th>
               <th>Satış Tarihi</th>
               <th>Satış Kodu</th>
               <th>Detaylar</th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($sales as $sale): ?>
            <tr>
               <td><?= htmlspecialchars($sale['sale_id']) ?></td>
               <td><?= number_format($sale['total_amount'], 2) ?> ₺</td>
               <td><?= $sale['sale_date'] ? (new DateTime($sale['sale_date']))->format('d/m/Y H:i:s') : 'Tarih Yok' ?></td>
               <td><?= htmlspecialchars($sale['sale_code']) ?></td>
               <td><a href="satis_detaylari.php?sale_id=<?= $sale['sale_id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Detaylar</a></td>
            </tr>
            <?php endforeach; ?>
         </tbody>
      </table>
      <?php else : ?>
      <div class="alert alert-warning" role="alert">
         Belirtilen tarihler arasında satış bulunamadı.
      </div>
      <?php endif; ?>
   </div>

   <!-- Bootstrap JS (Opsiyonel) -->
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

   <script>
      function handleDateFilter(value) {
         const customDateInputs = document.getElementById('customDateInputs');
         if (value === 'custom') {
            customDateInputs.style.display = 'block';
         } else {
            customDateInputs.style.display = 'none';
         }
      }
   </script>
   <?php endif; ?>
</body>
</html>