<?php
// Mevcut müşteri ID'sini al
$selected_customer = isset($_SESSION['selected_customer']) ? $_SESSION['selected_customer'] : '';

?>
<div class="payment-left mt-4">
    <div class="form-group">
        <label for="customer_search">Müşteri Seç:</label>
        <div class="input-group">
            <!-- Arama Alanı -->
            <input type="text" id="customer_search" class="form-control" placeholder="Müşteri Ara...">
            <div class="input-group-append">
                <button type="button" onclick="openNewCustomerForm()" class="btn btn-outline-secondary">
                    <i class="fas fa-plus"></i> Yeni Müşteri
                </button>
            </div>
        </div>
    </div>

    <!-- Müşteri Listesi -->
    <div class="form-group mt-2">
        <select name="customer_id" id="customer_id" class="form-control">
            <option value="">Müşterisiz Satış</option>
            <?php
            try {
                $stmt = $vt->prepare("SELECT id, name, phone, address FROM customers ORDER BY name");
                $stmt->execute();
                $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($customers as $customer) {
                    $selected = ($selected_customer == $customer['id']) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($customer['id']) . '" data-name="' . htmlspecialchars($customer['name']) . '" data-phone="' . htmlspecialchars($customer['phone']) . '" ' . $selected . '>';
                    echo htmlspecialchars($customer['name']) . ' - ' . htmlspecialchars($customer['phone']) . '</option>';
                }
            } catch (PDOException $e) {
                echo "<option value=''>Müşteri listesi yüklenirken hata oluştu.</option>";
                error_log("Müşteri Seçimi - Müşteri listesi alınamadı: " . $e->getMessage());
            }
            ?>
        </select>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSearch = document.getElementById('customer_search');
    const customerSelect = document.getElementById('customer_id');

    customerSearch.addEventListener('input', function() {
        const searchTerm = customerSearch.value.toLowerCase();
        const options = customerSelect.options;

        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            const name = option.dataset.name.toLowerCase();
            const phone = option.dataset.phone.toLowerCase();

            if (name.includes(searchTerm) || phone.includes(searchTerm) || searchTerm === '') {
                option.style.display = ''; // Göster
            } else {
                option.style.display = 'none'; // Gizle
            }
        }
    });
});
</script>