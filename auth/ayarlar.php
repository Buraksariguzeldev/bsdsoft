<?php
// Oturumu başlat
session_start();

// Gerekli dosyaları dahil et
// Önce veritabanı bağlantısı (PDO varsayılıyor)
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php'); // Bu dosyanın $vt = new PDO(...) şeklinde bir bağlantı kurduğunu varsayıyoruz.
// Sonra giriş kontrolü
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/giriskontrol.php');

// Kullanıcı ID'sini al
if (!isset($_SESSION["id"])) {
    header("location: /auth/uyeislemleri/girisyap.php");
    exit;
}
$user_id = $_SESSION["id"];

// Hata ve başarı mesajları için değişkenler
$hata_mesaji = '';
$basari_mesaji = '';

// Kullanıcı bilgileri
$kullanici_adi = '';
$kullanici_eposta = '';
$mevcut_sifre_hash = '';

// Veritabanı işlemlerini try-catch bloğuna al
try {
    // Veritabanı bağlantısı kontrolü (PDO nesnesi $vt olarak varsayılıyor)
    if ($vt) {
        // Kullanıcı bilgilerini ve mevcut şifre hash'ini getir
        $sql_user = "SELECT kullanici_adi, kullanici_eposta, sifre FROM kullanicilar WHERE id = :id";
        $stmt_user = $vt->prepare($sql_user);
        $stmt_user->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($stmt_user->execute()) {
            // fetch() metodu satır varsa array, yoksa false döndürür
            $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
            if ($user_data) {
                $kullanici_adi = $user_data['kullanici_adi'];
                $kullanici_eposta = $user_data['kullanici_eposta'];
                $mevcut_sifre_hash = $user_data['sifre'];
            } else {
                $hata_mesaji = "Hesap bilgileri bulunamadı.";
                 // Belki burada oturumu sonlandırmak daha iyi
            }
        } else {
            $hata_mesaji = "Kullanıcı bilgileri alınırken bir sorgu hatası oluştu.";
            // PDO'nun hata detaylarını logla
             error_log("Ayarlar - Kullanıcı bilgisi alma hatası: " . implode(" | ", $stmt_user->errorInfo()));
        }
        // PDO statement'ını kapatmaya gerek yok, genellikle kapsam dışı kalınca halledilir
        $stmt_user = null;

        // Şifre değiştirme isteği geldiyse
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sifre_degistir"])) {
            $mevcut_sifre = $_POST["mevcut_sifre"];
            $yeni_sifre = $_POST["yeni_sifre"];
            $yeni_sifre_tekrar = $_POST["yeni_sifre_tekrar"];

            // Ön kontroller
            if (empty($mevcut_sifre_hash) && empty($hata_mesaji)) {
                 $hata_mesaji = "Mevcut şifre bilgisi alınamadı. Lütfen tekrar deneyin.";
            } elseif (!empty($hata_mesaji)) {
                 // Zaten bir hata mesajı varsa, şifre işlemi yapma
            } elseif (!password_verify($mevcut_sifre, $mevcut_sifre_hash)) {
                $hata_mesaji = "Mevcut şifreniz yanlış.";
            } elseif (strlen($yeni_sifre) < 6) {
                $hata_mesaji = "Yeni şifre en az 6 karakter olmalıdır.";
            } elseif ($yeni_sifre != $yeni_sifre_tekrar) {
                $hata_mesaji = "Yeni şifreler eşleşmiyor.";
            } elseif ($mevcut_sifre == $yeni_sifre) {
                 $hata_mesaji = "Yeni şifre mevcut şifrenizle aynı olamaz.";
            } else {
                // Yeni şifreyi hashle
                $hashed_password = password_hash($yeni_sifre, PASSWORD_DEFAULT);

                // Veritabanında güncelle
                $sql_update = "UPDATE kullanicilar SET sifre = :yeni_sifre WHERE id = :id";
                $stmt_update = $vt->prepare($sql_update);
                $stmt_update->bindParam(':yeni_sifre', $hashed_password, PDO::PARAM_STR);
                $stmt_update->bindParam(':id', $user_id, PDO::PARAM_INT);

                if ($stmt_update->execute()) {
                    // rowCount() ile etkilenen satır sayısını kontrol et
                    if ($stmt_update->rowCount() > 0) {
                         $basari_mesaji = "Şifre başarıyla değiştirildi.";
                         // Başarı durumunda mevcut hash'i de güncelle
                         $mevcut_sifre_hash = $hashed_password;
                    } else {
                         // Güncelleme başarılı oldu ama hiçbir satır etkilenmedi (nadir durum) veya şifre zaten buydu.
                         $hata_mesaji = "Şifre güncellenemedi veya bir değişiklik yapılmadı.";
                    }
                } else {
                    $hata_mesaji = "Şifre güncellenirken bir veritabanı hatası oluştu.";
                     error_log("Ayarlar - Şifre güncelleme execute hatası: " . implode(" | ", $stmt_update->errorInfo()));
                }
                $stmt_update = null; // Statement'ı kapat
            }
        }
    } else {
         // Veritabanı bağlantısı nesnesi ($vt) mevcut değilse
         $hata_mesaji = "Veritabanı bağlantısı kurulamadı.";
         error_log("Ayarlar - Veritabanı bağlantı nesnesi bulunamadı.");
    }

} catch (PDOException $e) {
    // PDOException yakala
    $hata_mesaji = "Veritabanı hatası: İşlem gerçekleştirilemedi."; // Kullanıcıya genel bir mesaj göster
    error_log("Ayarlar - PDOException: " . $e->getMessage()); // Detayları logla
}

// Veritabanı bağlantısını kapat (PDO için null yapmak yeterli)
$vt = null;


// Header ve Navigasyon (HTML öncesi PHP işlemleri bittikten sonra)
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/header.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesap Ayarları</title>
    <!-- Stil dosyaları (Projenizin yapısına göre yolları kontrol edin) -->
    <link rel="stylesheet" href="/assets/src/css/main.css">
    <link rel="stylesheet" href="/assets/src/css/bsd_form.css">
    <link rel="stylesheet" href="/assets/src/css/bsd_button.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Ekstra stil iyileştirmeleri */
        .card { border: none; }
        .card-header { background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;}
        .form-label i { margin-right: 8px; color: #6c757d;}
        .btn-lg { padding: 0.75rem 1.25rem; font-size: 1.1rem;}
        .alert { margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="container mt-4 mb-5">
        <div class="card shadow-sm">
            <div class="card-header text-center">
                <h2 class="h4 mb-0 fw-normal">Hesap Ayarları</h2>
            </div>
            <div class="card-body p-4 p-md-5">
                <?php if (empty($hata_mesaji) && empty($kullanici_adi) && empty($kullanici_eposta)): ?>
                     <div class="alert alert-warning text-center">Kullanıcı bilgileri yüklenemedi.</div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <p class="lead"><strong>Kullanıcı Adı:</strong> <?php echo htmlspecialchars($kullanici_adi); ?></p>
                        <p class="text-muted"><strong>E-posta:</strong> <?php echo htmlspecialchars($kullanici_eposta); ?></p>
                    </div>
                <?php endif; ?>

                <hr class="my-4">

                <h3 class="text-center mb-4 fw-light">Şifre Değiştir</h3>

                <?php if (!empty($hata_mesaji)): ?>
                    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($hata_mesaji); ?></div>
                <?php endif; ?>
                <?php if (!empty($basari_mesaji)): ?>
                    <div class="alert alert-success text-center"><?php echo htmlspecialchars($basari_mesaji); ?></div>
                <?php endif; ?>

                <?php if (!isset($e) && $vt !== false): // Veritabanı bağlantısı başarılıysa ve Exception oluşmadıysa formu göster ( $vt null olabilir ama başta false değildi)?>
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-7 col-xl-6">
                         <!-- Form action'ı boş bırakmak veya kendine göndermek güvenlidir -->
                        <form method="post" action="" class="mt-3">
                            <div class="form-group mb-3">
                                <label for="mevcut_sifre" class="form-label"><i class="fas fa-lock"></i>Mevcut Şifre:</label>
                                <input type="password" id="mevcut_sifre" name="mevcut_sifre" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="yeni_sifre" class="form-label"><i class="fas fa-key"></i>Yeni Şifre:</label>
                                <input type="password" id="yeni_sifre" name="yeni_sifre" class="form-control" required minlength="6">
                                <small class="form-text text-muted">En az 6 karakter olmalıdır.</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="yeni_sifre_tekrar" class="form-label"><i class="fas fa-key"></i>Yeni Şifre (Tekrar):</label>
                                <input type="password" id="yeni_sifre_tekrar" name="yeni_sifre_tekrar" class="form-control" required minlength="6">
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" name="sifre_degistir" class="btn bsd-btn-primary btn-lg"><i class="fas fa-save me-2"></i>Şifreyi Değiştir</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                     <div class="alert alert-danger text-center">Şifre değiştirme formu yüklenemedi. Veritabanı veya yapılandırma hatası.</div>
                <?php endif; ?>
            </div> <!-- card-body sonu -->
        </div> <!-- card sonu -->
    </div> <!-- container sonu -->

<?php include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/footer.php'); ?>

</body>
</html>
