<div class="bg-secondary p-3 mb-3">
  <i class="bi bi-box-seam"></i>
  <label for="unit" class="form-label">
      Ürün Birimi:
   </label>

  <select name="unit" id="unit" class="form-select" required>
    <option value="">Lütfen birim seçin</option>
    <option value="adet" <?= (!empty($product['unit']) && $product['unit'] === 'adet') ? 'selected' : '' ?>>Adet</option>
    <option value="kg" <?= (!empty($product['unit']) && $product['unit'] === 'kg') ? 'selected' : '' ?>>Kg</option>
    <option value="lt" <?= (!empty($product['unit']) && $product['unit'] === 'lt') ? 'selected' : '' ?>>Litre</option>
  </select>
</div>