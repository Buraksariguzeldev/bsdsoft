   <div class="card shadow-sm mb-4">
      <div class="card-body">
         <h5 class="card-title mb-3">Sepet</h5>
         <div class="table-responsive">
            <table class="table table-striped table-hover">
               <thead class="table-light">
                  <tr>
                     <th>Ürün</th>
                     <th>Fiyat</th>
                     <th>Adet</th>
                     <th>Toplam</th>
                     <th>İşlem</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  if (isset($_SESSION['carts'][$selected_cash_register])) {
                     foreach ($_SESSION['carts'][$selected_cash_register] as $item) {
                        echo "<tr>";
                        echo "<td>{$item['name']}</td>";
                        echo "<td>{$item['price']} TL</td>";
                        echo "<td>";
                        if ($item['is_kg']) {
                           echo "{$item['weight']} KG";
                        } else {
                           echo "<div class='btn-group' role='group'>";
                           echo "<button onclick='updateProduct({$item['id']}, \"decrease\")' class='btn btn-outline-secondary btn-sm'><i class='fas fa-minus'></i></button>";
                           echo "<span>{$item['quantity']}</span>";
                           echo "<button onclick='updateProduct({$item['id']}, \"increase\")' class='btn btn-outline-secondary btn-sm'><i class='fas fa-plus'></i></button>";
                           echo "</div>";
                        }
                        echo "</td>";
                        echo "<td>" . ($item['is_kg'] ? ($item['price'] * $item['weight']) : ($item['price'] * $item['quantity'])) . " TL</td>";
                        echo "<td><button onclick='updateProduct({$item['id']}, \"remove\")' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></button></td>";
                        echo "</tr>";
                     }
                  }
                  ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
   
      <div class="payment-section mt-4">
      <div class="total-amount alert alert-info">
         <?php
         $total = 0;
         if (isset($_SESSION['carts'][$selected_cash_register])) {
            foreach ($_SESSION['carts'][$selected_cash_register] as $item) {
               if ($item['is_kg']) {
                  $total += $item['pripce'] * $item['weight'];
               } else {
                  $total += $item['price'] * $item['quantity'];
               }
            }
         }
         echo "<h3 class='mb-0'>Toplam Tutar: {$total} TL</h3>";
         ?>
      </div>
   </div>