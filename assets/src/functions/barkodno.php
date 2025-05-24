<?php
$current_page = basename($_SERVER['SCRIPT_NAME']); // Mevcut sayfanın adını al
$hide_submit = ($current_page === 'urun_ekle.php' || $current_page === 'urun_duzenle.php');

// Sadece urun_duzenle.php için value kullan
$value = ($current_page === 'urun_duzenle.php' && isset($product['product_name'])) 
    ? 'value="' . htmlspecialchars($product['barcode']) . '"' 
    : '';

// Required özelliğini sadece ürün ekleme/düzenleme sayfalarında ekle
$required = ($current_page === 'urun_ekle.php' || $current_page === 'urun_duzenle.php') ? 'required' : '';
?>

<!-- Diğer HTML kodları aynı kalacak -->


    <div class="bg-secondary p-3">
        <label class="fw-bold d-flex align-items-center" for="barcode">
            <i class="bi bi-upc-scan me-2"></i> 
            <span class="kaushan-script-regular">Barkod:</span>
        </label>

        <div class="input-group">
<input type="text"
       name="barcode"
       id="barcode"
       class="form-control form-control-lg"
       placeholder="Barkodu okutun veya girin..."
       autocomplete="off"
       <?= $value ?> <?= $required ?>>
       
            <!-- Kamera butonu (Sadece mobilde gösterilecek) -->
            <button type="button" id="cameraButton" class="btn btn-primary p-2" onclick="openBarcodeScanner(event)">
                <i class="bi bi-camera"></i>
            </button>

            <!-- "Git" butonu sadece ürün aramada görünecek -->
            <?php if (!$hide_submit): ?>
            <button type="submit" class="btn btn-success p-2">
                <i class="bi bi-arrow-right-circle"></i> Git
            </button>
            <?php endif; ?>
        </div>
    </div>


<script>
    // Kullanıcı bilgisayardaysa kamera butonunu gizle
    if (!/Mobi|Android|iPhone|iPad|iPod|Windows Phone/i.test(navigator.userAgent)) {
        document.getElementById('cameraButton').style.display = 'none';
    }
</script>