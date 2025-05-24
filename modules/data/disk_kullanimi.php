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

    <title>Gelişmiş Disk Kullanımı</title>

</head>
<body>
<?php
// Klasör boyutunu hesaplayan fonksiyon
function klasorBoyutunuHesapla($klasor) {
    $toplamBoyut = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($klasor, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $item) {
        if ($item->isFile()) {
            $toplamBoyut += $item->getSize();
        }
    }
    return $toplamBoyut;
}

// Boyutları uygun birimlere dönüştüren fonksiyon
function formatBoyut($bayt) {
    $birimler = array('B', 'KB', 'MB', 'GB', 'TB');
    $index = 0;
    while ($bayt >= 1024 && $index < 4) {
        $bayt /= 1024;
        $index++;
    }
    return round($bayt, 2) . ' ' . $birimler[$index];
}

// Kök dizini ve izin verilen alan adlarını ayarla
$baseDirectory = $_SERVER['DOCUMENT_ROOT'];

$toplamBoyut = klasorBoyutunuHesapla($baseDirectory);

$toplamBoyutGB = round($toplamBoyut / (1024 * 1024 * 1024), 2);
$toplamBoyutMB = round($toplamBoyut / (1024 * 1024), 2);
$toplamBoyutFormatli = formatBoyut($toplamBoyut);

$toplamAlan = 5 * 1024 * 1024 * 1024; // 5GB in bytes
$kalanAlan = $toplamAlan - $toplamBoyut;
$kullanımYüzdesi = round(($toplamBoyut / $toplamAlan) * 100, 2);
?>

<div class="info-box">
    <h5><i class="fas fa-chart-pie"></i> Gelişmiş Disk Kullanımı</h5>
    
    <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: <?php echo $kullanımYüzdesi; ?>%;" aria-valuenow="<?php echo $kullanımYüzdesi; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $kullanımYüzdesi; ?>%</div>
    </div>
    
    <h6><i class="fas fa-hdd"></i> Toplam Alan</h6>
    <p>5 GB (5120 MB)</p>
    
    <h6><i class="fas fa-database"></i> Kullanılan Alan</h6>
    <p><?php echo $toplamBoyutGB; ?> GB (<?php echo $toplamBoyutMB; ?> MB)</p>
    <p><i class="fas fa-file-archive"></i> Detaylı boyut: <?php echo $toplamBoyutFormatli; ?></p>
    
    <h6><i class="fas fa-leaf"></i> Kalan Alan</h6>
    <p><?php echo formatBoyut($kalanAlan); ?> (<?php echo round($kalanAlan / (1024 * 1024), 2); ?> MB)</p>
</div>

<canvas id="diskChart" width="400" height="400"></canvas>

<script>
    var ctx = document.getElementById('diskChart').getContext('2d');
    var diskChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Kullanılan Alan', 'Boş Alan'],
            datasets: [{
                data: [
                    <?php echo $toplamBoyutMB; ?>,
                    <?php echo round(5120 - $toplamBoyutMB, 2); ?>
                ],
                backgroundColor: ['#FF6384', '#36A2EB'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#ffffff'
                    }
                },
                title: {
                    display: true,
                    text: 'Disk Kullanımı (MB)',
                    color: '#ffffff'
                }
            },
            cutout: '70%'
        }
    });
</script>

<hr>
<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
<hr>
</body>
</html>
