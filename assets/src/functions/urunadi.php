<?php
$current_page = basename($_SERVER['SCRIPT_NAME']); // Mevcut sayfanın adını al
$hide_submit = ($current_page === 'urun_ekle.php' || $current_page === 'urun_duzenle.php');

// Sadece urun_duzenle.php için value kullan
$value = ($current_page === 'urun_duzenle.php' && isset($product['product_name'])) 
    ? 'value="' . htmlspecialchars($product['product_name']) . '"' 
    : '';
?>


    <div class="bg-secondary p-3">
        <label class="form-label fw-bold" for="product_name">
            <i class="bi bi-tag"></i> 
            <span class="kaushan-script-regular"> Ürün Adı:</span>
        </label>

     <?php if ($current_page === 'urun_listesi.php' || 
          $current_page === 'hizli_fiyat_guncelle.php' || 
          $current_page === 'satis_paneli.php'): ?>
            <!-- Ürün listesi veya hızlı fiyat güncelleme için arama alanı -->
            <div class="input-group">
                <input type="text"
                       name="product_name"
                       id="product_name"
                       class="form-control form-control-lg"
                       placeholder="Ürün adını yazın..."
                       autocomplete="off">

                <?php if (!$hide_submit): ?>
                <button type="submit" class="btn btn-success p-2">
                    <i class="bi bi-arrow-right-circle"></i> Git
                </button>
                <?php endif; ?>
            </div>
            <div id="search-results" class="position-absolute bg-white border rounded shadow-sm w-100 mt-1" 
                 style="display:none; z-index:1000;"></div>

        <?php elseif ($current_page === 'urun_duzenle.php' || $current_page === 'urun_ekle.php'): ?>
            <!-- Ürün düzenleme veya ekleme sayfaları için ürün adı girişi -->
            <input type="text" 
                   name="product_name" 
                   id="product_name" 
                   class="form-control form-control-lg" 
                   placeholder="Ürün adı giriniz..." 
                   <?= $value ?> >
        <?php endif; ?>
    </div>