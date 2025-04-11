            <div class="payment-left mt-4">
               <div class="form-group">
                  <label for="customer_id">Müşteri Seç:</label>
                  <div class="input-group">
                     <!-- Müşteri Seçim Dropdown -->
                     <select name="customer_id" id="customer_id" class="form-control">
                        <option value="">Müşterisiz Satış</option>
                        <?php
                        $stmt = $vt->query("SELECT id, name, phone FROM customers ORDER BY name");
                        $selected_customer = $_SESSION['selected_customer'] ?? '';
                        while ($customer = $stmt->fetch(PDO::FETCH_ASSOC)) {
                           $selected = ($selected_customer == $customer['id']) ? 'selected' : '';
                           echo "<option value='{$customer['id']}' {$selected}>{$customer['name']} - {$customer['phone']}</option>";
                        }
                        ?>
                     </select>

                     <!-- Yeni Müşteri Butonu -->
                     <div class="input-group-append">
                        <button type="button" onclick="openNewCustomerForm()" class="btn btn-outline-secondary">
                           <i class="fas fa-plus"></i> Yeni Müşteri
                        </button>
                     </div>
                  </div>
               </div>
            </div>

