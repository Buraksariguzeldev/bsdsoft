<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
// giriş kontrol
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/giriskontrol.php');

try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

    // Ürün gruplarını al
    $stmt = $vt->query("SELECT * FROM product_groups");
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Veritabanına bağlanılamadı: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  
    <title>Ürün Gruplarını Görüntüle ve Düzenle</title>

</head>
<body>
        <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>
   
        <h5>
           <i class="bi bi-ui-checks-grid"></i> Ürün Grubu listesi
        </h5>
        
<table class="table table-bordered table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th><i class="bi bi-box-seam"></i> Grup Adı</th>
            <th><i class="bi bi-tools"></i> İşlemler</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($groups as $group): ?>
            <tr>
                <td><?= htmlspecialchars($group['group_name']) ?></td>
                <td>
                    <a href="urun_grubu_duzenle.php?id=<?= $group['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil-square"></i> Düzenle
                    </a>
                    <a href="urun_grubu_sil.php?id=<?= $group['id'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Bu ürün grubunu silmek istediğinizden emin misiniz?');">
                        <i class="bi bi-trash"></i> Sil
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

   
<?php 
include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; 

?>
    <?php endif; ?>
</body>
</html>