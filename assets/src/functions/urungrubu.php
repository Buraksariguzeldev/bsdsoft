<div class="bg-secondary mb-3 p-3">
 <label for="product_group" class="form-label"> 
 <i class="bi bi-grid"></i> Ürün Grubu:
 </label>
  <select name="product_group" id="product_group" class="form-select" required>
   <option value="">Lütfen bir ürün grubu seçin
   </option>
    <?php foreach ($product_groups as $group): ?>
   <option value="<?= $group['id'] ?>" <?= (!empty($product['product_group']) && $product['product_group'] == $group['id']) ? 'selected' : '' ?>>
    <?= htmlspecialchars($group['group_name']) ?>
   </option>
   <?php endforeach; ?>
  </select>
 </div>
</div>