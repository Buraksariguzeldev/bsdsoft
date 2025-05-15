<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
if (!$kullanici_adi):
?>
<a href="../auth/login.php" class="btn btn-link text-decoration-none">
   İçerikleri görmek için giriş yapın
</a>
<?php else : ?>

<?php
date_default_timezone_set('Europe/Istanbul');

try {
   include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
   $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   // Belirli bir tarihteki toplam satışları ve karı hesapla
   $query = "
        SELECT
            DATE(s.sale_date) as sale_date,  -- Tarih bilgisini al
            SUM(s.total_amount) as total_sales,  -- Toplam satış tutarı
            SUM(sp.total_price) as total_product_sales,  -- Ürün satışlarından elde edilen toplam gelir
            COUNT(DISTINCT s.id) as total_transactions,  -- Farklı satış işlemlerinin sayısı
            SUM(sp.quantity) as total_items  -- Satılan toplam ürün adedi
        FROM sales s
        LEFT JOIN sales_products sp ON s.id = sp.sale_id  -- sales tablosundaki 'id' ile eşleşmeli
        GROUP BY DATE(s.sale_date)  -- Tarihe göre gruplama yap
        ORDER BY sale_date DESC  -- En yeni tarih en üstte olacak şekilde sırala
    ";

   $stmt = $vt->prepare($query);
   $stmt->execute();
   $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <h5>
       <i class="bi bi-clipboard-data" ></i>
       Günlük Satış ve Kar Raporu
    </h5>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Tarih</th>
                <th>Toplam Satış (TL)</th>
                <th>İşlem Sayısı</th>
                <th>Satılan Ürün Adedi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['sale_date']); ?></td>
                <td class="text-end"><?php echo number_format($row['total_sales'], 2, ',', '.'); ?> TL</td>
                <td><?php echo $row['total_transactions']; ?></td>
                <td><?php echo $row['total_items']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
} catch(PDOException $e) {
?>
<div class="alert alert-danger" role="alert">Veritabanı hatası: <?php echo $e->getMessage(); ?></div>
<?php
}
?>

<?php endif; ?>
