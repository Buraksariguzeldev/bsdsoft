<?php
$current_page = basename($_SERVER['SCRIPT_NAME']); // Mevcut sayfanın adını al

// Sadece urun_duzenle.php için value kullan
$value = ($current_page === 'urun_duzenle.php' && isset($product['product_name']))
? 'value="' . htmlspecialchars($product['sale_price']) . '"'
: '';
?>

<div class="bg-secondary mb-3 p-3">
  <i class="bi bi-cash-coin"></i>
  <label for="sale_price" class="form-label">
    Satis Fiyati;
  </label>
  <input type="number" name="sale_price"
  id="sale_price" class="form-control"   <?= $value ?> required>
</div>