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

    <title>Dizin Yapısı</title>
    
</head>
<body>
    <h5> <i class="fas fa-tree"></i>Ağaç görünümü json</h5>
    <?php
    function directoryToArray($directory) {
        $result = [];
        
        if (!is_dir($directory)) {
            return $result;
        }

        $files = scandir($directory);
        
        foreach ($files as $key => $value) {
            $path = realpath($directory . DIRECTORY_SEPARATOR . $value);
            
            if (!is_dir($path)) {
                $result[] = $value;
            } else if ($value != "." && $value != "..") {
                $result[$value] = directoryToArray($path);
            }
        }
        
        return $result;
    }

    $allowedDomains = ['localhost:8001', 'buraksariguzeldev.wuaze.com'];
    $currentDomain = $_SERVER['HTTP_HOST'];

    if (!in_array($currentDomain, $allowedDomains)) {
        die("Bu alan adından erişim reddedildi.");
    }

    $baseDirectory = $_SERVER['DOCUMENT_ROOT'];
    $directoryTree = directoryToArray($baseDirectory);

    $filePath = '../../assets/src/json/agacgorunumu.json';
    file_put_contents($filePath, json_encode($directoryTree, JSON_PRETTY_PRINT));

    echo "<div> <pre>". htmlspecialchars(file_get_contents($filePath)) .
    "</pre> </div>";
    ?>
</body>
</html>
