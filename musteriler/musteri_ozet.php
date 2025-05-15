<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
// Veritabanı bağlantısı
try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    // db pdo bu include yolunda bulunuyor 
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Kullanıcı ID'sini al (örneğin, oturumdan veya URL'den)
$user_id = isset($_GET['id']) ? $_GET['id'] : die("Müşteri ID bulunamadı");

// Önce müşteri bilgilerini alalım
$customerQuery = $vt->prepare("SELECT name FROM customers WHERE id = :user_id");
$customerQuery->execute([':user_id' => $user_id]);
$customerName = $customerQuery->fetchColumn();

// Sonra satış bilgilerini alalım
$query = $vt->prepare("
    SELECT 
        cs.id as sale_id,
        cs.sale_date,
        cs.total_amount,
        GROUP_CONCAT(p.product_name) as products,  -- Ürün adlarını alıyoruz
        SUM(csd.quantity * csd.price) as total_sale_amount
    FROM customer_sales cs
    JOIN customer_sales_details csd ON cs.id = csd.customer_sale_id
    JOIN products p ON p.id = csd.product_id
    WHERE cs.customer_id = :user_id
    GROUP BY cs.id
    ORDER BY cs.sale_date DESC
");

$query->execute([':user_id' => $user_id]);
$transactions = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  
    <title>Ürünler</title>
  
</head>
<body class="container mt-4">
         <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>
  
    <h5>
     <i class="fas fa-user"></i> <?= customer($customerName) ?> - Alınan Ürünler
    </h5>
    
    

<table class="table table-striped table-bordered">
    <thead class="table-warning text-center">
        <tr>
            <th><i class="bi bi-hash"></i> Satış No</th>
            <th><i class="bi bi-calendar-event"></i> Tarih</th>
            <th><i class="bi bi-box-seam"></i> Alınan Ürünler</th>
            <th><i class="bi bi-cash-stack"></i> Toplam (TL)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $transaction): ?>
            <tr class="text-center">
                <td><?= htmlspecialchars($transaction['sale_id']) ?></td>
                <td><?= htmlspecialchars($transaction['sale_date']) ?></td>
                <td><?= htmlspecialchars($transaction['products']) ?></td>
                <td><?= number_format($transaction['total_sale_amount'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    <button onclick="window.print()" class="btn btn-primary mt-3">
        <i class="fas fa-print"></i> Yazdır
    </button>

  
        <?php endif; ?>
</body>
</html>