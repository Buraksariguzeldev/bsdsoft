<?php
session_start();

// Veri tabanı bağlantısı
try {
    include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
} catch (PDOException $e) {
    die("Veri tabanına bağlanılamadı: " . $e->getMessage());
}

// Filtreleme değişkenleri
$filters = [];
$sql = "SELECT p.*, pg.group_name, b.brand_name  
        FROM products p  
        LEFT JOIN product_groups pg ON p.product_group = pg.id  
        LEFT JOIN brands b ON p.brand_id = b.id  
        WHERE 1=1";

if (!empty($_GET['product_name'])) {
    $sql .= " AND p.product_name LIKE :product_name";
    $filters[':product_name'] = '%' . $_GET['product_name'] . '%';
}

if (!empty($_GET['product_group'])) {
    $sql .= " AND p.product_group = :product_group";
    $filters[':product_group'] = $_GET['product_group'];
}

if (!empty($_GET['brand_id'])) {
    $sql .= " AND p.brand_id = :brand_id";
    $filters[':brand_id'] = $_GET['brand_id'];
}

if (!empty($_GET['barcode'])) {
    $sql .= " AND p.barcode LIKE :barcode";
    $filters[':barcode'] = '%' . $_GET['barcode'] . '%';
}

if (isset($_GET['hizli_urun']) && $_GET['hizli_urun'] !== '') {
    $sql .= " AND p.hizli_urun = :hizli_urun";
    $filters[':hizli_urun'] = $_GET['hizli_urun'];
}

if (!empty($_GET['min_price'])) {
    $sql .= " AND p.sale_price >= :min_price";
    $filters[':min_price'] = $_GET['min_price'];
}

if (!empty($_GET['max_price'])) {
    $sql .= " AND p.sale_price <= :max_price";
    $filters[':max_price'] = $_GET['max_price'];
}

// Sıralama seçenekleri
if (isset($_GET['order_by'])) {
    switch ($_GET['order_by']) {
        case 'name_asc':
            $sql .= " ORDER BY p.product_name ASC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY p.product_name DESC";
            break;
        case 'barcode':
            $sql .= " ORDER BY p.barcode ASC";
            break;
        default:
            break;
    }
}

$stmt = $vt->prepare($sql);
$stmt->execute($filters);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ürün grupları ve markaları filtre için al  
$groups = $vt->query("SELECT * FROM product_groups")->fetchAll(PDO::FETCH_ASSOC);
$brands = $vt->query("SELECT * FROM brands")->fetchAll(PDO::FETCH_ASSOC);

// Fiyat Güncelleme İşlemi bölümünde bu kısmı değiştir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPrice = $_POST['new_price'] ?? null;
    $updateType = $_POST['update_type'] ?? null; // Artır/Azalt seçeneği
    $applyFilters = $_POST['apply_filters'] ?? [];

    if (!empty($newPrice) && is_numeric($newPrice)) {
        $updateQuery = "UPDATE products SET ";
        $updateParams = [];
        $hasFilter = false;

        // Fiyat artırma veya azaltma işlemi
        if ($updateType === 'increase') {
            $updateQuery .= "sale_price = sale_price + :new_price";
        } elseif ($updateType === 'decrease') {
            $updateQuery .= "sale_price = sale_price - :new_price";
        } else {
// Şu satırı:
header('Location: hizli_fiyat_guncelle.php?error=2'); // Hatalı işlem tipi
exit;

// Şu şekilde değiştirin:
$currentParams = $_GET;
$currentParams['error'] = 2;
unset($currentParams['success']); // Varsa eski başarı mesajını temizle
$redirectUrl = 'hizli_fiyat_guncelle.php?' . http_build_query($currentParams);
header('Location: ' . $redirectUrl);
exit;


// Şu şekilde değiştirin:
$currentParams = $_GET;
$currentParams['error'] = 2;
unset($currentParams['success']); // Varsa eski başarı mesajını temizle
$redirectUrl = 'hizli_fiyat_guncelle.php?' . http_build_query($currentParams);
header('Location: ' . $redirectUrl);
exit;

        }

        $updateParams[':new_price'] = $newPrice;

        // Seçilen filtrelere göre fiyat değişikliği
        if (in_array('product_group', $applyFilters) && !empty($_GET['product_group'])) {
            $updateQuery .= " WHERE product_group = :product_group";
            $updateParams[':product_group'] = $_GET['product_group'];
            $hasFilter = true;
        }

        if (in_array('brand_id', $applyFilters) && !empty($_GET['brand_id'])) {
            $updateQuery .= $hasFilter ? " AND" : " WHERE";
            $updateQuery .= " brand_id = :brand_id";
            $updateParams[':brand_id'] = $_GET['brand_id'];
            $hasFilter = true;
        }

        if (in_array('price_range', $applyFilters)) {
            if (!empty($_GET['min_price'])) {
                $updateQuery .= $hasFilter ? " AND" : " WHERE";
                $updateQuery .= " sale_price >= :min_price";
                $updateParams[':min_price'] = $_GET['min_price'];
                $hasFilter = true;
            }
            if (!empty($_GET['max_price'])) {
                $updateQuery .= $hasFilter ? " AND" : " WHERE";
                $updateQuery .= " sale_price <= :max_price";
                $updateParams[':max_price'] = $_GET['max_price'];
                $hasFilter = true;
            }
        }

        if (in_array('barcode', $applyFilters) && !empty($_GET['barcode'])) {
            $updateQuery .= $hasFilter ? " AND" : " WHERE";
            $updateQuery .= " barcode LIKE :barcode";
            $updateParams[':barcode'] = '%' . $_GET['barcode'] . '%';
            $hasFilter = true;
        }
        
        if (in_array('search', $applyFilters) && !empty($_GET['product_name'])) {
            $updateQuery .= $hasFilter ? " AND" : " WHERE";
            $updateQuery .= " product_name LIKE :product_name";
            $updateParams[':product_name'] = '%' . $_GET['product_name'] . '%';
            $hasFilter = true;
        }

        // Eğer hiçbir filtre seçilmediyse bile işlem yapalım
        if (!$hasFilter) {
            // Tüm ürünlere uygula
            // Filtre hatasını kaldırıyoruz
        }

        $stmt = $vt->prepare($updateQuery);
        $stmt->execute($updateParams);


$currentParams = $_GET;
$currentParams['success'] = 1;
unset($currentParams['error']); // Varsa eski hata mesajını temizle
$redirectUrl = 'hizli_fiyat_guncelle.php?' . http_build_query($currentParams);
header('Location: ' . $redirectUrl);
exit;

    }
}



include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
?>
<!DOCTYPE html>
<html lang="tr">

<head>

    <title>Hızlı Fiyat Değişikliği</title>
 
</head>

<body>

      
      
             <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>
 
 
        <h5>
           <i class="bi bi-cash"></i>
           Hızlı Fiyat Değişikliği
        </h5>

<form method="get" action="">
   
            <?php
include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/functions/urunadi.php";

echo('<hr>');


include $_SERVER["DOCUMENT_ROOT"] .
"/assets/src/functions/barkodno.php" ; ?> 
<br>
            <div>
                <div class="bg-secondary  p-3 ">
                    <label for="product_group" class="form-label">Ürün Grubu:</label>
<select name="product_group" id="product_group" class="form-select">
    <option value="">Tümü</option>
    <?php
    foreach ($groups as $group) {
        $selected = (isset($_GET['product_group']) && $_GET['product_group'] == $group['id']) ? 'selected' : '';
        echo "<option value='{$group['id']}' {$selected}>{$group['group_name']}</option>";
    }
    ?>
</select>

                </div>
<br>
                <div class="bg-secondary mb-3 p-3">
                    <label for="brand_id" class="form-label">Marka:</label>
<select name="brand_id" id="brand_id" class="form-select">
    <option value="">Tümü</option>
    <?php
    foreach ($brands as $brand) {
        $selected = (isset($_GET['brand_id']) && $_GET['brand_id'] == $brand['id']) ? 'selected' : '';
        echo "<option value='{$brand['id']}' {$selected}>{$brand['brand_name']}</option>";
    }
    ?>
</select>

                </div>

                <div class="bg-secondary mb-3 p-3">
                    <label for="min_price" class="form-label">Min Fiyat:</label>
<input type="number" name="min_price" id="min_price" class="form-control"
value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">

                </div>


                <div class="bg-secondary mb-3 p-3">
                    <label for="max_price" class="form-label">Max Fiyat:</label>
<input type="number" name="max_price" id="max_price" class="form-control"
value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                </div>
            </div>


    <?php
    // Filtre butonu için
    $_SERVER['REQUEST_URI'] = "/update/hizli_fiyat_guncelle.php?action=filter";
    include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/code/button.php";
    ?>


        </form>

        <h5>
           <i class="bi bi-cash-stack"></i>
           Toplu Fiyat Güncelle</h5>
      <form method="post" action="">
         
            <div class="bg-secondary p-3">
                <label for="update_type" class="form-label">
                  <i class="bi bi-currency-exchange"></i> 
                   Fiyat İşlemi:</label>
                <select name="update_type" id="update_type" class="form-select" required>
                    <option value="increase">Artır</option>
                    <option value="decrease">Azalt</option>
                </select>
            </div>
<hr>
            <div class="bg-secondary p-3">
                <label for="new_price" class="form-label">
                   <i class="bi bi-arrow-left-right"></i>
                   Değişim Miktarı (TL):</label>
                <input type="number" step="0.01" name="new_price" id="new_price" class="form-control" required>
            </div>
<hr>

<div class="update-filters bg-secondary p-3 rounded">
    <p class="mb-2"><i class="bi bi-funnel-fill"></i> Fiyat güncellemesi uygulanacak alanları seçin:</p>
    
    <label class="form-check-label">
        <input type="checkbox" name="apply_filters[]" value="product_group" <?= !empty($_GET['product_group']) ? 'checked' : 'disabled' ?> class="form-check-input">
        <i class="bi bi-box-seam"></i> Seçili Ürün Grubu
    </label><br>

    <label class="form-check-label">
        <input type="checkbox" name="apply_filters[]" value="brand_id" <?= !empty($_GET['brand_id']) ? 'checked' : 'disabled' ?> class="form-check-input">
        <i class="bi bi-tags"></i> Seçili Marka
    </label><br>

    <label class="form-check-label">
        <input type="checkbox" name="apply_filters[]" value="price_range" <?= (!empty($_GET['min_price']) || !empty($_GET['max_price'])) ? 'checked' : 'disabled' ?> class="form-check-input">
        <i class="bi bi-cash-stack"></i> Seçili Fiyat Aralığı
    </label><br>

    <label class="form-check-label">
        <input type="checkbox" name="apply_filters[]" value="search" <?= !empty($_GET['product_name']) ? 'checked' : 'disabled' ?> class="form-check-input">
        <i class="bi bi-search"></i> Arama Sonuçları
    </label><br>
    
    <label class="form-check-label">
        <input type="checkbox" name="apply_filters[]" value="barcode" <?= !empty($_GET['barcode']) ? 'checked' : 'disabled' ?> class="form-check-input">
        <i class="bi bi-upc-scan"></i> Barkod Arama Sonuçları
    </label>
</div>

<hr>
<?php
    // Güncelleme butonu için
    $_SERVER['REQUEST_URI'] = "/update/hizli_fiyat_guncelle.php?action=update";
    include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/code/button.php";
    ?>

        </form>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger mt-3">
                Lütfen önce filtre seçimi yapınız!
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success p-3">
                Fiyatlar başarıyla güncellendi!
            </div>
        <?php endif; ?>

<hr> 

<h5>
   <i class="bi bi-bag-check"></i>
   Ürün Listesi
</h5>
<div class="table-responsive-md">  
 <table class="table table-bordered table-striped table-hover align-middle text-center">  
   <thead class="table-dark">  
    <tr>  
     <th>#</th>  
     <th>ID</th>  
     <th><i class="bi bi-tag"></i> Ürün Adı</th>  
     <th><i class="bi bi-upc-scan"></i> Barkod</th>  
     <th><i class="bi bi-cash-stack"></i> Alış Fiyatı</th>  
     <th><i class="bi bi-cash-coin"></i> Satış Fiyatı</th>  
     <th><i class="bi bi-percent"></i> Kâr Oranı (%)</th>  
     <th><i class="bi bi-grid"></i> Ürün Grubu</th>  
     <th><i class="bi bi-building"></i> Marka</th>  
     <th><i class="bi bi-box-seam"></i> Birimi</th>  
     <th><i class="bi bi-lightning-charge"></i> Hızlı Ürün</th>  
     <th><i class="bi bi-image"></i> Resim</th>  
   </tr>  
 </thead>  
 <tbody>  
   <?php if (!empty($products)):  
     $sira = 1; // Sıra numarası başlangıç
   ?>  
     <?php foreach ($products as $product): ?>  
     <tr>  
       <td><?= $sira++ ?></td>  
       <td><?= htmlspecialchars($product['id']) ?></td>  
       <td class="text-start">
         <a href="#" class="text-decoration-none fw-bold" onclick="toggleMenu('menu_<?= $product['id'] ?>'); return false;">
           <?= htmlspecialchars($product['product_name']) ?>
         </a>
         <div id="menu_<?= $product['id'] ?>" class="bg-light border rounded p-2 mt-1 d-none">
           <a href="urun_duzenle.php?id=<?= $product['id'] ?>" class="d-block text-primary">  
             <i class="bi bi-pencil-square"></i> Düzenle  
           </a>  
           <hr class="my-1">
           <a href="urun_sil.php?id=<?= $product['id'] ?>" class="d-block text-danger"  
              onclick="return confirm('Silmek istediğinizden emin misiniz?')">  
             <i class="bi bi-trash"></i> Sil  
           </a>  
         </div>
       </td>  
       <td><?= htmlspecialchars($product['barcode']) ?></td>  
       <td><?= htmlspecialchars($product['purchase_price']) ?> TL</td>  
       <td><?= htmlspecialchars($product['sale_price']) ?> TL</td>  
       <td><?= number_format($product['profit_margin'], 2) ?>%</td>  
       <td><?= htmlspecialchars($product['group_name'] ?? 'Belirtilmemiş') ?></td>  
       <td><?= htmlspecialchars($product['brand_name'] ?? 'Belirtilmemiş') ?></td>  
       <td><?= htmlspecialchars($product['unit']) ?></td>  
       <td>  
         <span class="badge <?= $product['hizli_urun'] ? 'bg-success' : 'bg-danger' ?>">  
           <?= $product['hizli_urun'] ? 'Evet' : 'Hayır' ?>  
         </span>  
       </td>  
       <td>  
         <?php if (!empty($product['image_path'])): ?>  
           <img src="<?= htmlspecialchars($product['image_path']) ?>"  
           alt="Ürün Resmi"  
           class="img-thumbnail d-block mx-auto"  
           style="width: 50px; height: 50px; object-fit: cover;"  
           onerror="this.src='no-image.png'">  
         <?php else: ?>  
           <span class="text-muted"><img src="../uploads/no-image.png" alt=""></span>  
         <?php endif; ?>  
       </td>  
     </tr>  
     <?php endforeach; ?>  
   <?php else: ?>  
     <tr>  
       <td colspan="12" class="text-center text-muted">Hiç ürün bulunamadı.</td>  
     </tr>  
   <?php endif; ?>  
 </tbody>  
</table>  
</div>

<script>
function toggleMenu(id) {
  var menu = document.getElementById(id);
  if (menu.classList.contains('d-none')) {
    document.querySelectorAll('.bg-light.border.rounded.p-2').forEach(el => el.classList.add('d-none'));
    menu.classList.remove('d-none');
  } else {
    menu.classList.add('d-none');
  }
}
</script>


  
    <script>
        function openBarcodeScanner(event) {
            event.preventDefault();
            window.open('../camera.php', 'BarcodeScanner', 'width=640,height=480,toolbar=no,statusbar=no,menubar=no');
        }
    </script>
        <?php endif; ?>
</body>

</html>