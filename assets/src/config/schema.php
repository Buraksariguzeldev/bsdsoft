<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php'); // Veritabanı bağlantısını dahil et

global $vt; // PDO bağlantısını al

$file = fopen($_SERVER['DOCUMENT_ROOT'] . "/schema.sql", "w"); // schema.sql dosyasını aç

if (!$file) {
    die("Dosya oluşturulamadı!");
}

// Veritabanındaki tüm tabloları al
$sql = "SHOW TABLES";
$result = $vt->query($sql);

if (!$result) {
    die("Tablolar alınamadı!");
}

while ($row = $result->fetch(PDO::FETCH_NUM)) {
    $table = $row[0];
    
    // Tablo yapısını al
    $stmt = $vt->query("SHOW CREATE TABLE `$table`");
    $createTableQuery = $stmt->fetch(PDO::FETCH_NUM)[1] . ";\n\n";
    
    // Dosyaya yaz
    fwrite($file, $createTableQuery);
}

fclose($file);
echo "Şema başarıyla kaydedildi: " . $_SERVER['DOCUMENT_ROOT'] . "/schema.sql";

$vt = null; // Bağlantıyı kapat
?>