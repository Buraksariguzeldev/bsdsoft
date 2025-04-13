<?php
session_start();

// Gerekli dosyaları dahil et
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php'); // PDO bağlantısı
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/giriskontrol.php'); // Giriş yapmış mı kontrolü
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/header.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

// Sepeti al
$sepet = isset($_SESSION['sepet']) ? $_SESSION['sepet'] : [];
$toplam_tutar = 0;

// Sepet boşsa veya geçerli değilse satış paneline geri dön
if (empty($sepet) || !is_array($sepet)) {
    header('Location: satis_paneli.php');
    exit;
}

// Toplam tutarı hesapla
foreach ($sepet as $urun_id => $urun_detay) {
    if (isset($urun_detay['fiyat']) && isset($urun_detay['miktar'])) {
        $toplam_tutar += $urun_detay['fiyat'] * $urun_detay['miktar'];
    }
}

// Kasaları veritabanından çek
$kasalar = [];
try {
    $stmt = $vt->query("SELECT id, register_name FROM cash_registers WHERE status = 1 ORDER BY register_name"); // Sadece aktif kasaları al
    $kasalar = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Kasa seçimi - Kasa listesi alınamadı: " . $e->getMessage());
    // Hata durumunda kullanıcıya bilgi verilebilir.
}

// Kasa seçimi form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kasa_id'])) {
    $secilen_kasa_id = intval($_POST['kasa_id']);

    // Seçilen kasanın geçerli olup olmadığını kontrol et
    $kasa_gecerli = false;
    foreach ($kasalar as $kasa) {
        if ($kasa['id'] == $secilen_kasa_id) {
            $kasa_gecerli = true;
            break;
        }
    }

    if ($kasa_gecerli) {
        $_SESSION['secilen_kasa_id'] = $secilen_kasa_id;
        // Kasa seçildikten sonra satışı tamamlama sayfasına yönlendir
        header('Location: satisi_tamamla.php');
        exit;
    } else {
        $hata_mesaji = "Geçersiz kasa seçimi.";
    }
}

// Veritabanı bağlantısını kapat
$vt = null;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satış Onayı ve Kasa Seçimi</title>
    <!-- Gerekli CSS dosyaları (main.css, bootstrap vb.) header.php içinde olmalı -->
    <style>
        .sepet-ozeti {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .sepet-ozeti h4 {
            border-bottom: 1px solid #ced4da;
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
        }
        .sepet-listesi {
            max-height: 250px;
            overflow-y: auto;
            margin-bottom: 1rem;
        }
        .sepet-listesi .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .toplam-tutar {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: right;
            margin-top: 1rem;
            border-top: 1px solid #ced4da;
            padding-top: 0.75rem;
        }
        .kasa-secimi-formu .btn {
            margin-bottom: 0.5rem; /* Butonlar arası boşluk */
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            <div class="sepet-ozeti">
                <h4><i class="fas fa-shopping-basket me-2"></i>Sepet Özeti</h4>
                <div class="sepet-listesi">
                    <?php if (!empty($sepet)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($sepet as $urun_id => $urun): ?>
                                <li class="list-group-item">
                                    <span>
                                        <?php echo htmlspecialchars($urun['isim'] ?? 'Bilinmeyen Ürün'); ?>
                                        (<?php echo htmlspecialchars($urun['miktar'] ?? 0); ?> Adet)
                                    </span>
                                    <span>
                                        <?php echo number_format(($urun['fiyat'] ?? 0) * ($urun['miktar'] ?? 0), 2, ',', '.'); ?> TL
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-center text-muted">Sepetiniz boş.</p>
                    <?php endif; ?>
                </div>
                <div class="toplam-tutar">
                    Toplam: <?php echo number_format($toplam_tutar, 2, ',', '.'); ?> TL
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header text-center bg-light">
                    <h3 class="mb-0 h4"><i class="fas fa-cash-register me-2"></i>Kasa Seçimi</h3>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($hata_mesaji)): ?>
                        <div class="alert alert-danger text-center"><?php echo $hata_mesaji; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($kasalar)): ?>
                        <p class="text-center text-muted mb-3">Lütfen satış işlemini tamamlamak için bir kasa seçin.</p>
                        <form method="post" action="" class="kasa-secimi-formu">
                            <div class="d-grid gap-2">
                                <?php foreach ($kasalar as $kasa): ?>
                                    <button type="submit" name="kasa_id" value="<?php echo htmlspecialchars($kasa['id']); ?>" class="btn btn-outline-primary btn-lg">
                                        <i class="fas fa-desktop me-2"></i><?php echo htmlspecialchars($kasa['register_name']); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">Aktif kasa bulunamadı. Lütfen sistem yöneticinizle iletişime geçin.</div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                    <a href="satis_paneli.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Satış Paneline Geri Dön</a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/footer.php'); ?>
</body>
</html>
