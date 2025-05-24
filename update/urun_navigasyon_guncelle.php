


<?php
session_start();

// Veritabanı bağlantısı
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

// Ürün ID ve ürün listesi
$currentProductId = $_GET['product_id'] ?? null;
$products = $vt->query("SELECT id, product_name, product_group, brand_id FROM products ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Varsayılan olarak ilk ürünü al
$currentProductId = $currentProductId ?? ($products[0]['id'] ?? null);
$currentIndex = array_search($currentProductId, array_column($products, 'id'));

if ($currentIndex === false) {
   die("Geçersiz ürün ID'si.");
}

// Ürün detayları
$stmt = $vt->prepare("SELECT p.*, g.group_name, b.brand_name
                      FROM products p
                      LEFT JOIN product_groups g ON p.product_group = g.id
                      LEFT JOIN brands b ON p.brand_id = b.id
                      WHERE p.id = :id");
$stmt->execute([':id' => $currentProductId]);
$currentProduct = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$currentProduct) {
   die("Ürün bulunamadı.");
}

// Grup ve marka güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $newGroup = $_POST['new_group'] ?? null;
   $newBrand = $_POST['new_brand'] ?? null;

   if ($newGroup || $newBrand) {
      $fieldsToUpdate = [];
      $updateParams = [':id' => $currentProductId];

      if ($newGroup) {
         $fieldsToUpdate[] = "product_group = :new_group";
         $updateParams[':new_group'] = $newGroup;
      }
      if ($newBrand) {
         $fieldsToUpdate[] = "brand_id = :new_brand";
         $updateParams[':new_brand'] = $newBrand;
      }

      $updateQuery = "UPDATE products SET " . implode(", ", $fieldsToUpdate) . " WHERE id = :id";
      $vt->prepare($updateQuery)->execute($updateParams);

      header("Location: urun_navigasyon_guncelle.php?product_id=$currentProductId&success=1");
      exit;
   }
}

include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
?>

<!DOCTYPE html>
<html lang="tr">
<head>
   <title>Ürün Güncelle</title>
   <style>
      .toggle-group {
         display: none;
         margin-left: 20px;
      }
      .toggle-btn {
         cursor: pointer;
         color: #007bff;
      }
      .toggle-btn:hover {
         text-decoration: underline;
      }
   </style>
</head>
<body>

   <?php if (!$kullanici_adi): ?>
   <a href="../auth/login.php">İçerikleri görmek için giriş yapın</a>
   <?php else : ?>
   
      <h5>
         <i class="bi bi-lightning-charge-fill"></i>
         Hizli Ürün Düzenle 
      </h5>

      <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">
         Güncelleme başarıyla tamamlandı.
      </div>
      <?php endif; ?>


<div>
<!-- Ürün Listesi -->
<div>
<!-- Ürün Listesi -->
<div class="p-3">
   <?php
   $groups = [];
   $unassigned = [];
   foreach ($products as $product) {
      if (!$product['product_group'] || !$product['brand_id']) {
         $unassigned[] = $product;
      } else {
         $groups[$product['product_group']][] = $product;
      }
   }
   ?>

   <!-- Belirtilmemiş Ürünler -->
<div class="belirtilmemiş-ürün">
   <?php if (count($unassigned) > 0): ?>
   <div class="bg-secondary yanone-kaffeesatz d-flex align-items-center p-3 text-white" onclick="toggleVisibility('unassigned')">
      <i class="bi bi-exclamation-circle me-2"></i> Belirtilmemiş Ürün (<?= count($unassigned) ?>)
   </div>
   <ul id="unassigned" class="toggle-group list-group m-0 p-0">
      <?php foreach ($unassigned as $product): ?>
         <li class="list-group-item list-group-item-action product-link 
            <?= $product[''] == $currentProductId ? 'active' : '' ?>">
            <a href="?product_id=<?= $product['id'] ?>" class="d-flex align-items-center text-decoration-none">
               <i class="bi bi-cart me-2"></i> 
               Ürün ID: <?= $product['id'] ?> - <?= htmlspecialchars($product['product_name']) ?> (Belirtilmemiş)
            </a>
         </li>
      <?php endforeach; ?>
   </ul>
   <?php endif; ?>
</div>
<hr>

 <div class="urun-gruplari">
   <?php
   // Ürün grubu ve ürün sayılarını al
   $groupCounts = [];
   $totalGroupedProducts = 0;

   $stmt = $vt->query("SELECT product_group, COUNT(*) as count FROM products WHERE product_group IS NOT NULL AND product_group != '' GROUP BY product_group");
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $groupCounts[$row['product_group']] = $row['count'];
      $totalGroupedProducts += $row['count']; // Sadece gruplu ürünleri say
   }

   // Tüm grupları al
   $groups = $vt->query("SELECT id, group_name FROM product_groups")->fetchAll(PDO::FETCH_ASSOC);

   // Dolular ve boşlar
   $filledGroups = 0;
   $emptyGroups = 0;

   foreach ($groups as $group) {
      if (isset($groupCounts[$group['id']]) && $groupCounts[$group['id']] > 0) {
         $filledGroups++;
      } else {
         $emptyGroups++;
      }
   }
   ?>
   <div class="bg-secondary yanone-kaffeesatz d-flex align-items-center p-3 text-white" onclick="toggleVisibility('all-groups')">
      <i class="bi bi-box me-2"></i> Ürün Grupları (<?= $filledGroups ?> + <?= $emptyGroups ?> = <?= count($groups) ?>) | Toplam Ürün: <?= $totalGroupedProducts ?>
   </div>
   <ul id="all-groups" class="toggle-group list-group m-0 p-0" style="display:none;">
      <?php foreach ($groups as $group): ?>
         <?php
         $groupProductCount = isset($groupCounts[$group['id']]) ? $groupCounts[$group['id']] : 0;
         ?>
         <li class="list-group-item">
            <a href="javascript:void(0);" class="toggle-btn d-flex align-items-center text-decoration-none" onclick="toggleVisibility('group-<?= $group['id'] ?>')">
               <i class="bi bi-box-seam me-2"></i> <?= htmlspecialchars($group['group_name']) ?> (<?= $groupProductCount ?>)
            </a>
            <ul id="group-<?= $group['id'] ?>" class="toggle-group list-group m-2" style="display:none;">
               <?php
               $stmt = $vt->prepare("SELECT id, product_name FROM products WHERE product_group = :group_id");
               $stmt->execute([':group_id' => $group['id']]);
               $groupProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

               foreach ($groupProducts as $product): ?>
               <li class="list-group-item list-group-item-action">
                  <a href="?product_id=<?= $product['id'] ?>" class="d-flex align-items-center text-decoration-none">
                     <i class="bi bi-cart me-2"></i> <?= htmlspecialchars($product['product_name']) ?>
                  </a>
               </li>
               <?php endforeach; ?>
            </ul>
         </li>
      <?php endforeach; ?>
   </ul>
</div>

<script>
function toggleVisibility(id) {
   var element = document.getElementById(id);
   if (element.style.display === "none") {
      element.style.display = "block";
   } else {
      element.style.display = "none";
   }
}
</script>

<hr>
<!-- Markalar (Brands) Bölümü -->
<div class="markalar">
   <?php
   // Marka ürün sayılarını al
   $brandCounts = [];
   $totalBrandProducts = 0;
   $filledBrands = 0;
   $emptyBrands = 0;

   // Markalara göre ürün sayısını al
   $stmt = $vt->query("SELECT brand_id, COUNT(*) as count FROM products WHERE brand_id IS NOT NULL GROUP BY brand_id");
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $brandCounts[intval($row['brand_id'])] = $row['count'];
      $totalBrandProducts += $row['count']; // Sadece markalara ait toplam ürün sayısı
   }

   // Tüm markaları çek
   $brands = $vt->query("SELECT id, brand_name FROM brands")->fetchAll(PDO::FETCH_ASSOC);
   $totalBrands = count($brands); // Veritabanındaki toplam marka sayısı

   // Dolu ve boş marka sayılarını hesapla
   foreach ($brands as $brand) {
      $brandId = intval($brand['id']);
      if (isset($brandCounts[$brandId]) && $brandCounts[$brandId] > 0) {
         $filledBrands++;
      } else {
         $emptyBrands++;
      }
   }
   ?>
   <div class="bg-secondary yanone-kaffeesatz d-flex align-items-center p-3 text-white" onclick="toggleVisibility('all-brands')">
      <i class="bi bi-tags me-2"></i> Markalar (<?= $filledBrands ?> + <?= $emptyBrands ?> = <?= $totalBrands ?>) | Toplam Marka Ürünü: <?= $totalBrandProducts ?>
   </div>

   <ul id="all-brands" class="list-group m-0 p-0" style="display:none;">
      <?php foreach ($brands as $brand): 
         $brandId = intval($brand['id']);
         $brandProductCount = isset($brandCounts[$brandId]) ? $brandCounts[$brandId] : 0;
      ?>
         <li class="list-group-item mb-3 p-3">
            <a href="javascript:void(0);" class="toggle-btn d-flex align-items-center text-decoration-none" onclick="toggleVisibility('brand-<?= $brandId ?>')">
               <i class="bi bi-tag-fill me-2"></i> <?= htmlspecialchars($brand['brand_name']) ?> (<?= $brandProductCount ?>)
            </a>
            <ul id="brand-<?= $brandId ?>" class="toggle-group list-group m-2" style="display:none;">
               <?php
               $stmt = $vt->prepare("SELECT id, product_name FROM products WHERE brand_id = :brand_id");
               $stmt->execute([':brand_id' => $brandId]);
               $brandProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

               if (count($brandProducts) > 0): 
                  foreach ($brandProducts as $product): ?>
                     <li class="list-group-item list-group-item-action">
                        <a href="?product_id=<?= $product['id'] ?>" class="d-flex align-items-center text-decoration-none">
                           <i class="bi bi-cart me-2"></i> <?= htmlspecialchars($product['product_name']) ?>
                        </a>
                     </li>
                  <?php endforeach; 
               else: ?>
                  <li class="list-group-item text-muted">Bu markaya ait ürün yok</li>
               <?php endif; ?>
            </ul>
         </li>
      <?php endforeach; ?>
   </ul>
</div>
</div>


      <!-- Mevcut Ürün Bilgisi -->
      <div class="musteri-bilgileri p-3">
         <p><i class="bi bi-tag"></i>
         Ürün: 
         <?= htmlspecialchars($currentProduct['product_name']) ?></p>
         
         <p>
            <i class="bi bi-upc-scan"></i>
            Barkod: <?= htmlspecialchars($currentProduct['barcode']) ?>
         </p>
         
         <p>
            <i class="bi bi-cash"></i>
            Fiyat: <?= htmlspecialchars($currentProduct['sale_price']) ?> TL
         </p>
         <p>
           <i class="bi bi-collection"></i>
            Grup: <?= $currentProduct['group_name'] ? htmlspecialchars($currentProduct['group_name']) : '<span style="color:red;">Belirtilmemiş</span>' ?>
         </p>
         <p>
            <i class="bi bi-building"></i>
             Marka: <?= $currentProduct['brand_name'] ? htmlspecialchars($currentProduct['brand_name']) : '<span style="color:red;">Belirtilmemiş</span>' ?>
         </p>
      </div>

<hr>
   <?php
 
       include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/code/grup_marka.php";
      
 
 ?>

      <!-- Güncelleme Formu -->
      <form method="post" action="?product_id=<?= $currentProductId ?>" class="border p-4 rounded shadow-sm">
<div class="bg-secondary p-3">
   <label for="new_group" class="form-label">
      <i class="fas fa-list me-2"></i> Yeni Grup:
   </label>
   <select name="new_group" id="new_group" class="form-select">
      <option value="">Seç</option>
      <?php
      // Gruplara göre ürün sayılarını al
      $groupCounts = [];
      $stmt = $vt->query("SELECT product_group, COUNT(*) as count FROM products WHERE product_group IS NOT NULL GROUP BY product_group");
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         $groupCounts[intval($row['product_group'])] = $row['count'];
      }

      // Tüm grupları çek
      $groups = $vt->query("SELECT * FROM product_groups")->fetchAll(PDO::FETCH_ASSOC);

      foreach ($groups as $group) {
         $groupId = intval($group['id']);
         $groupProductCount = isset($groupCounts[$groupId]) ? $groupCounts[$groupId] : 0;
         $selected = ($currentProduct['product_group'] == $groupId) ? 'selected' : '';
         echo "<option value='{$groupId}' {$selected}>{$group['group_name']} ({$groupProductCount})</option>";
      }
      ?>
   </select>
</div>
<hr>
<div class="bg-secondary p-3">
   <label for="new_brand" class="form-label">
      <i class="fas fa-cogs me-2"></i> Yeni Marka:
   </label>
   <select name="new_brand" id="new_brand" class="form-select">
      <option value="">Seç</option>
      <?php
      // Markalara göre ürün sayılarını al
      $brandCounts = [];
      $stmt = $vt->query("SELECT brand_id, COUNT(*) as count FROM products WHERE brand_id IS NOT NULL GROUP BY brand_id");
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         $brandCounts[intval($row['brand_id'])] = $row['count'];
      }

      // Tüm markaları çek
      $brands = $vt->query("SELECT * FROM brands")->fetchAll(PDO::FETCH_ASSOC);

      foreach ($brands as $brand) {
         $brandId = intval($brand['id']);
         $brandProductCount = isset($brandCounts[$brandId]) ? $brandCounts[$brandId] : 0;
         $selected = ($currentProduct['brand_id'] == $brandId) ? 'selected' : '';
         echo "<option value='{$brandId}' {$selected}>{$brand['brand_name']} ({$brandProductCount})</option>";
      }
      ?>
   </select>
</div>
<hr>
<?php       include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/code/button.php"; ?>
      
      
      </form>

      <!-- Ürün Navigasyonu -->
      <div class="mt-4 d-flex justify-content-between">
         <button class="btn btn-secondary" <?= $currentIndex <= 0 ? 'disabled' : '' ?> onclick="navigateTo('<?= $products[$currentIndex - 1]['id'] ?? '#' ?>')">
            <i class="fas fa-arrow-left me-2"></i> Geri
         </button>
         <button class="btn btn-secondary" <?= $currentIndex >= count($products) - 1 ? 'disabled' : '' ?> onclick="navigateTo('<?= $products[$currentIndex + 1]['id'] ?? '#' ?>')">
            <i class="fas fa-arrow-right me-2"></i> İleri
         </button>
      </div>
   </div>

   <script>
      function toggleVisibility(id) {
         var element = document.getElementById(id);
         if (element.style.display === 'none') {
            element.style.display = 'block';
         } else {
            element.style.display = 'none';
         }
      }

      function navigateTo(productId) {
         if (productId !== '#') {
            window.location.href = "?product_id=" + productId;
         }
      }

      document.addEventListener('keydown', function(e) {
         if (e.key === 'ArrowLeft') {
            navigateTo('<?= $products[$currentIndex - 1]['id'] ?? '#' ?>');
         } else if (e.key === 'ArrowRight') {
            navigateTo('<?= $products[$currentIndex + 1]['id'] ?? '#' ?>');
         }
      });
   </script>

   <?php endif; ?>
</body>
</html>