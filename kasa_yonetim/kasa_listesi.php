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
<body class="bg-light">
         <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>
  
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
                    <tr>
                        <td><?= htmlspecialchars($register['register_name']); ?></td>
                        <td><?= htmlspecialchars($register['created_at']); ?></td>
                        <td>
                            <a href="kasa_duzenle.php?id=<?= $register['id']; ?>" class="btn btn-sm ">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <a href="kasa_sil.php?id=<?= $register['id']; ?>" class="btn btn-sm" 
                               onclick="return confirm('Kasa silinecek. Emin misiniz?');">
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


</div>
<a href="kasa_ekle.php">kasa</a>
    <?php endif; ?>
</body>
</html>