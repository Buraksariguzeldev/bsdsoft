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

<title>En Büyük ve En Küçük Dosya/Klasör</title>

</head>
<body>
    <div class="container">


        <h5><i class="fas fa-chart-bar icon"></i>En Büyük ve En Küçük Dosya/Klasör Bilgileri</h5>

        <?php
     

        $baseDirectory = $_SERVER['DOCUMENT_ROOT'];
        $files = glob($baseDirectory . "/*");
        $largestFile = $smallestFile = $largestFolder = $smallestFolder = null;
        $largestFileSize = $smallestFileSize = $largestFolderSize = $smallestFolderSize = null;
        $totalFileCount = $totalFolderCount = 0;

        function formatSize($size) {
            if ($size >= 1073741824) {
                return number_format($size / 1073741824, 2) . ' GB';
            } elseif ($size >= 1048576) {
                return number_format($size / 1048576, 2) . ' MB';
            } elseif ($size >= 1024) {
                return number_format($size / 1024, 2) . ' KB';
            } elseif ($size > 1) {
                return $size . ' bytes';
            } elseif ($size == 1) {
                return $size . ' byte';
            } else {
                return '0 bytes';
            }
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                $totalFileCount++;
                $size = filesize($file);
                
                if ($largestFile === null || $size > $largestFileSize) {
                    $largestFile = $file;
                    $largestFileSize = $size;
                }
                if ($smallestFile === null || $size < $smallestFileSize) {
                    $smallestFile = $file;
                    $smallestFileSize = $size;
                }
            } elseif (is_dir($file)) {
                $totalFolderCount++;
                $folderSize = 0;
                foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($file)) as $f) {
                    if ($f->isFile()) {
                        $folderSize += $f->getSize();
                    }
                }
                
                if ($largestFolder === null || $folderSize > $largestFolderSize) {
                    $largestFolder = $file;
                    $largestFolderSize = $folderSize;
                }
                if ($smallestFolder === null || $folderSize < $smallestFolderSize) {
                    $smallestFolder = $file;
                    $smallestFolderSize = $folderSize;
                }
            }
        }
        
        echo "<table>";
        echo "<tr><th>Kategori</th><th>Yol</th><th>Boyut</th></tr>";

        if ($largestFile) {
            echo "<tr><td><i class='fas fa-file icon'></i>En Büyük Dosya</td><td>" . htmlspecialchars(basename($largestFile)) . "</td><td>" . formatSize($largestFileSize) . "</td></tr>";
        } else {
            echo "<tr><td><i class='fas fa-file icon'></i>En Büyük Dosya</td><td colspan='2'>Bulunamadı</td></tr>";
        }

        if ($smallestFile) {
            echo "<tr><td><i class='fas fa-file icon'></i>En Küçük Dosya</td><td>" . htmlspecialchars(basename($smallestFile)) . "</td><td>" . formatSize($smallestFileSize) . "</td></tr>";
        } else {
            echo "<tr><td><i class='fas fa-file icon'></i>En Küçük Dosya</td><td colspan='2'>Bulunamadı</td></tr>";
        }

        if ($largestFolder) {
            echo "<tr><td><i class='fas fa-folder icon'></i>En Büyük Klasör</td><td>" . htmlspecialchars(basename($largestFolder)) . "</td><td>" . formatSize($largestFolderSize) . "</td></tr>";
        } else {
            echo "<tr><td><i class='fas fa-folder icon'></i>En Büyük Klasör</td><td colspan='2'>Bulunamadı</td></tr>";
        }

        if ($smallestFolder) {
            echo "<tr><td><i class='fas fa-folder icon'></i>En Küçük Klasör</td><td>" . htmlspecialchars(basename($smallestFolder)) . "</td><td>" . formatSize($smallestFolderSize) . "</td></tr>";
        } else {
            echo "<tr><td><i class='fas fa-folder icon'></i>En Küçük Klasör</td><td colspan='2'>Bulunamadı</td></tr>";
        }

        echo "</table>";

        echo "<p>Toplam Dosya Sayısı: $totalFileCount</p>";
        echo "<p>Toplam Klasör Sayısı: $totalFolderCount</p>";
        ?>

       
 <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
        
    </div>
</body>
</html>
