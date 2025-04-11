<?php
$current_page = basename($_SERVER['SCRIPT_NAME']); // Mevcut sayfanın adını al

// Sadece urun_duzenle.php için value kullan
$value = ($current_page === 'urun_duzenle.php' && isset($product['product_name'])) 
    ? 'value="' . htmlspecialchars($product['purchase_price']) . '"' 
    : '';
?>

<div class="bg-secondary mb-3 p-3">
 <label for="purchase_price" class="form-label">
 <i class="bi bi-cash-stack"></i>
   Alış Fiyatı:
 </label>
 <input type="number" name="purchase_price"
id="purchase_price" class="form-control"   <?= $value ?> required>
</div>
