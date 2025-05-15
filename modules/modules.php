<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

// Menü öğeleri dizisi
$menu_items = [
    [
        'href' => 'data/data.php',
        'icon' => 'fas fa-database',
        'text' => 'Veri İşlemleri'
    ],
    [
        'href' => 'custom/custom.php',
        'icon' => 'fas fa-cogs',
        'text' => 'Özelleştirme'
    ],
    [
        'href' => 'class/class.php',
        'icon' => 'fas fa-chalkboard-teacher',
        'text' => 'Sınıf Yönetimi'
    ],
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>Modules</title>
</head>
<body>

<h5><i class="fas fa-cogs"></i> Modules</h5>

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