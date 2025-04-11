<?php
$current_page = basename($_SERVER['SCRIPT_NAME']); // Mevcut sayfanın adını al

// Sadece urun_duzenle.php için value kullan
$value = ($current_page === 'musteri_duzenle.php' && isset($customer['phone']))
? 'value="' . htmlspecialchars($customer['phone']) . '"'
: '';
?>

        <div class="bg-secondary p-3">
            <label for="phone" class="form-label"><i class="fas fa-phone"></i>
            Telefon no:</label>
            <input type="text" name="phone" id="phone" class="form-control" 
<?= $value ?> >
        </div>