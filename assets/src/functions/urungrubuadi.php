<?php
$current_page = basename($_SERVER['SCRIPT_NAME']); // Mevcut sayfanın adını al

// Sadece urun_duzenle.php için value kullan
$value = ($current_page === 'urun_grubu_duzenle.php' &&
   isset($group['group_name']))
? 'value="' . htmlspecialchars($group['group_name']) . '"'
: '';
?>

<div class="bg-secondary p-3 mb-3">
   <label for="group_name" class="form-label">
      <i class="fas fa-tag me-2"></i> Ürün Grubu Adı:
   </label>
   <input type="text" name="group_name" id="group_name" class="form-control"
   <?= $value ?> required>
</div>