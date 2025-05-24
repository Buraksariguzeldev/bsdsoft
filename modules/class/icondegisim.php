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

    <title>Icon Class Değiştirme</title>

</head>
<body>
          <h5><i class="fas fa-edit fa-icon"></i> Icon Class Değiştirme</h5>
<div class="container mt-4">
    <div class="card shadow-sm">

        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="target_class" class="form-label"><i class="fas fa-code"></i> Hedef Class:</label>
                    <input type="text" name="target_class" id="target_class" class="form-control" placeholder="Hedef Class" required>
                </div>

                <div class="mb-3">
                    <label for="new_icon_class" class="form-label"><i class="fas fa-icons"></i> Yeni Icon Class:</label>
                    <input type="text" name="new_icon_class" id="new_icon_class" class="form-control" placeholder="Yeni Icon Class" required>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-sync me-2"></i> Icon Class'ı Değiştir
                </button>
            </form>
        </div>
    </div>

    <br>

    <?php
    $baseDirectory = $_SERVER['DOCUMENT_ROOT'];

    function findAndReplaceIconClass($directory, $targetClass, $newIconClass) {
        $changes = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() == 'php') {
                $content = file_get_contents($file->getPathname());
                if (preg_match("/class\s+$targetClass\s*{/", $content)) {
                    $pattern = "/(\\\$icon_class\s*=\s*['\"])([^'\"]+)(['\"]\s*;)/";
                    $newContent = preg_replace($pattern, "$1$newIconClass$3", $content);
                    if ($newContent != $content) {
                        file_put_contents($file->getPathname(), $newContent);
                        $changes[] = $file->getPathname();
                    }
                }
            }
        }
        return $changes;
    }

    function displayChanges($changes) {
        if (!empty($changes)) {
            echo "<div class='alert alert-success mt-3'>";
            echo "<h5><i class='fas fa-clipboard-check me-2'></i> Değişiklik Yapılan Dosyalar:</h5>";
            echo "<ul class='list-group'>";
            foreach ($changes as $file) {
                echo "<li class='list-group-item'><i class='fas fa-check text-success me-2'></i>" . htmlspecialchars($file) . "</li>";
            }
            echo "</ul></div>";
        } else {
            echo "<div class='alert alert-warning mt-3'><i class='fas fa-exclamation-circle me-2'></i> Hiçbir dosyada değişiklik yapılmadı.</div>";
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $targetClass = $_POST['target_class'] ?? '';
        $newIconClass = $_POST['new_icon_class'] ?? '';

        if (!empty($targetClass) && !empty($newIconClass)) {
            $changes = findAndReplaceIconClass($baseDirectory, $targetClass, $newIconClass);
            displayChanges($changes);
        } else {
            echo "<div class='alert alert-danger mt-3'><i class='fas fa-exclamation-triangle me-2'></i> Hedef class ve yeni icon class girilmelidir.</div>";
        }
    }
    ?>
</div>
    
    
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
   
</body>
</html>
