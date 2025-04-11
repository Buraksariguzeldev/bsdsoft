

   <div class=" row g-3 ">
      <?php
      $quickProducts = getQuickProducts();
      foreach ($quickProducts as $product) {
         $imagePath = $product['image_path'] ?? 'default.jpg'; // Varsayılan resim yolu
         echo "<div class='col-6 col-md-4 col-lg-3'>
                <button type='button' class='quick-product-btn btn btn-outline-primary w-100' onclick='addQuickProduct(\"{$product['barcode']}\")'>
                    <img src='{$imagePath}' alt='{$product['product_name']}' class='product-image img-fluid rounded mb-2'>
                    <span class='product-name d-block text-center'>{$product['product_name']}</span>
                    <span class='product-price d-block text-center'>{$product['sale_price']} TL</span>
                </button>
              </div>";
      }
      ?>
   </div>

