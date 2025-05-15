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

    <title>bsdev wuaze uzantisayisi</title>

</head>
<body>

    <h5><i class="fas fa-file-alt"></i> Uzantı Sayısı</h5>
    <br>
    <?php
 
        $baseDirectory = $_SERVER['DOCUMENT_ROOT']; // diğer durumlar için varsayılan
    

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDirectory));

    $extensions = [];
    $totalSize = 0;

    foreach ($files as $file) {
        if ($file->isFile()) {
            $extension = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
            $size = $file->getSize();
            $totalSize += $size;

            if (!isset($extensions[$extension])) {
                $extensions[$extension] = ['count' => 0, 'size' => 0];
            }

            $extensions[$extension]['count']++;
            $extensions[$extension]['size'] += $size;
        }
    }

    function formatSize($size) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($size >= 1024 && $i < 4) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    function getFileIcon($extension) {
        $iconMap = [
            'html' => 'fab fa-html5',
            'css' => 'fab fa-css3-alt',
            'js' => 'fab fa-js-square',
            'php' => 'fab fa-php',
            'jpg' => 'far fa-file-image',
            'png' => 'far fa-file-image',
            'pdf' => 'far fa-file-pdf',
            'doc' => 'far fa-file-word',
            'docx' => 'far fa-file-word',
            'xls' => 'far fa-file-excel',
            'xlsx' => 'far fa-file-excel',
            'zip' => 'far fa-file-archive',
            'rar' => 'far fa-file-archive',
            'py' => 'fab fa-python',
            'db' => 'fas fa-database',
            'mp3' => 'fas fa-file-audio',
            'wav' => 'fas fa-file-audio',
            'url' => 'fas fa-link',
            'config' => 'fas fa-cogs',
            'map' => 'fas fa-map',
            'jpeg' => 'far fa-file-image',
            'tff' => 'fas fa-font',
            'sh' => 'fas fa-terminal',
            'scss' => 'fab fa-sass',
            'htaccess' => 'fas fa-file-alt',
            'schema' => 'fas fa-database',
        ];

        return isset($iconMap[$extension]) ? $iconMap[$extension] : 'far fa-file';
    }

  echo "<table class='table table-bordered'>";
echo "<tr><th>Uzantı</th><th>Dosya Sayısı</th><th>Toplam Boyut</th></tr>";

foreach ($extensions as $ext => $data) {
    $icon = getFileIcon($ext);
    echo "<tr>";
    echo "<td><i class='$icon file-icon'></i>" . htmlspecialchars($ext) . "</td>";
    echo "<td>" . htmlspecialchars($data['count']) . "</td>";
    echo "<td>" . htmlspecialchars(formatSize($data['size'])) . "</td>";
    echo "</tr>";
}

echo "<tr class='table-info'>";
echo "<td><strong><i class='fas fa-sum'></i> Toplam</strong></td>";
echo "<td><strong>" . array_sum(array_column($extensions, 'count')) . "</strong></td>";
echo "<td><strong>" . formatSize($totalSize) . "</strong></td>";
echo "</tr>";

echo "</table>";
    ?>
    
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
    

</body>
</html>
