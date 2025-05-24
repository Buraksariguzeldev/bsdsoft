<?php
$current_page = basename($_SERVER['SCRIPT_NAME']); // Mevcut sayfanın adını al

// Value belirleme
$input_value = ($current_page === 'musteri_duzenle.php' && isset($customer['name'])) 
    ? htmlspecialchars($customer['name']) 
    : htmlspecialchars($_POST['search_name'] ?? '');

// Eğer müşteri listesi sayfasındaysak required kaldır
$required_attr = ($current_page === 'musteri_duzenle.php') ? 'required' : '';
?>

<div class="bg-secondary p-3">
    <label for="name" class="form-label"><i class="fas fa-user"></i> Müşteri Adı</label>
    <input type="text" name="<?= ($current_page === 'musteriler.php') ? 'search_name' : 'name' ?>" 
           id="name" class="form-control" 
           placeholder="Müşteri Adı giriniz" 
           value="<?= $input_value ?>" <?= $required_attr ?>>
</div>