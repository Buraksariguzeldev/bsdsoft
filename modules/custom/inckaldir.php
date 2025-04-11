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

    <title>Include Silici</title>

</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Include Silici</h1>
    <form method="post" class="text-center">
        <div class="mb-3">
            <label for="pattern" class="form-label">Silinecek Include</label>
            <input type="text" name="pattern" id="pattern" class="form-control" placeholder="Örn: include('../bsd_yonetim/src/include/kullanici_adi.php');" required>
        </div>
        <button type="submit" name="process" class="btn btn-danger">Sil</button>
    </form>
    <div class="mt-4">
        <h5>Sonuç:</h5>
        <pre class="bg-light p-3 border rounded">
<?php
if (isset($_POST['process'])) {
    $inputPattern = trim($_POST['pattern']);
    $escapedPattern = preg_quote($inputPattern, '/'); // Deseni düzenli ifadeye uygun hale getir
    $pattern = "/$escapedPattern/i";

    // Aranacak temel dizin
    $baseDir = $_SERVER['DOCUMENT_ROOT'] . '/';

    // Recursive iterator ile dizindeki tüm dosyaları bul
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    $deletedCount = 0;

    foreach ($files as $file) {
        if ($file->getExtension() === 'php') {
            $fileContent = file_get_contents($file->getPathname());

            // Deseni dosyada bul ve sil
            if (preg_match($pattern, $fileContent)) {
                $newContent = preg_replace($pattern, '', $fileContent);
                file_put_contents($file->getPathname(), $newContent);
                echo $file->getPathname() . " - Silindi\n";
                $deletedCount++;
            }
        }
    }

    if ($deletedCount === 0) {
        echo "Belirtilen include bulunamadı.";
    } else {
        echo "\nToplam $deletedCount dosyada değişiklik yapıldı.";
    }
}
?>
        </pre>
    </div>
</div>


<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
</body>
</html>