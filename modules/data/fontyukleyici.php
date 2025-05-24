<?php

// navisyon
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>Font Listesi</title>


</head>
<body>

<h3 class="kaushan-script-regular text-center">Font</h3>
<ol class="list-group list-group-numbered">
    <?php
    // fontheader.php dosyasını oku
    $cssContent = file_get_contents('../../assets/src/include/fontheader.php');

    // RegEx ile .sınıf_adı yakala
    preg_match_all('/\.([a-zA-Z0-9\-_]+)\s*\{/', $cssContent, $matches);

    // Bulunan tüm class'ları ol li içine koy
    foreach ($matches[1] as $class) {
        echo "<li class='list-group-item $class'>  
        $class <br>
       </li>";
    }
    ?>
</ol>

</body>
</html>