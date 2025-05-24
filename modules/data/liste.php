<?php
session_start();
$rol_kontrol_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/rol_kontrol.php';

if (file_exists($rol_kontrol_path)) {
    include($rol_kontrol_path);
    if (function_exists('rol_kontrol')) {
        rol_kontrol(1);
    }
}

// Sayfa içeriği buradan devam eder...
?>
<?php


include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

?>


<!DOCTYPE html>
<html lang="tr">
<head>

    <title>Dizin Bilgisi</title>

</head>
<body>

    <h5><i class="fas fa-folder-open"></i> Hizli liste </h5>
   
    <!-- Arama ve Görüntüleme Formu -->
    <form id="searchForm" action="liste.php" method="get">
        <label for="dosya_adi"><i class="fas fa-search"></i> Dosya veya Klasör Adı:</label>
        <input type="text" id="dosya_adi" name="adi" value="<?php echo isset($_GET['adi']) ? htmlspecialchars($_GET['adi']) : ''; ?>" placeholder="Örneğin: data/veys/font">
        <button type="submit"><i class="fas fa-search"></i> Ara ve Görüntüle</button>
    </form>
    <hr>
    <!-- PHP Kod Bloğu Burada -->
    <?php
    // Hata ayıklama işlemlerini etkinleştir
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Türkiye'nin saat dilimini ayarla
    date_default_timezone_set('Europe/Istanbul');

    // Kök dizini belirle
    $baseDirectory = $_SERVER['DOCUMENT_ROOT'];

    // Kullanıcının isteği doğrultusunda dizin veya dosya adını al
    $adi = isset($_GET['adi']) ? $_GET['adi'] : '';
    $yol = $baseDirectory . '/' . $adi;

    // Klasör veya dosya var mı diye kontrol et
    if (file_exists($yol)) {
        if (is_dir($yol)) {
            // Klasör içeriğini listele
            $dosyalar = scandir($yol);
            if ($dosyalar === false) {
                echo "<p><i class='fas fa-exclamation-triangle'></i> Klasör içeriği listeelenemedi: " . htmlspecialchars($adi) . "</p>";
            } else {
                $dosyaSayisi = 0;
                $klasorSayisi = 0;
                $toplamBoyut = 0;
                echo "<ul>";
                foreach ($dosyalar as $dosya) {
                    if ($dosya != "." && $dosya != "..") {
                        $dosyaYolu = $yol . '/' . $dosya;
                        $tip = is_dir($dosyaYolu) ? 'klasör' : 'dosya';
                        $icon = is_dir($dosyaYolu) ? '<i class="fas fa-folder"></i>' : '<i class="fas fa-file"></i>';
                        $olusturma = date("d/m/Y H:i", fileatime($dosyaYolu));
                        $degisiklik = date("d/m/Y H:i", filemtime($dosyaYolu));
                        $boyut = is_dir($dosyaYolu) ? '-' : formatBytes(filesize($dosyaYolu));
                        echo "<li>$icon <a class='bsd-navlink1 folder-link' href='liste.php?adi=" . rawurlencode($adi . '/' . $dosya) . "'>$dosya ($tip)</a><br>
                            <i class='far fa-calendar-alt'></i> Oluşturma Tarihi: $olusturma <br>
                            <i class='far fa-clock'></i> Son Değişiklik: $degisiklik <br>
                            <i class='fas fa-weight'></i> Boyut: $boyut";
                        if (is_dir($dosyaYolu)) {
                            $altDosyalar = scandir($dosyaYolu);
                            $altDosyaSayisi = 0;
                            $altKlasorSayisi = 0;
                            $altToplamBoyut = 0;
                            foreach ($altDosyalar as $altDosya) {
                                if ($altDosya != "." && $altDosya != "..") {
                                    $altDosyaYolu = $dosyaYolu . '/' . $altDosya;
                                    if (is_dir($altDosyaYolu)) {
                                        $altKlasorSayisi++;
                                    } else {
                                        $altDosyaSayisi++;
                                        $altToplamBoyut += filesize($altDosyaYolu);
                                    }
                                }
                            }
                            echo "<br>
                            <i class='fas fa-info-circle'></i> Alt Dosya Sayısı: $altDosyaSayisi, Alt Klasör Sayısı: $altKlasorSayisi, Toplam Boyut: " . formatBytes($altToplamBoyut);
                            $dosyaSayisi += $altDosyaSayisi;
                            $klasorSayisi += $altKlasorSayisi;
                            $toplamBoyut += $altToplamBoyut;
                        } else {
                            $dosyaSayisi++;
                            $toplamBoyut += filesize($dosyaYolu);
                        }
                        echo "</li>";
                    }
                }
                echo "</ul>";
                echo "<p><i class='fas fa-info-circle'></i> İçinde Bulunan Dosya Sayısı: $dosyaSayisi, Klasör Sayısı: $klasorSayisi, Toplam Boyut: " . formatBytes($toplamBoyut) . "</p>";
            }
        } else {
            // Dosya içeriğini göster
            $dosyaIcerik = @file_get_contents($yol);
            if ($dosyaIcerik === false) {
                echo "<p><i class='fas fa-exclamation-triangle'></i> Dosya içeriği okunamadı: " . htmlspecialchars($adi) . "</p>";
            } else {
                $olusturma = date("d/m/Y H:i", fileatime($yol));
                $degisiklik = date("d/m/Y H:i", filemtime($yol));
                $boyut = formatBytes(filesize($yol));
                echo "<h5><i class='fas fa-file-alt'></i> Dosya içeriği</h5>";
                echo "<p><i class='far fa-calendar-alt'></i> Oluşturma Tarihi: $olusturma</p>";
                echo "<p><i class='far fa-clock'></i> Son Değişiklik: $degisiklik</p>";
                echo "<p><i class='fas fa-weight'></i> Boyut: $boyut</p>";
                echo "<p><i class='fas fa-file-code'></i> Dosya yolu: <br>" . htmlspecialchars($adi) . "</p>";
                echo "<p><i class='fas fa-file-code'></i> Dosya içeriği: <br></p><pre class='code'>" . htmlspecialchars($dosyaIcerik) . "</pre>";
            }
        }
    } else {
        http_response_code(404);
        echo "<p><i class='fas fa-exclamation-triangle'></i> Dosya veya klasör bulunamadı veya erişim izni yok: " . htmlspecialchars($adi) . "</p>";
    }

    // Dosya boyutunu insan okunabilir bir formata dönüştüren fonksiyon
    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    ?>
    
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
   
</body>
</html>
