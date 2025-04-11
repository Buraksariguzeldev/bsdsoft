<?php
  include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
// Veritabanı bağlantısı
try {
  include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Veritabanı Bağlantı Hatası: " . $e->getMessage());
}

// Müşterileri Listele
function getCustomers($vt, $onlyDebtors = false) {
    $params = [];
    $conditions = [];
    
    $query = "SELECT c.id, c.name,
              (SELECT SUM(total_amount) FROM customer_sales WHERE customer_id = c.id) as total_debt,
              (SELECT SUM(amount) FROM payments WHERE customer_id = c.id) as total_payments
              FROM customers c";

    if ($onlyDebtors) {
        $conditions[] = "c.id IN (SELECT customer_id FROM customer_sales GROUP BY customer_id HAVING SUM(total_amount) > 0)";
    }

    if (!empty($_POST['search_name'])) {
        $conditions[] = "c.name LIKE ?";
        $params[] = '%' . $_POST['search_name'] . '%';
    }

    if (!empty($_POST['min_debt'])) {
        $conditions[] = "(SELECT SUM(total_amount) - COALESCE((SELECT SUM(amount) FROM payments WHERE customer_id = c.id), 0) 
                         FROM customer_sales WHERE customer_id = c.id) >= ?";
        $params[] = $_POST['min_debt'];
    }

    if (!empty($_POST['max_debt'])) {
        $conditions[] = "(SELECT SUM(total_amount) - COALESCE((SELECT SUM(amount) FROM payments WHERE customer_id = c.id), 0) 
                         FROM customer_sales WHERE customer_id = c.id) <= ?";
        $params[] = $_POST['max_debt'];
    }



    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY c.name";
    $stmt = $vt->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Borcu olan müşterileri gösterme durumu
$showDebtors = isset($_POST['show_debtors']) ? true : false;

// Müşterileri listeleyelim
$customers = getCustomers($vt, $showDebtors);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Müşteriler</title>
</head>
<body>

<?php if (!$kullanici_adi): ?>
    <a href="../auth/login.php" class="btn btn-link text-decoration-none">
        İçerikleri görmek için giriş yapın
    </a>
<?php else: ?>

    <h5>
       <i class="bi bi-people"></i> Müşteriler
    </h5>

    <!-- Filtreleme Formu -->
<form method="POST"  id="filterForm">
    <div class="p-3">
            <?php
      include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/functions/musteriadi.php";
      ?>
        
        <hr>
        <div class="bg-secondary p-3">
           Min Borc
            <input type="number" name="min_debt" class="form-control"
            placeholder="Min Borç giriniz" value="<?= $_POST['min_debt'] ?? '' ?>">
        </div>
        <hr>
        <div class="bg-secondary p-3">
           Max Borç giriniz
            <input type="number" name="max_debt" class="form-control" placeholder="Max Borç" value="<?= $_POST['max_debt'] ?? '' ?>">
    </div>
    <hr>
     <?
       include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/code/button.php";

      ?>
    </div>
</form>
<hr>


    <table class="table table-striped">
        <thead>
            <tr>
                <th>İsim</th>
                <th>Toplam Satış</th>
                <th>Toplam Borç</th>
                <th>Toplam Ödeme</th>
                <th>Kalan Borç</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalAllSales = 0;
            $totalAllDebt = 0;
            $totalAllPayments = 0;
            $remainingAllDebt = 0;

            foreach ($customers as $customer):
                $stmt = $vt->prepare("SELECT COUNT(id) FROM customer_sales WHERE customer_id = ?");
                $stmt->execute([$customer['id']]);
                $totalSales = $stmt->fetchColumn();
                $totalAllSales += $totalSales;

                $stmt = $vt->prepare("SELECT SUM(total_amount) FROM customer_sales WHERE customer_id = ?");
                $stmt->execute([$customer['id']]);
                $totalDebt = $stmt->fetchColumn() ?: 0;
                $totalAllDebt += $totalDebt;

                $stmt = $vt->prepare("SELECT SUM(amount) FROM payments WHERE customer_id = ?");
                $stmt->execute([$customer['id']]);
                $totalPayments = $stmt->fetchColumn() ?: 0;
                $totalAllPayments += $totalPayments;

                $remainingDebt = $totalDebt - $totalPayments;
                $remainingAllDebt += $remainingDebt;
            ?>
                <tr>
                    <td>
                        <div class="dropdown">
                            <a href="#" class="text-primary fw-bold dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= htmlspecialchars($customer['name']) ?>
                            </a>
                            <ul class="dropdown-menu shadow">
                                <li><a class="dropdown-item" href="musteri_duzenle.php?id=<?= $customer['id'] ?>"><i class="fas fa-edit"></i> Düzenle</a></li>
                     <hr > 
                                <li><a class="dropdown-item text-danger" href="musteri_sil.php?id=<?= $customer['id'] ?>" onclick="return confirm('Bu müşteriyi silmek istediğinize emin misiniz?');"><i class="fas fa-trash-alt"></i> Sil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item"
                                href="musteri_detay.php?customer_id=<?=
                                $customer['id'] ?>"><i class="fas fa-eye"></i>
                                Detay</a></li> <hr>
                                <li><a class="dropdown-item" href="musteri_ozet.php?id=<?= $customer['id'] ?>"><i class="fas fa-file-alt"></i> Özet</a></li>
                            </ul>
                        </div>
                    </td>
                    <td class="text-center"><?= $totalSales ?></td>
                    <td class="text-end"><?= number_format($totalDebt, 2) ?> TL</td>
                    <td class="text-end"><?= number_format($totalPayments, 2) ?> TL</td>
                    <td class="text-end fw-bold <?= ($remainingDebt > 0) ? 'text-danger' : 'text-success' ?>">
                        <?= number_format($remainingDebt, 2) ?> TL
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr class="table-dark fw-bold">
                <td>GENEL TOPLAM</td>
                <td class="text-center"><?= $totalAllSales ?></td>
                <td class="text-end"><?= number_format($totalAllDebt, 2) ?> TL</td>
                <td class="text-end"><?= number_format($totalAllPayments, 2) ?> TL</td>
                <td class="text-end <?= ($remainingAllDebt > 0) ? 'text-danger' : 'text-success' ?>">
                    <?= number_format($remainingAllDebt, 2) ?> TL
                </td>
            </tr>
        </tbody>
    </table>

<script>
    // Form gönderildiğinde URL'yi temizle
    document.getElementById("filterForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Formun normal şekilde gönderilmesini engelle
        let formData = new FormData(this);
        
        fetch(window.location.pathname, {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            document.body.innerHTML = html; // Sayfanın içeriğini güncelle
            window.history.pushState({}, "", window.location.pathname); // URL'deki POST verilerini temizle
        })
        .catch(error => console.error("Hata:", error));
    });

    // Sayfa yenilenirken veri kaybı olmasın
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            window.location.reload(); // Tarayıcı önbelleğinden geliyorsa yenile
        }
    });
</script>
<?php endif; ?>


</body>
</html>