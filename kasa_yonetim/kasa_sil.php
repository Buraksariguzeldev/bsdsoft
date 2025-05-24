<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');


// Veritabanı bağlantısı
try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Veritabanı bağlantı hatası: " . $e->getMessage();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
   die("Geçersiz ürün ID'si!");
}
// Kasa silme işlemi
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM cash_registers WHERE id = :id";
        $stmt = $vt->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            sleep(5); // 5 saniye bekle

            // Yönlendirme mesajı
            $success_message = "Kasa başarıyla silindi. Yönlendiriliyorsunuz...";

            // JavaScript ile yönlendirme
            echo "<script type='text/javascript'>
                    setTimeout(function() {
                        window.location.href = 'kasa_listesi.php';
                    }, 1000); // 1 saniye sonra yönlendir
                </script>";
        } else {
            $error_message = "Kasa silinirken bir hata oluştu.";
        }
    } catch (PDOException $e) {
        $error_message = "Hata: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasa Sil</title>

</head>
<body>
       <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="fas fa-trash-alt"></i> Kasa Sil</h2>

        <!-- Başarı ve Hata Mesajları -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success mt-4" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>


    </div>

 
        <?php endif; ?>
</body>
</html>