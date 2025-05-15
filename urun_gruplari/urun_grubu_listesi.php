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
<tr data-id="<?= $group['id'] ?>">
    <td><?= htmlspecialchars($group['group_name']) ?></td>
    <td>
        <a href="urun_grubu_duzenle.php?id=<?= $group['id'] ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil-square"></i> Düzenle
        </a>
        <a href="#" class="btn btn-danger btn-sm" onclick="grupSil(<?= $group['id'] ?>, event)">
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

    
    <script>
function grupSil(id, event) {
    event.preventDefault();
    if (confirm('Bu ürün grubunu silmek istediğinizden emin misiniz?')) {
        fetch('urun_grubu_sil.php?id=' + id)
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    // Başarı mesajını göster
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        <i class="bi bi-check-circle"></i> Ürün grubu başarıyla silindi!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.querySelector('h5').after(alertDiv);

                    // Silinen satırı tablodan kaldır
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        row.remove();
                    }

                    // 3 saniye sonra alert'i kaldır
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 3000);
                } else {
                    // Hata mesajını göster
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        <i class="bi bi-exclamation-triangle"></i> ${data}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.querySelector('h5').after(alertDiv);
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                alert('Ürün grubu silinirken bir hata oluştu.');
            });
    }
}
</script>

</body>
</html>