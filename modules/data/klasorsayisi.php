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

<title>Dosya ve Klasör İstatistikleri</title>

</head>
<body>
  
<?php


// Dosya ve klasör sayısını hesaplayan fonksiyon
function dosyaVeKlasorSayisi($klasor) {
    $dosyaSayisi = 0;
    $klasorSayisi = 0;
    $bosKlasorSayisi = 0;
    $bosKlasorYollari = [];
    $bosDosyaSayisi = 0;
    $bosDosyaYollari = [];

    // RecursiveDirectoryIterator ve RecursiveIteratorIterator kullanarak klasörü tara
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($klasor, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $item) {
        if ($item->isFile()) {
            $dosyaSayisi++;
            // Boş dosya kontrolü (dosya boyutu sıfır)
            if ($item->getSize() === 0) {
                $bosDosyaSayisi++;
                $bosDosyaYollari[] = $item->getPathname();
            }
        } elseif ($item->isDir()) {
            $klasorSayisi++;
            // Boş klasör sayısını kontrol et
            $klasorIciDosyaSayisi = iterator_count(new FilesystemIterator($item->getPathname(), FilesystemIterator::SKIP_DOTS));
            if ($klasorIciDosyaSayisi === 0) {
                $bosKlasorSayisi++;
                $bosKlasorYollari[] = $item->getPathname();
            }
        }
    }
    
    echo "<h5><i class='fas fa-chart-bar icon'></i>Dosya ve Klasör İstatistikleri</h5>";
   
    echo "<table>";
    echo "<tr><th>Özellik</th><th>Değer</th></tr>";
    echo "<tr><td><i class='fas fa-file icon'></i>Toplam Dosya Sayısı</td><td>$dosyaSayisi</td></tr>";
    echo "<tr><td><i class='fas fa-folder icon'></i>Toplam Klasör Sayısı</td><td>$klasorSayisi</td></tr>";
    echo "<tr><td><i class='fas fa-folder-open icon'></i>Boş Klasör Sayısı</td><td>$bosKlasorSayisi</td></tr>";
    echo "<tr><td><i class='fas fa-file-alt icon'></i>Boş Dosya Sayısı</td><td>$bosDosyaSayisi</td></tr>";
    echo "</table>";

    if ($bosKlasorSayisi > 0 || $bosDosyaSayisi > 0) {
        echo '<hr>';
        echo "<h5><i class='fas fa-exclamation-triangle icon'></i>Boş Klasörler ve Dosyalar</h5>";
        echo "<table>";
        echo "<tr><th>Kategori</th><th>Yol</th></tr>";
        
        foreach ($bosKlasorYollari as $bosKlasorYolu) {
            echo "<tr><td><i class='fas fa-folder-open icon'></i>Boş Klasör</td><td>" . htmlspecialchars($bosKlasorYolu) . "</td></tr>";
        }
        
        foreach ($bosDosyaYollari as $bosDosyaYolu) {
            echo "<tr><td><i class='fas fa-file-alt icon'></i>Boş Dosya</td><td>" . htmlspecialchars($bosDosyaYolu) . "</td></tr>";
        }

        echo "</table>";
    }
    
    echo '<hr>'; 
    echo "<h5><i class='fas fa-info-circle icon'></i>Özet</h5>";
    echo "<p>Toplam Dosya ve Klasör Sayısı: " . ($dosyaSayisi + $klasorSayisi) . "</p>";
}

// Kök dizini kullan
$klasor = $_SERVER['DOCUMENT_ROOT'];
dosyaVeKlasorSayisi($klasor);
?>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>

</body>
</html>
