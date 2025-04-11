<?php
// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');

session_start();

// Eğer bir önceki sayfa bilgisi yoksa, yönlendirilecek varsayılan sayfayı ayarla
if (!isset($_SESSION['return_to'])) {
    $_SESSION['return_to'] = $_SERVER['HTTP_REFERER'] ?? '../../index.php'; // Önceki sayfayı al, yoksa varsayılan yönlendirme
}

try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php'); // PDO bağlantısını içe aktar
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanına bağlanılamadı: " . $e->getMessage());
}

$error = '';

// Eğer POST isteği varsa (form gönderimi yapılmışsa)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Kullanıcıyı doğrula
    $stmt = $vt->prepare("SELECT id, password FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Giriş başarılı
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;

        // Yönlendirme işlemi
        $redirect_url = $_SESSION['return_to']; // Yönlendirme URL'si
        unset($_SESSION['return_to']); // Session'dan kaldır
         
        header("Location: $redirect_url");
        exit; // Yönlendirme sonrası kod çalışmasın
    } else {
        $error = 'Geçersiz kullanıcı adı veya şifre!';
    }
}
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>Giriş Yap</title>
    
</head>
<body>



            <h5>
             <i class="bi bi-box-arrow-in-right"></i> 
             Giriş Yap
            </h5>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

<form method="post">
 <div class="p-3 bg-secondary">
  <label for="username" class="form-label"> <i class="bi bi-person"></i>Kullanıcı Adı:</label>
  <input type="text" id="username" name="username" class="form-control" required>
 </div>
<hr>
 <div class="p-3 bg-secondary">
  <label for="password" class="form-label"> <i class="bi bi-lock"></i>Şifre:</label>
  <input type="password" id="password" name="password" class="form-control" required>
 </div>
<?php

      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/code/button.php";
?>
</form>

            <div class="text-center mt-3">
                <a href="kayitol.php" class="btn btn-link p-0">Kayıt Ol</a>
            </div>
        </div>
    </div>
</div>
<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
</body>
</html>