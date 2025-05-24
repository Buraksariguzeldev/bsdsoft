<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
date_default_timezone_set('Europe/Istanbul');
setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'turkish');

try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Filtreleme için parametreleri al
$filter_type = $_GET['filter_type'] ?? 'all';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// SQL sorgusu oluştur
$sql = "SELECT * FROM sales_tracking WHERE 1=1";
$params = [];

if ($filter_type !== 'all') {
    $sql .= " AND sale_type = ?";
    $params[] = $filter_type;
}

if ($start_date && $end_date) {
    $sql .= " AND sale_date BETWEEN ? AND ?";
    $params[] = $start_date . ' 00:00';
    $params[] = $end_date . ' 23:59';
}

$sql .= " ORDER BY id DESC";  // 'tracking_id' yerine 'id' kullanarak sıralama yapıyoruz

$stmt = $vt->prepare($sql);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  
    <title>Satış Takip Listesi</title>
  
</head>
<body>
         <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>

        <h5>
           <i class="bi bi-list-check"></i>
           Satış Takip Listesi
        </h5>
        
        <!-- Filtre Formu -->
        <form class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <select name="filter_type" class="form-select">
                        <option value="all" <?= $filter_type == 'all' ? 'selected' : '' ?>>Tüm Satışlar</option>
                        <option value="customer" <?= $filter_type == 'customer' ? 'selected' : '' ?>>Müşteri Satışları</option>
                        <option value="normal" <?= $filter_type == 'normal' ? 'selected' : '' ?>>Normal Satışlar</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa fa-filter"></i> Filtrele</button>
                </div>
            </div>
        </form>

        <!-- Satış Listesi Tablosu -->
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Takip No</th>
                    <th>Tarih</th>
                    <th>Satış Türü</th>
                    <th>Müşteri Adı</th>
                    <th>Satış ID</th>
                    <th>Toplam Tutar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                    <tr class="<?= $sale['sale_type'] == 'customer' ? 'customer-sale' : 'normal-sale' ?>">
                        <td><?= htmlspecialchars($sale['id']) ?></td>  <!-- 'tracking_id' yerine 'id' -->
                        <td><?= date('d.m.Y H:i', strtotime($sale['sale_date'])) ?></td>
                        <td><?= $sale['sale_type'] == 'customer' ? 'Müşteri Satışı' : 'Normal Satış' ?></td>
                   <td><?= htmlspecialchars($sale['customer_name'] ?? '-') ?></td>
<td><?= htmlspecialchars($sale['sale_id'] ?? '-') ?></td>
<td><?= number_format($sale['total_amount'], 2, ',', '.') ?> ₺</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


        <?php endif; ?>
</body>
</html>