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
    <title>Dosya İndir</title>
    <script>
        function removeMessage() {
            var messageElement = document.getElementById('message');
            if (messageElement) {
                messageElement.style.display = 'none';
            }
        }
        setTimeout(removeMessage, 5000);
    </script>
</head>
<body>
    <h5><i class="fas fa-download"></i> Dosya İndir</h5>

    <form method="post" class="p-3 border rounded bg-light">
        <div class="mb-3">
            <label for="dirPath" class="form-label">
                <i class="fas fa-folder"></i> Dizin Yolu:
            </label>
            <input type="text" name="dirPath" id="dirPath" class="form-control" 
                   placeholder="Dizin yolu (../ veya ../siteharitasi.php gibi)" required>
        </div>
        <div class="mb-3">
            <label for="itemName" class="form-label">
                <i class="fas fa-file"></i> Dosya veya Klasör Adı:
            </label>
            <input type="text" name="itemName" id="itemName" class="form-control" 
                   placeholder="Dosya veya klasör adı" required>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-download"></i> İndir
        </button>
    </form>

    <?php
    $baseDirectory = $_SERVER['DOCUMENT_ROOT']; // Kök dizin yolu
    $message = '';
    $error_code = 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dirPath = trim($_POST['dirPath']);
        $itemName = trim($_POST['itemName']);
        
        // Kullanıcı tarafından girilen dizin yolunu ve dosya adını birleştir
        $fullPath = rtrim($baseDirectory, '/') . '/' . ltrim($dirPath, '/') . '/' . $itemName;

        if (file_exists($fullPath)) {
            $message = "Dosya bulundu: " . (is_file($fullPath) ? 'Dosya' : 'Klasör');

            if (is_file($fullPath)) {
                // Dosya indirme işlemi
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($fullPath));
                readfile($fullPath);
                exit;
            } else {
                $message = "Bu bir klasördür, dosya indirilemez.";
            }
        } else {
            $error_code = 404;
            $message = "Dosya veya klasör bulunamadı.";
        }
    }
    ?>

    <?php if ($message): ?>
        <div id="message" class="<?php echo $error_code ? 'error' : 'success'; ?>">
            <p>
                <i class="fas <?php echo $error_code ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </p>
            <?php if ($error_code): ?>
                <p class="error">Hata Kodu: <?php echo htmlspecialchars($error_code); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
</body>
</html>