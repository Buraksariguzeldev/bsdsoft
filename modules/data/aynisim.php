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

<title>Aynı İsimli Dosya ve Klasörler</title>

</head>
<body>
  
<?php 

$baseDirectory = $_SERVER['DOCUMENT_ROOT'];


// Dizindeki dosyaları tarama
function scanDirectory($dir) {
    global $fileList;
    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $filePath = $dir . DIRECTORY_SEPARATOR . $file;
        $relativePath = str_replace($GLOBALS['baseDirectory'] . DIRECTORY_SEPARATOR, '', $filePath);
        
        if (is_file($filePath) || is_dir($filePath)) {
            if (isset($fileList[$file])) {
                $fileList[$file][] = $relativePath;
            } else {
                $fileList[$file] = [$relativePath];
            }
        }
        
        if (is_dir($filePath)) {
            scanDirectory($filePath);
        }
    }
}

// Dizini tarama ve dosyaları listeleme
$fileList = [];
scanDirectory($baseDirectory);

// Aynı isimleri bulma
$duplicates = array_filter($fileList, function($paths) {
    return count($paths) > 1;
});

// Sonuçları ekrana yazdırma
echo '<h5><i class="fas fa-copy"></i> Aynı İsimlere Sahip <br> Dosya ve Klasörler</h5>';

echo '<ul>';
foreach ($duplicates as $name => $paths) {
    echo '<li><strong><i class="' . (is_dir($GLOBALS['baseDirectory'] . DIRECTORY_SEPARATOR . $paths[0]) ? 'fas fa-folder' : 'fas fa-file') . '"></i> ' . htmlspecialchars($name) . '</strong>';
    echo '<ul>';
    foreach ($paths as $path) {
        echo '<li><i class="fas fa-chevron-right"></i> ' . htmlspecialchars($path) . '</li>';
    }
    echo '</ul></li>';
}
echo '</ul>';
?>


<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>

</body>
</html>
