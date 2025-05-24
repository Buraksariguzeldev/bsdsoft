<?php
  include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
// Veritabanına bağlantı
try {
  
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
// db pdo bu include yolunda bulunuyor 
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanına bağlanılamadı: " . $e->getMessage());
}

// Müşteri ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $email = $_POST['email'] ?? null;
    $address = $_POST['address'] ?? null;

    if ($name) {
        try {
            $stmt = $vt->prepare("
                INSERT INTO customers (name, phone, email, address) 
                VALUES (:name, :phone, :email, :address)
            ");
            $stmt->execute([
                ':name' => $name,
                ':phone' => $phone,
                ':email' => $email,
                ':address' => $address,
            ]);
            $message = "Müşteri başarıyla eklendi.";
        } catch (PDOException $e) {
            $message = "Müşteri eklenirken bir hata oluştu: " . $e->getMessage();
        }
    } else {
        $message = "Müşteri adı boş olamaz.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  
    <title>Müşteri Ekle</title>
 
</head>
<body>
  
  
         <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>

    <h5>
       <i class="bi bi-person-plus"></i> Müşteri Ekle
   </h5>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="p-3">
      
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
      "/assets/src/functions/adres.php";
      
      
      
      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/code/button.php";
      ?> 

    </form>
    
        <?php endif; ?>
</d>
</body>
</html>