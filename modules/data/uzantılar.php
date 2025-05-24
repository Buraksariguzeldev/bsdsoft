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

    <title>Dosya Uzantıları</title>

</head>
<body>
    <div class="container">
        <hr>

        
        <h5><i class="fas fa-file-alt"></i> Dosya Uzantılarına Göre Gruplandırılmış Dosyalar:</h5>
        
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="extensionSearch" class="" placeholder="Uzantı ara..." onkeyup="searchExtensions()">
            <i class="" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);"></i>
        </div>

        <?php
        // Kök dizini ayarla
        $baseDirectory = $_SERVER['DOCUMENT_ROOT'];

        function formatSize($size) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $unitIndex = 0;
            while ($size >= 1024 && $unitIndex < 4) {
                $size /= 1024;
                $unitIndex++;
            }
            return round($size, 2) . ' ' . $units[$unitIndex];
        }

        function scanDirectory($dir) {
            global $extensionList;
            $files = scandir($dir);

            foreach ($files as $file) {
                if ($file == '.' || $file == '..') continue;

                $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                $relativePath = str_replace($GLOBALS['baseDirectory'] . DIRECTORY_SEPARATOR, '', $filePath);
                
                if (is_file($filePath)) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $size = filesize($filePath);
                    if ($extension) {
                        if (!isset($extensionList[$extension])) {
                            $extensionList[$extension] = ['files' => [], 'total_size' => 0];
                        }
                        $extensionList[$extension]['files'][] = ['path' => $relativePath, 'size' => $size];
                        $extensionList[$extension]['total_size'] += $size;
                    }
                }
                
                if (is_dir($filePath)) scanDirectory($filePath);
            }
        }

        $extensionList = [];
        scanDirectory($baseDirectory);
        ksort($extensionList);

echo '<table id="extensionTable" class="table table-bordered table-striped">';
echo '<thead class="thead-dark"><tr><th>Uygulama Uzantısı</th><th>Toplam Dosya Sayısı</th><th>Toplam Boyut</th><th>Dosyalar</th></tr></thead>';
echo '<tbody>';
foreach ($extensionList as $extension => $data) {
    echo '<tr class="extension-row">';
    echo '<td><i class="fas fa-file-code"></i> ' . htmlspecialchars($extension) . '</td>';
    echo '<td><i class="fas fa-list-ol"></i> ' . count($data['files']) . '</td>';
    echo '<td><i class="fas fa-hdd"></i> ' . formatSize($data['total_size']) . '</td>';
    echo '<td><ul class="list-unstyled">';
    foreach ($data['files'] as $file) {
        echo '<li><i class="fas fa-file"></i> ' . htmlspecialchars($file['path']) . ' (' . formatSize($file['size']) . ')</li>';
    }
    echo '</ul></td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
        ?>
        
       
  <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
       
    </div>

    <script>
    function searchExtensions() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("extensionSearch");
        filter = input.value.toLowerCase();
        table = document.getElementById("extensionTable");
        tr = table.getElementsByClassName("extension-row");

        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    </script>
</body>
</html>
