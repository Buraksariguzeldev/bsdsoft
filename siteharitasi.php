<?php
include_once('assets/src/include/navigasyon.php');

// Kök dizini belirle
$serverRoot = $_SERVER['DOCUMENT_ROOT'];
$currentPath = isset($_GET['path']) ? $_GET['path'] : $serverRoot;

function listDirectories($dir)
{
    $items = scandir($dir);
    
    echo "<ul style='list-style: none; padding-left: 15px;'>";
    foreach ($items as $item) {
        if ($item === "." || $item === "..") {
            continue;
        }

        $path = $dir . DIRECTORY_SEPARATOR . $item; 

        if (is_dir($path)) {
            // 📁 Klasörler için aç/kapat yapılabilir div ekliyoruz
            echo "<li class='folder'>
                    <span class='toggle ibm-plex-mono-regular' onclick='toggleFolder(this)'>📁 " . $item . "</span>
                    <ul class='nested' style='display: none;'>";
            listDirectories($path);
            echo "</ul>
                  </li>";
        } else {
            echo "<li class='file'><a class='ibm-plex-mono-regular' href='" . str_replace($_SERVER['DOCUMENT_ROOT'], '', $path) . "'>📄 " . $item . "</a></li>";
        }
    }
    echo "</ul>";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>Site Haritası</title>
    
</head>
<body>

<h5>
    <i class="bi bi-diagram-3"></i> 
    Site Haritası
</h5>

<div class="bg-secondary text-black mb-3 p-2">
    <i class="bi bi-search"></i> ARA...
    <input class="ibm-plex-mono-regular" type="text" id="searchBox" placeholder="
    
    <?php echo htmlspecialchars($currentPath); ?>" 
           onkeyup="filterList()" style="width: 100%; padding: 5px; margin-bottom: 10px;">
</div>

<hr>



<?php listDirectories($currentPath); ?>

<script>
function toggleFolder(element) {
    let nestedList = element.nextElementSibling;
    nestedList.style.display = nestedList.style.display === "none" ? "block" : "none";
}

function filterList() {
    let input = document.getElementById("searchBox").value.toLowerCase();
    let items = document.querySelectorAll(".file, .folder .toggle");

    items.forEach(item => {
        let text = item.innerText.toLowerCase();
        let parentLi = item.closest("li");

        if (text.includes(input)) {
            parentLi.style.display = "";
        } else {
            parentLi.style.display = "none";
        }
    });
}
</script>

</body>
</html>