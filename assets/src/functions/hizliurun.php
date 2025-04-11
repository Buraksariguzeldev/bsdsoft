



<div class="bg-secondary p-3">
  <div class="mb-3 d-flex align-items-center">
    <i class="bi bi-lightning-charge fs-5 me-2"></i>
    <label for="hizli_urun" class="form-label me-2 mb-0">Hızlı Ürün:</label>
<?php
// Eğer $product değişkeni tanımlı değilse boş bir dizi olarak ayarla
$product = $product ?? [];
?>

<input type="checkbox" name="hizli_urun" id="hizli_urun" class="form-check-input m-0" value="1" 
<?= (isset($product['hizli_urun']) && $product['hizli_urun'] == 1) ? 'checked' :
'' ?>>
  </div>
</div>