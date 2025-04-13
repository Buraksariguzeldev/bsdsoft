<?php

include 'satis_tag.php';

// Oturumdaki kasa bilgisini al
$secilen_kasa_id = isset($_SESSION['secilen_kasa_id']) ? $_SESSION['secilen_kasa_id'] : null;
$secilen_kasa_adi = "Kasa Seçilmedi"; // Varsayılan değer

// Veritabanından kasa adını al
if ($secilen_kasa_id) {
    try {
        $stmt = $vt->prepare("SELECT register_name FROM cash_registers WHERE id = ?");
        $stmt->execute([$secilen_kasa_id]);
        $kasa = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($kasa) {
            $secilen_kasa_adi = htmlspecialchars($kasa['register_name']);
        }
    } catch (PDOException $e) {
        error_log("Kasa adı alınırken hata: " . $e->getMessage());
    }
}

// Kasa seçimi form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kasa_id'])) {
    $secilen_kasa_id = intval($_POST['kasa_id']);

    // Seçilen kasanın geçerli olup olmadığını kontrol et (isteğe bağlı)
    try {
        $stmt = $vt->prepare("SELECT id FROM cash_registers WHERE id = ?");
        $stmt->execute([$secilen_kasa_id]);
        $kasa_var_mi = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$kasa_var_mi) {
            echo '<script>alert("Geçersiz kasa seçimi!");</script>';
            $secilen_kasa_id = null; // Geçersizse oturumdan kaldır
        }
    } catch (PDOException $e) {
        error_log("Kasa kontrol edilirken hata: " . $e->getMessage());
        echo '<script>alert("Kasa seçimi kontrol edilirken bir hata oluştu!");</script>';
    }

    if ($secilen_kasa_id) {
        $_SESSION['secilen_kasa_id'] = $secilen_kasa_id;
        // Sayfayı yenile
        echo '<script>window.location.reload();</script>';
        exit; // Yönlendirmeden sonra betiği durdur
    }
}

?>
<style>
    .small-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }

    #cash-register-info {
        display: flex;
        flex-direction: column;
    }

    .kasa-btn-group {
        display: flex;
        gap: 10px;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }

    .kasa-btn-group .btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    /* Kasa Seçimi için Stil */
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
        margin-bottom: 0.5rem;
        /* Butonlar arası boşluk */
    }

    /* Modal stilleri */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }
</style>
<div id="cash-register-info" class="container mt-3">
    <div id="kasa-buttons" class="kasa-btn-group">
        <span>Kasa: <?php echo $secilen_kasa_adi; ?></span>
        <button type="button" class="btn btn-primary btn-sm" onclick="openKasaModal()">Kasa Değiştir</button>
    </div>
</div>

<!DOCTYPE html>
<html lang="tr">

<head>
    <title>Kasa Sistemi</title>
</head>

<body>

    <?php if (!$kullanici_adi): ?> <a href="../auth/login.php" class="btn btn-link text-decoration-none">
            İçerikleri görmek için giriş yapın
        </a>

        <?php else : ?>


            <!-- Müşteri Seçimi -->
            <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/code/musterisec.php"; ?>

            <hr>

            <!-- Hızlı Satış -->
            <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/code/hizlisatis.php"; ?>

            <hr>

            <form id="barcode-form" method="POST">
                <!-- Barkod Okuyucu -->
                <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/functions/barkodno.php"; ?>

            </form>

            <hr>

            <form id="name-search-form" method="POST">
                <!-- Ürün Adı ile Arama -->
                <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/functions/urunadi.php"; ?>
            </form>

            <hr>

            <!-- Sepet tablosunu değiştirin -->
            <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/code/sepet.php"; ?>

            <hr>

            <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/code/tutar.php"; ?>

            <hr>

            <?php

            // Sepeti al
            $sepet = isset($_SESSION['sepet']) ? $_SESSION['sepet'] : [];
            $toplam_tutar = 0;

            // Sepet boşsa veya geçerli değilse
            if (empty($sepet) || !is_array($sepet))) {
                echo '<p class="alert alert-warning">Sepetiniz boş. Lütfen ürün ekleyin.</p>';
            } else {
                // Toplam tutarı hesapla
                foreach ($sepet as $urun_id => $urun_detay) {
                    if (isset($urun_detay['fiyat']) && isset($urun_detay['miktar'])) {
                        $toplam_tutar += $urun_detay['fiyat'] * $urun_detay['miktar'];
                    }
                }


            }

            ?>

            <div class="payment-form border p-3">
                <form id="payment-form" method="POST" action="satisi_tamamla.php">
                    <fieldset id="complete-sale-btn" class="btn-group w-100 mb-3" role="group">
                        <button type="submit" name="payment_type" value="cash" class="btn btn-success small-btn" <?php echo $total <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-money-bill-wave"></i> Nakit Ödeme
                        </button>
                        <button type="submit" name="payment_type" value="card" class="btn btn-info small-btn" <?php echo $total <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-credit-card"></i> Kredi Kartı
                        </button>
                    </fieldset>

                    <!-- Müşteriye Satış Yap Butonu -->
                    <button type="button" id="customer-sale-btn" class="btn btn-primary w-100 mt-3" onclick="window.location.href='musteri_satisi.php'">
                        <i class="fas fa-user"></i> Müşteriye Satış Yap
                    </button>
                </form>
            </div>

    </div>

    <!-- Kasa Seçimi Modal -->
    <div id="kasaModal" class="modal">
        <div class="modal-content">
            <span onclick="closeKasaModal()" style="float: right; cursor: pointer;">&times;</span>
            <h3>Kasa Seçimi</h3>
            <form method="post" action="">
                <?php
                // Kasaları veritabanından çek
                $kasalar = [];
                try {
                    $stmt = $vt->query("SELECT id, register_name FROM cash_registers WHERE status = 1 ORDER BY register_name");
                    $kasalar = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    error_log("Kasa seçimi - Kasa listesi alınamadı: " . $e->getMessage());
                    echo '<p class="alert alert-danger">Kasalar yüklenirken bir hata oluştu.</p>';
                }

                if (!empty($kasalar)) {
                    foreach ($kasalar as $kasa) {
                        echo '<button type="submit" name="kasa_id" value="' . htmlspecialchars($kasa['id']) . '" class="btn btn-outline-primary btn-lg">' . htmlspecialchars($kasa['register_name']) . '</button><br>';
                    }
                } else {
                    echo '<p class="alert alert-warning">Aktif kasa bulunamadı. Lütfen sistem yöneticinizle iletişime geçin.</p>';
                }
                ?>
            </form>
        </div>
    </div>

    <script>
        function openKasaModal() {
            document.getElementById("kasaModal").style.display = "block";
        }

        function closeKasaModal() {
            document.getElementById("kasaModal").style.display = "none";
        }

       // Kasa seçimi yapıldığında sayfayı yenile
        document.querySelector('#kasaModal form').addEventListener('submit', function(event) {
            // Formun varsayılan davranışını engelle
            event.preventDefault();

            // Form verilerini al
            const formData = new FormData(this);

            // AJAX isteği gönder
           fetch('', { // Aynı sayfaya POST isteği gönder
                method: 'POST',
                body: formData
            }).then(response => {
                // Sayfayı yenile
                window.location.reload();
            }).catch(error => {
                console.error('Hata:', error);
                alert('Kasa seçimi sırasında bir hata oluştu.');
            });
        });
    </script>

    <?php
    include 'satis_script.php';
    include 'kasa_bilgisi_getir.php';
    ?>

    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
    <script src="assets/src/js/klavye.js"></script>

    <?php endif; ?>
</body>

</html>