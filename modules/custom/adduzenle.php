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

    <title>Dosya veya Klasör Yeniden Adlandır</title>

</head>
<body>

    <h5><i class="fas fa-edit"></i> Dosya veya Klasör Yeniden Adlandır</h5>
    <br>
    <form method="post">
        <label for="dirPath"><i class="fas fa-folder"></i> Dizin Yolu:</label>
        <input type="text" name="dirPath" id="dirPath" placeholder="Dizin yolu (../ veya ../siteharitasi.php gibi)" required>
        
        <label for="currentName"><i class="fas fa-file-alt"></i> Eski Ad:</label>
        <input type="text" name="currentName" id="currentName" placeholder="Eski ad" required>
        
        <label for="newName"><i class="fas fa-file"></i> Yeni Ad:</label>
        <input type="text" name="newName" id="newName" placeholder="Yeni ad" required>
        
        <button type="submit" class="bsd-btn1"><i class="fas fa-save"></i> Yeniden Adlandır</button>
    </form>

    <?php
    
    // Kök dizini belirleme
    $baseDirectory = $_SERVER['DOCUMENT_ROOT'];
    $message = '';
    $error_code = 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dirPath = trim($_POST['dirPath']);
        $currentName = trim($_POST['currentName']);
        $newName = trim($_POST['newName']);

        // Kullanıcı tarafından girilen mevcut dizin yolunu birleştir
        $currentFullPath = rtrim($baseDirectory, '/') . '/' . ltrim($dirPath, '/') . '/' . $currentName;
        $newFullPath = dirname($currentFullPath) . '/' . $newName;

        // Güvenlik kontrolü: baseDirectory'nin dışına çıkışları engelle
        $realBase = realpath($baseDirectory);
        $realCurrentPath = realpath(dirname($currentFullPath));
        
        if ($realCurrentPath === false || strpos($realCurrentPath, $realBase) !== 0) {
            $error_code = 403;
            $message = "Erişim reddedildi.";
        } else {
            // Yeniden adlandırma işlemi
            if (file_exists($currentFullPath)) {
                if (rename($currentFullPath, $newFullPath)) {
                    $message = "Dosya veya klasör başarıyla yeniden adlandırıldı.";
                } else {
                    $error_code = 500;
                    $message = "Yeniden adlandırma işlemi sırasında bir hata oluştu.";
                }
            } else {
                $error_code = 404;
                $message = "Dosya veya klasör bulunamadı.";
            }
        }
    }
    ?>
    <hr>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
        <?php if ($error_code): ?>
            <p><i class="fas fa-exclamation-triangle"></i> Hata Kodu: <?php echo htmlspecialchars($error_code); ?></p>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
    
</body>
</html>
