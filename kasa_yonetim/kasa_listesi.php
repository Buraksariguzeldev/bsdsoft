<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
// Veritabanı bağlantısı
try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo '<div class="alert alert-danger" role="alert">Veritabanı bağlantı hatası: ' . $e->getMessage() . '</div>';
    exit;
}

// Kasaları görüntüleme
try {
    $sql = "SELECT * FROM cash_registers";
    $stmt = $vt->query($sql);
    $cash_registers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Kasaları görüntülerken hata oluştu: " . $e->getMessage();
    echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error_message) . '</div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  
    <title>Kasa listesi</title>
 
</head>
<body>

  
    <h5>
       <i class="bi bi-list-ul"></i>
       Kasa Listesi
      </h5>

    <?php if (!empty($cash_registers)): ?>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th><i class="fas fa-tag"></i> Kasa Adı</th>
                    <th><i class="fas fa-calendar-alt"></i> Oluşturulma Tarihi</th>
                    <th><i class="fas fa-tools"></i> İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cash_registers as $register): ?>
<tr data-id="<?= $register['id']; ?>">
    <td><?= htmlspecialchars($register['register_name']); ?></td>
    <td><?= htmlspecialchars($register['created_at']); ?></td>
    <td>
        <a href="kasa_duzenle.php?id=<?= $register['id']; ?>" class="btn btn-sm">
            <i class="fas fa-edit"></i> Düzenle
        </a>
        <a href="#" class="btn btn-sm" onclick="kasaSil(<?= $register['id']; ?>, event)">
            <i class="fas fa-trash-alt"></i> Sil
        </a>
    </td>
</tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-circle"></i> Hiç kasa eklenmemiş.
        </div>
    <?php endif; ?>

<a href="kasa_ekle.php">kasa ekle</a>
</div>

    
    
    <script>
function kasaSil(id, event) {
    event.preventDefault();
    if (confirm('Bu kasayı silmek istediğinizden emin misiniz?')) {
        fetch('kasa_sil.php?id=' + id)
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    // Başarı mesajını göster
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        <i class="fas fa-check-circle"></i> Kasa başarıyla silindi!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.querySelector('h5').after(alertDiv);

                    // Silinen satırı tablodan kaldır
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        row.remove();
                    }

                    // Eğer tablo boşsa uyarı mesajı göster
                    const tbody = document.querySelector('tbody');
                    if (tbody.children.length === 0) {
                        const table = document.querySelector('table');
                        const warningDiv = document.createElement('div');
                        warningDiv.className = 'alert alert-warning';
                        warningDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Hiç kasa eklenmemiş.';
                        table.replaceWith(warningDiv);
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
                        <i class="fas fa-exclamation-triangle"></i> ${data}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.querySelector('h5').after(alertDiv);
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                alert('Kasa silinirken bir hata oluştu.');
            });
    }
}
</script>

</body>
</html>