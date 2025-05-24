<?php
$current_page = basename($_SERVER['SCRIPT_NAME']); // Mevcut sayfanın adını al

// Sadece urun_duzenle.php için value kullan
$value = ($current_page === 'urun_duzenle.php' && isset($product['product_name'])) 
    ? 'value="' . htmlspecialchars($product['profit_margin']) . '"' 
    : '';
?>

<div class="bg-secondary mb-3 p-3">
 <label for="profit_margin" class="form-label"><i class="bi bi-graph-up-arrow"></i>Kar Oranı:
 </label>
  <input type="text" name="profit_margin" id="profit_margin" class="form-control"  <?= $value ?> readonly>
</div>

