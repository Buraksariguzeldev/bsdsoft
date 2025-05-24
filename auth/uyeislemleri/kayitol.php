<?php
session_start();
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');
try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    // db pdo bu include yolunda bulunuyor 
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanına bağlanılamadı: " . $e->getMessage());
}

if (!isset($_SESSION['return_to'])) {
    $_SESSION['return_to'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../../index.php';
}

$error = '';
$success = '';

// Kullanıcı sayısını kontrol et
$stmt = $vt->prepare("SELECT COUNT(*) FROM users");
$stmt->execute();
$user_count = $stmt->fetchColumn();

// Maksimum kullanıcı limitini belirleyin
$max_limit = 1;  

if ($user_count >= $max_limit) {
    $error = '';
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!empty($username) && !empty($password)) {
            try {
                // Şifreyi hashle ve veritabanına kaydet
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $vt->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                $stmt->execute([':username' => $username, ':password' => $hashedPassword]);

                // Kullanıcıyı otomatik olarak giriş yap
                $_SESSION['user_id'] = $vt->lastInsertId();
                $_SESSION['username'] = $username;

                // return_to URL'sine yönlendir
                $return_to = $_SESSION['return_to'] ?? '../../index.php';
                unset($_SESSION['return_to']); // return_to'yu temizle

                header("Location: " . $return_to);
                exit;
            } catch (PDOException $e) {
                $error = 'Bu kullanıcı adı zaten alınmış.';
            }
        } else {
            $error = 'Lütfen tüm alanları doldurun.';
        }
    }
}
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>kayit Ol</title>

</head>
<body>

        <h5>
          <i class="bi bi-person-add"></i>  Kayit Ol
        </h5>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="p-3 bg-secondary">
                <label for="username" class="form-label"><i class="fas fa-user"></i> Kullanıcı Adı:</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-control" 
                    required 
                    <?= $user_count >= $max_limit ? 'disabled' : '' ?>
                >
            </div>
<hr>
            <div class="p-3 bg-secondary ">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Şifre:</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    required 
                    <?= $user_count >= $max_limit ? 'disabled' : '' ?>
                >
            </div>
<hr>
            <div class="d-grid gap-2 bg-secondary">
                <button type="submit" class="btn btn-primary" <?= $user_count >= $max_limit ? 'disabled' : '' ?>>
                    <i class="fas fa-sign-in-alt"></i>  Kayit Ol
                </button>
            </div>
        </form>

        <?php if ($user_count >= $max_limit): ?>
            <div class="mt-4 text-center text-danger">
                <i class="fas fa-ban"></i> Yeni kayıt yapılamaz. Maksimum kullanıcı limitine ulaşıldı.
            </div>
        <?php endif; ?>
    </div>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
</body>
</html>