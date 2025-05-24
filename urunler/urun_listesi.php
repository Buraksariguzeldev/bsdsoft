<?php  
session_start();  
  
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);  
  
// Eğer giriş yapılmamışsa yönlendir  
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');  
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');  
  
try {  
   // Filtreleme için değişkenleri al  
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
  
   if (!empty($_GET['min_price']) && !empty($_GET['max_price'])) {  
      $sql .= " AND p.sale_price BETWEEN :min_price AND :max_price";  
      $filters[':min_price'] = $_GET['min_price'];  
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
  
   // Grup ve marka sayıları  
   $group_counts = [];  
   $brand_counts = [];  
  
   // Ürün sayılarının sayılması  
   foreach ($products as $product) {  
      if (!isset($group_counts[$product['product_group']])) {  
         $group_counts[$product['product_group']] = 0;  
      }  
      $group_counts[$product['product_group']]++;  
  
      if (!isset($brand_counts[$product['brand_id']])) {  
         $brand_counts[$product['brand_id']] = 0;  
      }  
      $brand_counts[$product['brand_id']]++;  
   }  
  
} catch (PDOException $e) {  
   die("Veri tabanına bağlanılamadı: " . $e->getMessage());  
}  
?>  
  
<!DOCTYPE html>  
<html lang="tr">  
<head>  

   <title>Ürün Listesi</title>  
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script
src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</head>  
<body>  
  
   <?php if (!$kullanici_adi): ?>  
   <a href="../auth/login.php" class="btn btn-link text-decoration-none">  
      İçerikleri görmek için giriş yapın  
   </a>  
   <?php else : ?>  
   
      <h5>  
         <i class="bi bi-bag-check"></i> Ürün Listesi  
      </h5>  

      <!-- Filtre Formu -->  
      <form method="get" action="urun_listesi.php">  
  
         <!-- Ürün Adı, Grubu, Barkod ve Fiyat Filtreleme -->
 <?php
include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/functions/urunadi.php";

echo('<hr>');



include $_SERVER["DOCUMENT_ROOT"] .
"/assets/src/functions/barkodno.php" ; ?> 
<hr>
 
 
       <!-- Ürün Grubu -->
<div class="">
   <div class="bg-secondary p-3">
    <label for="product_group" class="">
        <i class="fas fa-layer-group me-2"></i> Ürün Grubu Seç;
    </label>
    <select name="product_group" id="product_group" class="form-select selectpicker" data-live-search="true">
        <option value="">Tümü</option>
        <option value="0">Belirtilmemiş</option>
        <?php foreach ($groups as $group): ?>
            <option value="<?= $group['id'] ?>" <?= (isset($_GET['product_group']) && $_GET['product_group'] == $group['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($group['group_name']) ?> 
                (<?= isset($group_counts[$group['id']]) ? $group_counts[$group['id']] : 0 ?>)
            </option>
        <?php endforeach; ?>
    </select>
</div>

<hr>
         <!-- Marka Seçimi -->
         <div class="bg-secondary p-3">
            <label for="brand_id" class="form-label">
               <i class="fas fa-tag me-2"></i> Marka Seç:
            </label>
            <select name="brand_id" id="brand_id" class="form-select">
               <option value="">Tümü</option>
               <option value="0">Belirtilmemiş</option>
               <?php foreach ($brands as $brand): ?>
               <option value="<?= $brand['id'] ?>" <?= (isset($_GET['brand_id']) && $_GET['brand_id'] == $brand['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($brand['brand_name']) ?>
                  (<?= isset($brand_counts[$brand['id']]) ? $brand_counts[$brand['id']] : 0 ?>)
               </option>
               <?php endforeach; ?>
            </select>
         </div>

<hr>
         <!-- Fiyat Aralığı -->
         <div class="bg-secondary p-3">
            
            <label for="min_price" class="mr-2"><i class="bi bi-sliders"></i>Fiyat Aralığı:</label>
            <input type="number" name="min_price" id="min_price" class="form-control mr-2" placeholder="Minimum" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
            <hr>
            <input type="number" name="max_price" id="max_price" class="form-control mr-2" placeholder="Maksimum" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
</div>

<hr>
         <!-- Sıralama Seçeneği -->  
         <div class="bg-secondary p-3">  
            <label for="order_by" class="form-label">  
               <i class="fas fa-sort me-2"></i> Sıralama:  
            </label>  
            <select name="order_by" id="order_by" class="form-select">  
               <option value="">Sıralama Seçin</option>  
               <option value="name_asc" <?= (isset($_GET['order_by']) && $_GET['order_by'] == 'name_asc') ? 'selected' : '' ?>>  
                  A'dan Z'ye  
               </option>  
               <option value="name_desc" <?= (isset($_GET['order_by']) && $_GET['order_by'] == 'name_desc') ? 'selected' : '' ?>>  
                  Z'den A'ya  
               </option>  
               <option value="barcode" <?= (isset($_GET['order_by']) && $_GET['order_by'] == 'barcode') ? 'selected' : '' ?>>  
                  Barkod ID'ye Göre  
               </option>  
            <option value="id" <?=
                              (isset($_GET['order_by']) && $_GET['order_by'] ==
                              'id') ? 'selected' : '' ?>>  
                Ürun ID'ye Göre  
               </option>  
            </select>  
         </div>  
         <hr>
         <?php
         
      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/code/button.php";

      ?>
         <!-- Diğer filtreler -->


  
      </form>  
   
   
</div>
</hr>  
      <hr>  
  
      <!-- Ürün Listesi -->  
         <h5>  
            <i class="bi bi-card-list "></i> Ürün Listesi  
         </h5> 


<form action="">     
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
</form>
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
   <?php endif; ?>  
  
   <script>  
   
         $(document).ready(function() {
    $('.selectpicker').selectpicker();
});
     
      function openBarcodeScanner(event) {  
         event.preventDefault();  
         window.open('../assets/src/php/camera.php', 'BarcodeScanner',  
            'width=640,height=480,toolbar=no,statusbar=no,menubar=no');  
      }  
   </script>  
   <script src="../assets/src/js/klavye.js"></script>  

</body>  
</html>