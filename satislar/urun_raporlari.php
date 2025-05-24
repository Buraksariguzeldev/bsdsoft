<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
// Veritabanı bağlantısı
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanına bağlanılamadı: " . $e->getMessage());
}

// Ürün satış verilerini getir (satılmamış ürünleri hariç tutarak)
$query = "
    SELECT 
        p.id,
        p.product_name,
        p.barcode,
        p.image_path,
        COALESCE(SUM(sp.quantity), 0) AS total_quantity,
        COALESCE(SUM(sp.quantity * sp.unit_price), 0) AS total_revenue
    FROM products p
    LEFT JOIN sales_products sp ON p.id = sp.product_id
    WHERE sp.quantity > 0
    GROUP BY p.id, p.product_name, p.barcode, p.image_path
    ORDER BY total_revenue DESC
";

$products = $vt->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    
    <title>Ürün Satış Raporu</title>
    <!-- Bootstrap CSS -->
  
</head>
<body>
         <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>
 
  

    <h5>
       <i class="bi bi-graph-up"></i>
       Ürün Satış Raporu
    </h5>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Resim <i class="fas fa-image"></i></th>
                <th>Ürün Adı <i class="fas fa-cogs"></i></th>
                <th>Barkod <i class="fas fa-barcode"></i></th>
                <th>Satılan Miktar <i class="fas fa-cubes"></i></th>
                <th>Toplam Gelir (TL) <i class="fas fa-money-bill-wave"></i></th>
                <th>Satış Durumu <i class="fas fa-check-circle"></i></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($product['product_name']) ?>" 
                                 class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                        <?php else: ?>
                            <span>Resim Yok</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                    <td><?= htmlspecialchars($product['barcode']) ?></td>
                    <td><?= htmlspecialchars($product['total_quantity']) ?></td>
                    <td><?= number_format($product['total_revenue'], 2) ?></td>
                    <td>
                        <?php if ($product['total_quantity'] > 0): ?>
                            <i class="fas fa-check-circle text-success"></i> Satıldı
                        <?php else: ?>
                            <i class="fas fa-times-circle text-danger"></i> Satılmadı
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</d>

<!-- Bootstrap JS -->

    <?php endif; ?>
</body>
</html>