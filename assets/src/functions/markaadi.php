<?php
$current_page = basename($_SERVER['SCRIPT_NAME']); // Mevcut sayfanın adını al

// Sadece urun_duzenle.php için value kullan
$value = ($current_page === 'marka_duzenle.php' && isset($brand['brand_name']))
? 'value="' . htmlspecialchars($brand['brand_name']) . '"'
: '';
?>

<div class="bg-secondary mb-3 p-3">
   <label for="brand_name" class="form-label"><i class="fas fa-tag"></i>
      Marka Adı:</label>
   <input type="text" name="brand_name" id="brand_name" class="form-control"
   <?= $value ?> required>
</div>


