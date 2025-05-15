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

    <title>Dosya ve Klasör Sil</title>

 
</head>
<body>

    
    <h5><i class="fas fa-trash-alt"></i> Dosya veya Klasör Sil</h5>

<form method="post" class="p-3 bg-light rounded shadow-sm" onsubmit="return confirmDelete();">
    <div class="mb-3">
        <label for="dir" class="form-label fw-bold">
            <i class="fas fa-folder me-2"></i> Dizin Yolu:
        </label>
        <input type="text" name="dir" id="dir" class="form-control" placeholder="Dizin yolu" required>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label fw-bold">
            <i class="fas fa-file-alt me-2"></i> Silinecek İsim:
        </label>
        <input type="text" name="name" id="name" class="form-control" placeholder="İsim" required>
    </div>

    <button type="submit" class="btn btn-danger w-100">
        <i class="fas fa-trash"></i> Sil
    </button>
</form>

<script>
function confirmDelete() {
    return confirm("Bu öğeyi silmek istediğinizden emin misiniz?");
}
</script>
   

    <?php

    $baseDirectory = $_SERVER['DOCUMENT_ROOT']; // Kök dizin yolu

    $message = '';

    // Klasör ve içindekileri silen fonksiyon
    function deleteDirectory($dir) {
        if (!is_dir($dir)) {
            return unlink($dir);
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            deleteDirectory($dir . DIRECTORY_SEPARATOR . $item);
        }

        return rmdir($dir);
    }

    // Silme işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $dir = $_POST['dir'] ?? '';
        $fullPath = rtrim($baseDirectory, '/') . '/' . ltrim($dir, '/');

        // Güvenlik kontrolü
        if (strpos($dir, '..') !== false || strpos($name, '..') !== false) {
            $message = "<i class='fas fa-exclamation-triangle'></i> Erişim reddedildi.";
        } else {
            // Dizin mevcut mu kontrol et
            if (is_dir($fullPath)) {
                $targetPath = $fullPath . '/' . $name;

                // Hedef dosya veya klasör mevcut mu kontrol et
                if (file_exists($targetPath)) {
                    if (is_dir($targetPath)) {
                        // Klasör silme
                        if (deleteDirectory($targetPath)) {
                            $message = "<i class='fas fa-check-circle'></i> Klasör başarıyla silindi.";
                        } else {
                            $message = "<i class='fas fa-times-circle'></i> Klasör silinirken bir hata oluştu: " . htmlspecialchars(print_r(error_get_last(), true));
                        }
                    } else {
                        // Dosya silme
                        if (unlink($targetPath)) {
                            $message = "<i class='fas fa-check-circle'></i> Dosya başarıyla silindi.";
                        } else {
                            $message = "<i class='fas fa-times-circle'></i> Dosya silinirken bir hata oluştu: " . htmlspecialchars(print_r(error_get_last(), true));
                        }
                    }
                } else {
                    $message = "<i class='fas fa-exclamation-circle'></i> Hedef dosya veya klasör bulunamadı.";
                }
            } else {
                $message = "<i class='fas fa-folder-minus'></i> Dizin bulunamadı.";
            }
        }
    }
    ?>

    <?php if ($message): ?>
    <p><?php echo $message; ?></p>
    <?php endif; ?>
    

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
    
</body>
</html>
