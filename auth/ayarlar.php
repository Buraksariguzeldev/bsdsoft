php
<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/header.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/giriskontrol.php');
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  
} else {
    header("location: giris.php");
  }

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir


$user_id = $_SESSION["id"];
// Kullanıcı bilgilerini getir
$sql = "SELECT kullanici_adi, kullanici_eposta FROM kullanicilar WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($kullanici_adi, $kullanici_eposta);
    $stmt->fetch();
    $stmt->close();
}

// Şifre değiştirme işlemleri
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sifre_degistir"])) {
    $yeni_sifre = $_POST["yeni_sifre"];
    $yeni_sifre_tekrar = $_POST["yeni_sifre_tekrar"];  

    if (strlen($yeni_sifre) < 6) {
        $hata_mesaji = "Şifre en az 6 karakter olmalıdır.";
    } elseif ($yeni_sifre != $yeni_sifre_tekrar) {
        $hata_mesaji = "Şifreler eşleşmiyor.";
    } else {
        $hashed_password = password_hash($yeni_sifre, PASSWORD_DEFAULT);
        $sql = "UPDATE kullanicilar SET sifre = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $hashed_password, $user_id);
            if ($stmt->execute()) {
                $basari_mesaji = "Şifre başarıyla değiştirildi.";
            } else {
                $hata_mesaji = "Şifre değiştirilirken bir hata oluştu.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hesap Ayarları</title>

  <!-- Harici CSS Dosyalarý -->
  <link rel="stylesheet" href="../assets/src/css/bsd_form.css">
  <link rel="stylesheet" href="../assets/src/css/bsd_bağlantı.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>

  <div class="container">
    <h2 class="text-center">Hesap Ayarları</h2>
    <p class="text-center">Kullanıcı Adı: <?php echo htmlspecialchars($kullanici_adi); ?></p>
    <p class="text-center">E-posta: <?php echo htmlspecialchars($kullanici_eposta); ?></p>

    <h3>Şifre Değiştir</h3>
    <?php if (isset($hata_mesaji)): ?>
        <p style="color: red;"><?php echo $hata_mesaji; ?></p>
    <?php endif; ?>
    <?php if (isset($basari_mesaji)): ?>
        <p style="color: green;"><?php echo $basari_mesaji; ?></p>
    <?php endif; ?>
    <div class="row justify-content-center">
      <form method="post" class="col-md-6 col-12 mt-5">
        <div class="form-group mt-2">
          <label for="yeni_sifre" class="form-label">Yeni Şifre:</label>
          <input type="password" name="yeni_sifre" class="form-control" required>
        </div>
        <div class="form-group mt-2">
          <label for="yeni_sifre_tekrar" class="form-label">Yeni Şifre (Tekrar):</label>
          <input type="password" name="yeni_sifre_tekrar" class="form-control" required>
        </div>
        <input type="submit" name="sifre_degistir" value="Şifreyi Değiştir" class="btn btn-primary mt-3">
      </form>
    </div>

  </div>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/footer.php'); ?>
</body>
</html>