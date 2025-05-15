<?php
$current_page = basename($_SERVER['SCRIPT_NAME']); // Mevcut sayfanın adını al

// Sadece urun_duzenle.php için value kullan
$value = ($current_page === 'kasa_duzenle.php' &&
   isset($cash_register['register_name']))
? 'value="' . htmlspecialchars($cash_register['register_name']) . '"'
: '';
?>



<div class="bg-secondary mb-3 p-3"
   <label for="register_name" class="form-label">
   <i class="fas fa-cash-register"></i> Kasa Adı :</label>
<input type="text" id="register_name" name="register_name"
class="form-control"   <?= $value ?> placeholder="Kasa Adı Giriniz"
required>
</div>