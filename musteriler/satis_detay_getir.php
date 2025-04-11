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

// Satış ID'sini al
$sale_id = isset($_GET['sale_id']) ? intval($_GET['sale_id']) : 0;

// Satış bilgilerini getir
$sale_query = $vt->prepare("
    SELECT cs.*, c.name AS customer_name 
    FROM customer_sales cs
    JOIN customers c ON cs.customer_id = c.id
    WHERE cs.id = :sale_id
");
$sale_query->execute([':sale_id' => $sale_id]);
$sale = $sale_query->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    die("Satış bulunamadı.");
}

// Satış detaylarını getir
$sale_details_query = $vt->prepare("
    SELECT 
        product_name,
        quantity,
        price,
        is_kg,
        total_price
    FROM customer_sales_details
    WHERE customer_sale_id = :sale_id
");
$sale_details_query->execute([':sale_id' => $sale_id]);
$sale_details = $sale_details_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  
    <title>Satış Detayları</title>
  
</head>
<body class="container mt-4">
         <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>
    <h5>
   <i class="bi bi-receipt"></i> 
   Satış Detayları</h5>

<div class="p-3">
 <p>
  <i class="fas fa-user"></i> 
   Müşteri Adı:
  <?= customer($sale['customer_name']) ?> (ID: <?= htmlspecialchars($sale['customer_id']) ?>)
 </p>
 <p>
<i class="bi bi-hash"></i> Satış ID:</strong> <?= htmlspecialchars($sale['id']) ?></p>

        <p><i class="bi bi-cash-stack"></i> Tutar:</strong> <?= number_format($sale['total_amount'], 2) ?> TL</p>
        
        <p><i class="fas fa-calendar-alt"> </i> Satış Tarihi: <?= htmlspecialchars($sale['sale_date']) ?></p>
    </div>

<h5>
 <i class="bi bi-info-circle"></i> 
 Ürün Detayları
</h5>

<table class="table table-striped table-bordered p-3">
    <thead class="table-warning text-center">
        <tr>
            <th><i class="bi bi-tag"></i> Ürün Adı</th>
            <th><i class="bi bi-123"></i> Miktar</th>
            <th><i class="bi bi-cash-stack"></i> Fiyat (TL)</th>
            <th><i class="bi bi-calculator"></i> Toplam (TL)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sale_details as $detail): ?>
            <tr class="text-center">
                <td><?= htmlspecialchars($detail['product_name']) ?></td>
                <td><?= htmlspecialchars($detail['quantity']) ?></td>
                <td><?= number_format($detail['price'], 2) ?></td>
                <td><?= number_format($detail['total_price'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    <!-- Bootstrap JS -->
  
        <?php endif; ?>
</body>
</html>