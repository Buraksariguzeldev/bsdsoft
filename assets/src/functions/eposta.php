<?php
$current_page = basename($_SERVER['SCRIPT_NAME']); // Mevcut sayfanın adını al

// Sadece urun_duzenle.php için value kullan
$value = ($current_page === 'musteri_duzenle.php' && isset($customer['email']))
? 'value="' . htmlspecialchars($customer['email']) . '"'
: '';
?>


        <div class="bg-secondary p-3">
            <label for="email" class="form-label"><i class="fas fa-envelope"></i> E-posta</label>
            <input type="email" name="email" id="email" class="form-control" 
<?= $value ?>  >
        </div>