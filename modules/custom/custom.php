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
        'href' => 'olustur.php',
        'icon' => 'fas fa-plus-circle',
        'text' => 'Oluştur'
    ],
    [
        'href' => 'silme.php',
        'icon' => 'fas fa-trash-alt',
        'text' => 'Silme'
    ],
    [
        'href' => 'tasima.php',
        'icon' => 'fas fa-exchange-alt',
        'text' => 'Taşıma'
    ],
    [
        'href' => 'inckaldir.php',
        'icon' => 'fas fa-trash-alt',
        'text' => 'Include Silme'
    ],
    [
        'href' => 'incdegis.php',
        'icon' => 'fas fa-exchange-alt',
        'text' => 'Include Değiştirme'
    ],
    [
        'href' => 'adduzenle.php',
        'icon' => 'fas fa-edit',
        'text' => 'Ad Düzenle'
    ],
    [
        'href' => 'kodduzenleyici.php',
        'icon' => 'fas fa-code',
        'text' => 'Kod Düzenleme'
    ],
    [
        'href' => 'lang.php',
        'icon' => 'fas fa-language',
        'text' => 'Lang Düzenleme'
    ],
    [
        'href' => 'değişken_duzenle.php',
        'icon' => 'fa-brands fa-php',
        'text' => 'Değişken Düzenleme'
    ]
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>Custom İşlemler</title>
</head>
<body>

<h5><i class="fas fa-cogs"></i> Custom İşlemler</h5>

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