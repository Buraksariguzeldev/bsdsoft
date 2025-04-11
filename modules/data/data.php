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

// Menü öğeleri dizisi
$menu_items = [
    [
        'href' => 'liste.php',
        'icon' => 'fas fa-list',
        'text' => 'Dosyaları Listele'
    ],
    [
        'href' => 'dosyaindir.php',
        'icon' => 'fas fa-download',
        'text' => 'Dosya İndir'
    ],
    [
        'href' => 'aynisim.php',
        'icon' => 'fas fa-copy',
        'text' => 'Aynı İsim'
    ],
    [
        'href' => 'uzantılar.php',
        'icon' => 'fas fa-file-code',
        'text' => 'Uzantılar'
    ],
    [
        'href' => 'klasorsayisi.php',
        'icon' => 'fas fa-folder',
        'text' => 'Klasör Sayısı'
    ],
    [
        'href' => 'disk_kullanimi.php',
        'icon' => 'fas fa-hdd',
        'text' => 'Disk Kullanımı'
    ],
    [
        'href' => 'uzantisayisi.php',
        'icon' => 'fas fa-file-alt',
        'text' => 'Uzantı Sayısı'
    ],
    [
        'href' => 'enbuyukenkucuk.php',
        'icon' => 'fas fa-sort-amount-down',
        'text' => 'En Büyük - En Küçük'
    ],
    [
        'href' => 'sondeğişiklik.php',
        'icon' => 'fas fa-history',
        'text' => 'Son Değişiklik'
    ],
    [
        'href' => 'oluşturulma.php',
        'icon' => 'fas fa-calendar-plus',
        'text' => 'Oluşturulma Tarihi'
    ],
    [
        'href' => 'agacgorunumu.php',
        'icon' => 'fas fa-tree',
        'text' => 'Ağaç Görünümü JSON'
    ],
   [
        'href' => 'fontyukleyici.php',
        'icon' => 'bi bi-fonts',
        'text' => 'fonts'
    ]
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>Klasör ve Dosya Yönetimi</title>
    
</head>
<body>

    <h5><i class="fas fa-folder-open"></i> Klasör ve Dosya Yönetimi</h5>


<nav class="menu-container">
    <?php foreach ($menu_items as $item): ?>
        <a href="<?php echo $item['href']; ?>" class="menu-item bsd-navlink1">
            <i class="<?php echo $item['icon']; ?>"></i>
            <div><?php echo $item['text']; ?></div>
        </a>
    <?php endforeach; ?>
</nav>


<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>


</body>
</html>