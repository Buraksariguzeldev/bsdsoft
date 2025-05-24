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
// Diğer include'lar ve PHP kodu buraya gelecek


    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
?>

<?php
// Kök dizini ayarla
$baseDirectory = $_SERVER['DOCUMENT_ROOT'];

// Eğer DOCUMENT_ROOT boşsa, alternatif bir yol belirleyelim
if (empty($baseDirectory)) {
    $baseDirectory = '/storage/emulated/0/Buraksariguzeldev/hızlı_klasör';
}

// Sunucu adresini al
$serverName = $_SERVER['SERVER_NAME'];

// Tam site URL'sini oluştur
$siteURL = ($serverName == 'localhost') ? 'http://localhost:8003' : 'https://buraksariguzeldev.wuaze.com';

// Değiştirilecek içeriği al
$searchContent = isset($_POST['search_content']) ? trim($_POST['search_content']) : '';

// Yeni içeriği al
$replaceContent = isset($_POST['replace_content']) ? trim($_POST['replace_content']) : '';

function replaceInFiles($dir, $search, $replace) {
    if (empty($dir) || !is_dir($dir)) {
        echo "Hata: Geçerli bir dizin belirtilmedi.";
        return;
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST,
        RecursiveIteratorIterator::CATCH_GET_CHILD
    );

    foreach ($files as $file) {
        if ($file->isDir()) continue;
        if ($file->getExtension() != 'php') continue;

        $content = file_get_contents($file->getRealPath());
        $newContent = str_replace($search, $replace, $content);

        if ($content !== $newContent) {
            file_put_contents($file->getRealPath(), $newContent);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($searchContent) && !empty($replaceContent)) {
    replaceInFiles($baseDirectory, $searchContent, $replaceContent);
    echo "Değişiklikler başarıyla uygulandı.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>PHP Dosya İçeriği Değiştirme</title>
</head>
<body>
    <h5>PHP Dosya İçeriği Değiştirme</h5>
    <form method="post">
        <input type="text" name="search_content" placeholder="Aranacak içerik ($ işareti ile)" required>
        <input type="text" name="replace_content" placeholder="Yeni içerik ($ işareti ile)" required>
        <button type="submit" class="bsd-btn1">Değiştir</button>
    </form>
    <div class="info">
        <p>Not: Aranacak ve değiştirilecek içerikleri tam olarak girin, $ işaretleri dahil.</p>
        <p>Örnek: Aranacak içerik "$messi$", Yeni içerik "$Ronaldo$"</p>
        <p>Kök Dizin: <?php echo $baseDirectory; ?></p>
        <p>Site URL: <?php echo $siteURL; ?></p>
    </div>
    
<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>    
</body>
</html>
