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

<?php
$baseDirectory = $_SERVER['DOCUMENT_ROOT'];

function findTag($directory, $tag) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    $matches = array();
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array(pathinfo($file, PATHINFO_EXTENSION), ['php', 'html'])) {
            $content = file_get_contents($file);
            if (strpos($content, $tag) !== false) {
                $lines = explode("\n", $content);
                foreach ($lines as $lineNumber => $line) {
                    if (strpos($line, $tag) !== false) {
                        $matches[$file->getPathname()][] = [
                            'line' => $lineNumber + 1,
                            'content' => trim($line)
                        ];
                    }
                }
            }
        }
    }
    return $matches;
}

function replaceTag($directory, $oldTag, $newTag) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    $changes = array();
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array(pathinfo($file, PATHINFO_EXTENSION), ['php', 'html'])) {
            $content = file_get_contents($file);
            $newContent = str_replace($oldTag, $newTag, $content, $count);
            if ($count > 0) {
                file_put_contents($file, $newContent);
                $changes[$file->getPathname()] = $count;
            }
        }
    }
    return $changes;
}

function displayMatches($matches) {
    if (!empty($matches)) {
        echo "<div class='match-log'>";
        echo "<h5><i class='fas fa-search'></i> Eşleşme Sonuçları</h5>";
        echo "<hr>";
        foreach ($matches as $file => $fileMatches) {
            echo "<div class='file-match'>";
            echo "<strong><i class='fas fa-file-code'></i> " . htmlspecialchars($file) . "</strong>";
            echo "<ul>";
            foreach ($fileMatches as $match) {
                echo "<li>Satır " . $match['line'] . ": <code>" . htmlspecialchars($match['content']) . "</code></li>";
            }
            echo "</ul>";
            echo "</div>";
            echo "<hr>";
        }
        echo "</div>";
    } else {
        echo "<div class='no-match'><i class='fas fa-exclamation-circle'></i> Eşleşen içerik bulunamadı.</div>";
    }
}

function displayChanges($changes) {
    if (!empty($changes)) {
        echo "<div class='change-log'>";
        echo "<h5><i class='fas fa-clipboard-list'></i> Değişiklik Günlüğü</h5>";
        echo "<hr>";
        foreach ($changes as $file => $count) {
            echo "<div class='file-change'>";
            echo "<strong><i class='fas fa-file-code'></i> " . htmlspecialchars($file) . "</strong>";
            echo "<p>$count etiket değiştirildi.</p>";
            echo "</div>";
            echo "<hr>";
        }
        echo "</div>";
    } else {
        echo "<div class='no-change'><i class='fas fa-exclamation-circle'></i> Hiçbir değişiklik yapılmadı.</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>HTML Etiket Değiştirme</title>

</head>
<body>
  

    <div class="container">
        <h5><i class="fas fa-edit fa-icon"></i> HTML Etiket Değiştirme</h5>
        <div class="warning">
            <i class="fas fa-exclamation-triangle"></i> Tüm HTML etiketleri kabul edilir. Etiketleri tam olarak girin.
        </div>
        <form method="post">
            <input type="text" name="old_tag" placeholder="Aranacak Etiket (örn: <p>İçerik</p>)" required>
            <button type="submit" name="action" value="search"><i class="fas fa-search fa-icon"></i> Etiketi Ara</button>
        </form>
        <br>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $oldTag = $_POST['old_tag'] ?? '';

            if (!empty($oldTag)) {
                if ($_POST['action'] == 'search') {
                    $matches = findTag($baseDirectory, $oldTag);
                    if (!empty($matches)) {
                        echo "<div class='success'><i class='fas fa-check-circle'></i> Etiket bulundu! Aşağıda detayları görebilirsiniz.</div>";
                        displayMatches($matches);
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='old_tag' value='" . htmlspecialchars($oldTag) . "'>";
                        echo "<textarea name='new_tag' placeholder='Yeni Etiket' required></textarea>";
                        echo "<button type='submit' name='action' value='replace'><i class='fas fa-sync fa-icon'></i> Etiketi Değiştir</button>";
                        echo "</form>";
                    } else {
                        echo "<div class='error'><i class='fas fa-exclamation-triangle'></i> Etiket bulunamadı.</div>";
                    }
                } elseif ($_POST['action'] == 'replace') {
                    $newTag = $_POST['new_tag'] ?? '';
                    if (!empty($newTag)) {
                        $changes = replaceTag($baseDirectory, $oldTag, $newTag);
                        displayChanges($changes);
                    } else {
                        echo "<div class='error'><i class='fas fa-exclamation-triangle'></i> Yeni etiket girilmelidir.</div>";
                    }
                }
            } else {
                echo "<div class='error'><i class='fas fa-exclamation-triangle'></i> Aranacak etiket girilmelidir.</div>";
            }
        }
        ?>
    </div>
    
    
<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
</body>
</html>
