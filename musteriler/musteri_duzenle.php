<?php
  include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
// Veritabanı bağlantısı
try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanına bağlanılamadı: " . $e->getMessage());
}

// Müşteri bilgilerini çekme
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $vt->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$customer) {
            die("Müşteri bulunamadı.");
        }
    } catch (PDOException $e) {
        die("Müşteri bilgileri alınırken hata oluştu: " . $e->getMessage());
    }
} else {
    die("Geçersiz ID.");
}

// Müşteri bilgilerini güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';

    try {
        $stmt = $vt->prepare("UPDATE customers SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $email, $address, $id]);

        // Yönlendirme mesajı
        echo "<div class='alert alert-success mt-3'>
                <i class='fas fa-check-circle'></i> Müşteri başarıyla güncellendi. Yönlendiriliyorsunuz...
              </div>";

        // JavaScript ile yönlendirme
        echo "<script type='text/javascript'>
                setTimeout(function() {
                    window.location.href = 'musteriler.php';
                }, 3000); // 3 saniye sonra yönlendir
              </script>";
        
        exit;
    } catch (PDOException $e) {
        die("Müşteri güncellenirken hata oluştu: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>Müşteri Düzenle</title>
  
</head>
<body>
  
         <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>

    <h5> 
    <i class="bi bi-person-gear"></i>
       Müşteri Düzenle
   </h5>



    <form method="POST">
       
       
            <?php
      include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/functions/musteriadi.php";
      
      echo "<hr>";
      

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/telefonno.php";
      
            echo "<hr>";
      
            include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/eposta.php";
      
            echo "<hr>";
      
            include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/eposta.php";
      
                  echo "<hr>";
      
            include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/adres.php";
      
      
      
      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/code/button.php";
      ?>
    </form>



    <?php endif; ?>
</body>
</html>