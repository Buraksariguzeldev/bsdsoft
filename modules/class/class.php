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
        'href' => 'iciceclass.php',
        'icon' => 'fas fa-layer-group',
        'text' => 'İç İçe Class'
    ],
    [
        'href' => 'icondegisim.php',
        'icon' => 'fas fa-exchange-alt',
        'text' => 'Icon Değiştirme'
    ]
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>Document</title>
</head>
<body>

<h5><i class="fas fa-code"></i> Class</h5>

<nav class="menu-container">
    <?php foreach ($menu_items as $item): ?>
        <a href="<?php echo $item['href']; ?>" class="menu-item bsd-navlink1">
            <div>
                <i class="<?php echo $item['icon']; ?>"></i>
                <br>
                <?php echo $item['text']; ?>
            </div>
        </a>
    <?php endforeach; ?>
</nav>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>

</body>
</html>