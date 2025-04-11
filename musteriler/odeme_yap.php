<?php include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>

<?php

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

// Gönderilen müşteri ID ve ödeme tutarını al
$customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// Müşteri toplam borcunu hesapla
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

// Eğer borç 0 veya daha azsa işlem engellenir
if ($totalDebt <= 0) {
    echo '<div class="alert alert-warning d-flex align-items-center" role="alert">';
    echo '<i class="fas fa-exclamation-circle me-2"></i>';
    echo '<div>Ödeme işlemi başarısız: Bu müşterinin borcu bulunmamaktadır.</div>';
    echo '</div>';
    echo '<script type="text/javascript">
            setTimeout(function() {
                window.location.href = "musteri_detay.php?customer_id='.$customer_id.'";
            }, 1000);
          </script>';
    exit;
}

// Ödeme işlemini kaydet
$paymentQuery = $vt->prepare("
    INSERT INTO payments (customer_id, amount, payment_date, description) 
    VALUES (:customer_id, :amount, NOW(), :description)
");
$paymentQuery->execute([
    ':customer_id' => $customer_id,
    ':amount' => $amount,
    ':description' => $description
]);

// Customer_sales tablosundaki ödenen miktarı güncelle
$updatePaidAmountQuery = $vt->prepare("
    UPDATE customer_sales 
    SET paid_amount = paid_amount + :amount 
    WHERE customer_id = :customer_id
");
$updatePaidAmountQuery->execute([
    ':amount' => $amount,
    ':customer_id' => $customer_id
]);

// Başarı mesajı ve yönlendirme
echo '<div class="alert alert-success d-flex align-items-center" role="alert">';
echo '<i class="fas fa-check-circle me-2"></i>';
echo '<div>Ödeme başarıyla kaydedildi.</div>';
echo '</div>';
echo '<script type="text/javascript">
        setTimeout(function() {
            window.location.href = "musteri_detay.php?customer_id='.$customer_id.'";
        }, 1000);
      </script>';
exit;


?>

    <?php endif; ?>