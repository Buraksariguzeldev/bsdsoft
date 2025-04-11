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

    <title>Dosya veya Klasör Taşı</title>

</head>
<body>

    <h5><i class="fas fa-exchange-alt"></i> Dosya veya Klasör Taşı</h5>
<form method="post" class="p-3 bg-light rounded shadow-sm" onsubmit="return confirmMove();">
    <div class="mb-3">
        <label for="sourceDir" class="form-label fw-bold">
            <i class="fas fa-folder me-2"></i> Kaynak Dizin:
        </label>
        <input type="text" name="sourceDir" id="sourceDir" class="form-control" placeholder="Kaynak dizin" required>
    </div>

    <div class="mb-3">
        <label for="itemName" class="form-label fw-bold">
            <i class="fas fa-file me-2"></i> Taşınacak İsim:
        </label>
        <input type="text" name="itemName" id="itemName" class="form-control" placeholder="Taşınacak İsim" required>
    </div>

    <div class="mb-3">
        <label for="targetDir" class="form-label fw-bold">
            <i class="fas fa-folder-open me-2"></i> Hedef Dizin:
        </label>
        <input type="text" name="targetDir" id="targetDir" class="form-control" placeholder="Hedef dizin" required>
    </div>

    <button type="submit" class="btn btn-warning w-100">
        <i class="fas fa-truck"></i> Taşı
    </button>
</form>

<script>
function confirmMove() {
    return confirm("Bu öğeyi taşımak istediğinizden emin misiniz?");
}
</script>
    <?php
 
    // Kök dizini doğrudan site URL'si olarak ayarla
    $baseDirectory = $_SERVER['DOCUMENT_ROOT'];
    $message = '';

    // Taşıma işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sourceDir = trim($_POST['sourceDir']);
        $targetDir = trim($_POST['targetDir']);
        $itemName = trim($_POST['itemName']);

        $sourcePath = rtrim($baseDirectory, '/') . '/' . ltrim($sourceDir, '/') . '/' . $itemName;
        $targetPath = rtrim($baseDirectory, '/') . '/' . ltrim($targetDir, '/') . '/' . $itemName;

        // Güvenlik kontrolü
        if (strpos($sourceDir, '..') !== false || strpos($targetDir, '..') !== false) {
            $message = "Erişim reddedildi.";
        } else {
            // Kaynak dosya veya klasör mevcut mu kontrol et
            if (file_exists($sourcePath)) {
                // Hedef dizin var mı kontrol et, yoksa oluştur
                $targetDirPath = rtrim($baseDirectory, '/') . '/' . ltrim($targetDir, '/');
                if (!is_dir($targetDirPath)) {
                    if (mkdir($targetDirPath, 0777, true)) {
                        $message = "Hedef dizin oluşturuldu.";
                    } else {
                        $message = "Hedef dizin oluşturulurken bir hata oluştu.";
                    }
                }
                // Taşıma işlemi
                if (rename($sourcePath, $targetPath)) {
                    $message = "Dosya veya klasör başarıyla taşındı.";
                } else {
                    $message = "Taşıma işlemi sırasında bir hata oluştu: " . htmlspecialchars(print_r(error_get_last(), true));
                }
            } else {
                $message = "Kaynak dosya veya klasör bulunamadı.";
            }
        }
    }
    ?>
    <hr>
    <?php if ($message): ?>
        <p><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
    
</body>
</html>
