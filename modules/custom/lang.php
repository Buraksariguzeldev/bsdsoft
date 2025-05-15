<?php
session_start();

$rol_kontrol_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/rol_kontrol.php';
if (file_exists($rol_kontrol_path)) {
    include($rol_kontrol_path);
    if (function_exists('rol_kontrol')) {
        rol_kontrol(1);
    }
}

// Navigasyon ekleniyor
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

?>
<!DOCTYPE html>
<html lang="tr">
<head>

<title>HTML Lang Özelliği Değiştirme</title>
    
</head>
<body class="bg-light">


<h5><i class="fas fa-edit"></i>Lang Özelliği Değiştirme</h5>
<div class="container mt-5">
    <div class="card shadow">

        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                Bu işlem tüm PHP ve HTML dosyalarındaki lang özelliğini değiştirecektir.
            </div>

            <form method="post">
                <div class="mb-3">
                    <label for="new_lang" class="form-label">Yeni Dil Kodu:</label>
                    <input type="text" name="new_lang" id="new_lang" class="form-control" placeholder="Örn: tr, en, de" value="tr" required>
                </div>
                <button type="submit" name="action" value="change_lang" class="btn btn-success">
                    <i class="fas fa-language"></i> Lang Özelliğini Değiştir
                </button>
            </form>

            <hr>

            <?php
            $baseDirectory = $_SERVER['DOCUMENT_ROOT'];

            function changeHtmlLang($directory, $newLang) {
                $changedFiles = [];
                $allowedExtensions = ['html', 'php']; // Hem HTML hem PHP dosyalarına bakıyor

                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

                foreach ($iterator as $file) {
                    if ($file->isFile() && in_array(strtolower($file->getExtension()), $allowedExtensions)) {
                        $filePath = $file->getPathname();
                        $content = file_get_contents($filePath);

                        // Eğer lang özelliği varsa değiştir
                        if (preg_match('/<html\s+[^>]*lang=["\']([^"\']*)["\']/i', $content)) {
                            $newContent = preg_replace('/(<html\s+[^>]*lang=["\'])([^"\']*)(["\'])/i', "$1$newLang$3", $content);
                        } else {
                            // Eğer lang özelliği yoksa ekle
                            $newContent = preg_replace('/<html([^>]*)>/i', "<html$1 lang=\"$newLang\">", $content);
                        }

                        if ($content !== $newContent) {
                            file_put_contents($filePath, $newContent);
                            $changedFiles[] = $filePath;
                        }
                    }
                }
                return $changedFiles;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_lang') {
                $newLang = htmlspecialchars(trim($_POST['new_lang'])); // Güvenlik için temizleme
                $changedFiles = changeHtmlLang($baseDirectory, $newLang);

                if (!empty($changedFiles)) {
                    echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Lang özelliği başarıyla değiştirildi.</div>';
                    echo '<div class="alert alert-info"><strong>Değiştirilen Dosyalar:</strong><ul>';
                    foreach ($changedFiles as $file) {
                        echo "<li>" . htmlspecialchars($file) . "</li>";
                    }
                    echo '</ul></div>';
                } else {
                    echo '<div class="alert alert-secondary"><i class="fas fa-exclamation-circle"></i> Hiçbir dosyada değişiklik yapılmadı.</div>';
                }
            }
            ?>

        </div>
    </div>
</div>



<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>

</body>
</html>