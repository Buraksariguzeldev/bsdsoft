<?php
// Veritabanına bağlan
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    // db pdo bu include yolunda bulunuyor 
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

// Markaları al
try {
    $stmt = $vt->query("SELECT * FROM brands ORDER BY id ASC");
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Markaları getirirken bir hata oluştu: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
 
    <title>Markaları Görüntüle</title>
 
</head>
<body>
         <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>
   
        <h5>
           <i class="bi bi-card-list"></i> Marka Listesi
        </h5>

        <?php if (!empty($brands)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Marka Adı</th>

                        <th>Oluşturulma Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($brands as $brand): ?>
                        <tr>
<tr data-id="<?= $brand['id'] ?>">
                               <td><?= htmlspecialchars($brand['id']) ?></td>
                            <td><?= htmlspecialchars($brand['brand_name']) ?></td>

                            <td><?= htmlspecialchars($brand['created_at']) ?></td>
                            <td>
                                <a href="marka_duzenle.php?id=<?= $brand['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Düzenle
                                </a>
<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="markaSil(<?= $brand['id'] ?>)">
    <i class="fas fa-trash"></i> Sil
</a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Henüz eklenmiş bir marka bulunmamaktadır.</p>
        <?php endif; ?>
    </d>

   
        <?php endif; ?>
        
        <script>
function markaSil(id) {
    if (confirm('Bu markayı silmek istediğinizden emin misiniz?')) {
        fetch('marka_sil.php?id=' + id)
            .then(response => response.text())
            .then(data => {
                // Başarı mesajını göster
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
                    Marka başarıyla silindi
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                document.querySelector('h5').after(alertDiv);

                // Silinen satırı tablodan kaldır
                const silinecekSatir = document.querySelector(`tr[data-id="${id}"]`);
                if (silinecekSatir) {
                    silinecekSatir.remove();
                }

                // 3 saniye sonra alert'i kaldır
                setTimeout(() => {
                    alertDiv.remove();
                }, 3000);
            })
            .catch(error => {
                console.error('Hata:', error);
                alert('Marka silinirken bir hata oluştu.');
            });
    }
}
</script>

</body>
</html>