<div class="bg-secondary p-3 mb-3">
  <i class="bi bi-building"></i>
  <label for="brand_id" class="form-label">Marka:</label>
  <select name="brand_id" id="brand_id" class="form-select" required>
    <option value="">Lütfen bir marka seçin</option>
    <?php foreach ($brands as $brand): ?>
    <option value="<?= $brand['id'] ?>" <?= (!empty($product['brand_id']) && $product['brand_id'] == $brand['id']) ? 'selected' : '' ?>>
      <?= htmlspecialchars($brand['brand_name']) ?>
    </option>
    <?php endforeach; ?>
  </select>
</div>