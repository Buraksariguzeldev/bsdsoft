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

if (isset($_POST['process'])) {
    $searchPattern = trim($_POST['search_pattern']); // Aranacak metin
    $replaceText = trim($_POST['replace_text']); // Değiştirilecek metin

    // Kontroller
    if (empty($searchPattern) || empty($replaceText)) {
        die("Aranacak ve değiştirilecek metinler boş olamaz.");
    }

    // Ana dizin
    $baseDir = '/storage/emulated/0/bsdsof_yedek'; // Ana dizin yolunu buraya yazın

    if (!is_dir($baseDir)) {
        die("Dizin bulunamadı: $baseDir");
    }

    // Recursive iterator ile dosya gezgini
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    $modifiedFiles = []; // Değişiklik yapılan dosyaları tutacak

    foreach ($files as $file) {
        if ($file->getExtension() === 'php') { // Sadece PHP dosyalarını işle
            $filePath = $file->getPathname();
            $fileContent = file_get_contents($filePath);

            // Metin arama ve değiştirme
            if (strpos($fileContent, $searchPattern) !== false) {
                $newContent = str_replace($searchPattern, $replaceText, $fileContent);
                file_put_contents($filePath, $newContent); // Yeni içeriği kaydet
                $modifiedFiles[] = $filePath; // Değiştirilen dosyayı listeye ekle
            }
        }
    }

    // Sonuçları göster
    if (count($modifiedFiles) > 0) {
        echo "<h3>Değiştirilen Dosyalar:</h3>";
        echo "<ul>";
        foreach ($modifiedFiles as $file) {
            echo "<li>$file</li>";
        }
        echo "</ul>";
    } else {
        echo "Belirtilen metin hiçbir dosyada bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>PHP Metin Değiştirici</title>
</head>
<body>
    <h2>PHP Dosyalarında Metin Değiştir</h2>
    <form method="POST">
        <label for="search_pattern">Aranacak Metin:</label><br>
        <textarea name="search_pattern" id="search_pattern" rows="3" cols="50" required></textarea><br><br>

        <label for="replace_text">Değiştirilecek Metin:</label><br>
        <textarea name="replace_text" id="replace_text" rows="3" cols="50" required></textarea><br><br>

        <button type="submit" name="process">Değiştir</button>
    </form>
    
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
</body>
</html>