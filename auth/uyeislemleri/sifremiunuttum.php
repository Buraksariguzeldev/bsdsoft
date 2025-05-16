php
<?php
session_start();

// Include necessary files
include '../../assets/src/config/vt_baglanti.php'; // Database connection
include '../../assets/src/include/navigasyon.php'; // Navigation

$message = '';
$error = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = $_POST['username_or_email'] ?? '';
    $new_password = $_POST['new_password'] ?? ''; // Added for direct change
    $confirm_password = $_POST['confirm_password'] ?? ''; // Added for direct change

    // Basic validation (can be expanded)
    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $error = 'Lütfen kullanıcı adı/e-posta ve yeni şifre alanlarını doldurun.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Yeni şifreler uyuşmuyor.';
    } else {
        // **TEMPORARY DIRECT PASSWORD CHANGE LOGIC (USING USERNAME FOR NOW)**
        // In a real application, this would typically involve finding the user by email and sending a reset link.
        // This is a simplified example for direct change for now.

        try {
            // Find the user by username (since email column doesn't exist in users table based on schema.sql)
            $stmt = $vt->prepare("SELECT id FROM users WHERE username = :email"); // Keep :email placeholder for now, but it's username
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT); // Use BCRYPT for stronger hashing

                // Update the user's password
                $stmt = $vt->prepare("UPDATE users SET password = :password WHERE id = :id");
                $stmt->execute([':password' => $hashed_password, ':id' => $user['id']]);

                $message = 'Şifreniz başarıyla güncellendi. Şimdi giriş yapabilirsiniz.';
                 // Optionally redirect to login page
                 // header('Location: girisyap.php');
                 // exit;

            } else {
                $error = 'Bu kullanıcı adı/e-posta ile eşleşen bir kullanıcı bulunamadı.';
            }
        } catch (PDOException $e) {
            $error = 'Bir hata oluştu: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifremi Unuttum</title>
    <!-- Include your CSS files here -->
    <?php include '../../assets/src/include/styles.php'; ?>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-question-circle"></i> Şifremi Unuttum</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label for="username_or_email" class="form-label"><i class="bi bi-person"></i> Kullanıcı Adınız veya E-posta Adresiniz:</label>
                                <input type="text" class="form-control" id="username_or_email" name="username_or_email" required>
                            </div>

                            <!-- TEMPORARY FIELDS FOR DIRECT PASSWORD CHANGE -->
                             <div class="mb-3">
                                <label for="new_password" class="form-label"><i class="bi bi-lock"></i> Yeni Şifre:</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                             <div class="mb-3">
                                <label for="confirm_password" class="form-label"><i class="bi bi-lock-fill"></i> Yeni Şifre Tekrar:</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <!-- END TEMPORARY FIELDS -->

                            <button type="submit" class="btn btn-primary">Şifremi Güncelle</button>
                        </form>
                         <div class="text-center mt-3">
                            <a href="girisyap.php" class="btn btn-link p-0">Giriş Sayfasına Dön</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../assets/src/include/footer.php'; ?>
    <?php include '../../assets/src/include/script.php'; ?>
</body>
</html>