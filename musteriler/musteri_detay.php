<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
// Hata ayıklama modu
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bağlantısı
try {
   include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
   $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Müşteri ID'sini al
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

// Müşteri bilgilerini getir
$customer_query = $vt->prepare("SELECT * FROM customers WHERE id = :customer_id");
$customer_query->execute([':customer_id' => $customer_id]);
$customer = $customer_query->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
   die("Müşteri bulunamadı.");
}

// Müşteri toplam borcunu ve ödemelerini hesapla
$totalSalesQuery = $vt->prepare("
    SELECT
        COALESCE(SUM(total_amount), 0) AS total_sales,
        COALESCE(SUM(paid_amount), 0) AS total_paid
    FROM customer_sales
    WHERE customer_id = :customer_id
   ");
$totalSalesQuery->execute([':customer_id' => $customer_id]);
$salesData = $totalSalesQuery->fetch(PDO::FETCH_ASSOC);

$totalDebt = $salesData['total_sales'] - $salesData['total_paid'];

// Müşteri işlemlerini al (satışlar ve ödemeler)
$transactions_query = $vt->prepare("
    SELECT 'sale' AS type, id, total_amount AS amount, sale_date AS date, NULL AS description
    FROM customer_sales WHERE customer_id = :customer_id
    UNION ALL
    SELECT 'payment' AS type, id, amount, payment_date AS date, description
    FROM payments WHERE customer_id = :customer_id
    ORDER BY date DESC
   ");
$transactions_query->execute([':customer_id' => $customer_id]);
$transactions = $transactions_query->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="tr">
<head>

   <title>Müşteri Detayları</title>

</head>
<body class="container mt-4">

   <?php if (!$kullanici_adi): ?>



   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>


   <?php else : ?>




   <h5>
      <i class="bi bi-person-vcard"></i>
      Müşteri Detayları: <br>
      <?= customer($customer["name"]); ?>
   </h5>

   <div class="p-3 musteri-bilgileri">
      <p>
         <i class="fas fa-phone"></i>
         Telefon no: <?= customerPhone($customer['phone']) ?>
      </p>
      <p>
         <i class="bi-calendar"></i>
         <?= customer($customer['created_at']) ?>
      </p>
      <p>
         <i class="fas fa-envelope"></i>
         Email:
         <?= customer($customer['email']) ?>
      </p>
      <p>
         <i class="fas fa-map-marker-alt"></i> Adres:
         <?= customer($customer['address']) ?>
      </p>
      <p>
         <i class="fas fa-wallet"></i> Toplam Borç: ve <?=
         customer($totalDebt > 0 ? $totalDebt : 0, 2) ?> TL
      </p>

   </div>
   <hr>
   <h5 class="p-3">
      <i class="bi bi-credit-card-2-front"></i> Ödeme Yap
   </h5>


   <!-- OdÖdeme formu -->

   <form action="odeme_yap.php" method="POST" class="mb-3">
      <input type="hidden" name="customer_id" value="<?= $customer_id ?>">


      <div class="p-3 bg-secondary ">
         <label for="amount" class="form-label"> <i class="bi bi-currency-exchange"></i>Tutar:
         </label>

         <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
      </div>
      <hr>
      <div class="bg-secondary ">
         <div class="p-3">
            <label for="description" class="form-label"><i class="bi bi-card-text"></i>Açıklama:</label>
            <input type="text" name="description" id="description" class="form-control">
         </div>
      </div>
      <br>


      <button type="button" class=" bg-secondary w-100 " onclick="document.getElementById('amount').value = <?= $totalDebt ?>;">
         <i class="fas fa-coins"></i> Tümünü Öde
      </button>
      <hr>
      <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-paper-plane"></i> Gönder</button>
      <br>

   </form>

   <h5>
      <i class="fas fa-list"></i> Satışlar ve Ödemeler</h5>
   <table class="table table-striped table-bordered">
      <thead class="table-dark">
         <tr>
            <th><i class="fas fa-calendar-alt"></i> Tarih</th>
            <th><i class="fas fa-tags"></i> Tür</th>
            <th><i class="fas fa-money-bill-wave"></i> Tutar</th>
            <th><i class="fas fa-comment-dots"></i> Açıklama</th>
            <th><i class="fas fa-info-circle"></i> Satış Detay</th>
         </tr>
      </thead>
      <tbody>
         <?php foreach ($transactions as $transaction): ?>
         <tr>
            <td><?= customer($transaction['date']) ?></td>
            <td><?= $transaction['type'] === 'sale' ? 'Satış' : 'Ödeme' ?></td>
            <td><?= number_format($transaction['amount'], 2) ?> TL</td>
            <td><?= customer($transaction['description'] ?? '-') ?></td>
            <td>
               <?php if ($transaction['type'] === 'sale'): ?>
               <button class="btn btn-info btn-sm" onclick="window.location.href='satis_detay_getir.php?sale_id=<?= $transaction['id'] ?>'">
                  <i class="fas fa-info-circle"></i> Satış Detayları
               </button>
               <?php else : ?>
               <span>-</span>
               <?php endif; ?>
            </td>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>

   <?php endif; ?>
</body>
</html>