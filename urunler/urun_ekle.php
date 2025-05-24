<?php
session_start();
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');
date_default_timezone_set('Europe/Istanbul');

include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

try {
   // Ürün gruplarını çek
   $stmt = $vt->query("SELECT * FROM product_groups");
   $product_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Markaları çek
   $stmt = $vt->query("SELECT * FROM brands");
   $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Barkod kontrolü
      $checkBarcode = $vt->prepare("SELECT COUNT(*) FROM products WHERE barcode = ?");
      $checkBarcode->execute([$_POST['barcode']]);
      $barcodeExists = $checkBarcode->fetchColumn();

      // Eğer barkod varsa, hata mesajı göster
      if ($barcodeExists > 0) {
         echo '<i class="fas fa-exclamation-circle"></i> Aynı ürün barkodu zaten mevcut!';
      } else {
         // Ürün bilgilerini al
         $product_name = $_POST['product_name'];
         $barcode = $_POST['barcode'];
         $purchase_price = $_POST['purchase_price'];
         $sale_price = $_POST['sale_price'];
         $profit_margin = (($sale_price - $purchase_price) / $purchase_price) * 100; // Kar oranı
         $product_group = $_POST['product_group'];
         $brand_id = $_POST['brand_id'];
         $unit = $_POST['unit'];
         $hizli_urun = isset($_POST['hizli_urun']) ? 1 : 0;
         $is_kg = ($unit === 'kg') ? 1 : 0;

         // Resim yükleme işlemi
         $image_path = '';
         if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Ürün adını temizle ve resim adı olarak belirle
            $clean_product_name = preg_replace("/[^a-zA-Z0-9]+/", "_", strtolower(trim($product_name)));
            $image_path = '../uploads/' . $clean_product_name . '.png'; // Yeni resim adı
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
         }

         // En son ID'yi bul ve bir sonraki sıralı ID'yi belirle
         $stmt = $vt->query("SELECT MAX(id) FROM products");
         $max_id = $stmt->fetchColumn();
         $next_id = $max_id ? $max_id + 1 : 1; // Eğer max_id varsa 1 artır, yoksa 1 olarak ayarla

         // Yeni ürünü veri tabanına ekle
         $stmt = $vt->prepare("INSERT INTO products (id, product_name, barcode, purchase_price, sale_price, profit_margin, product_group, brand_id, unit, image_path, hizli_urun, is_kg)
                VALUES (:id, :product_name, :barcode, :purchase_price, :sale_price, :profit_margin, :product_group, :brand_id, :unit, :image_path, :hizli_urun, :is_kg)");

         $stmt->execute([
            ':id' => $next_id,
            ':product_name' => $product_name,
            ':barcode' => $barcode,
            ':purchase_price' => $purchase_price,
            ':sale_price' => $sale_price,
            ':profit_margin' => $profit_margin,
            ':product_group' => $product_group,
            ':brand_id' => $brand_id,
            ':unit' => $unit,
            ':image_path' => $image_path,
            ':hizli_urun' => $hizli_urun,
            ':is_kg' => $is_kg
         ]);

         echo '<i class="fas fa-check-circle"></i> Ürün başarıyla eklendi!';
      }
   }
} catch (PDOException $e) {
   die('<i class="fas fa-exclamation-triangle"></i> Veri tabanına bağlanılamadı: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

   <title>Ürün Ekle</title>

</head>
<body>
   <?php if (!$kullanici_adi): ?>



   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>

   <?php else : ?>

   <h5>
       <i class="bi bi-bag-plus"></i>
       Ürün Ekle
   </h5>

   <form action="" method="POST" enctype="multipart/form-data">

      <?php
      include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/functions/urunadi.php";

      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/barkodno.php";

      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/alisfiyat.php";

      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/satisfiyat.php";

      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/karorani.php";

      echo('<hr>');


      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/birimsec.php";

      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/urungrubu.php";

      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/marka.php";

      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/code/grup_marka.php";

      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/hizliurun.php";

      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/functions/urunresmi.php";

      echo('<hr>');

      include $_SERVER["DOCUMENT_ROOT"] .
      "/assets/src/code/button.php";

      ?>



   </form>


</div>

<script>
   document.getElementById('purchase_price').addEventListener('input', hesaplaKarOrani);
   document.getElementById('sale_price').addEventListener('input', hesaplaKarOrani);

   function hesaplaKarOrani() {
      var alisFiyati = parseFloat(document.getElementById('purchase_price').value) || 0;
      var satisFiyati = parseFloat(document.getElementById('sale_price').value) || 0;

      if (alisFiyati > 0) {
         var karOrani = ((satisFiyati - alisFiyati) / alisFiyati) * 100;
         document.getElementById('profit_margin').value = karOrani.toFixed(2) + '%';
      } else {
         document.getElementById('profit_margin').value = '';
      }
   }

   function openBarcodeScanner(event) {
      event.preventDefault();
      window.open('../assets/src/php/camera.php', 'BarcodeScanner', 'width=640,height=480,toolbar=no,statusbar=no,menubar=no');
   }

   document.getElementById('urunForm').addEventListener('submit', function(e) {
      e.preventDefault();

      let formData = new FormData(this);

      fetch(this.action, {
         method: 'POST',
         body: formData
      })
      .then(response => response.text())
      .then(data => {
         alert(data); // Sunucudan gelen mesajı göster
         if (data.includes('başarıyla')) {
            window.location.reload(); // Başarılı ise sayfayı yenile
         }
      })
      .catch(error => {
         console.error('Hata:', error);
         alert('Bir hata oluştu!');
      });
   });

   document.getElementById('purchase_price').addEventListener('input', hesaplaKarOrani);
   document.getElementById('sale_price').addEventListener('input', hesaplaKarOrani);

   function hesaplaKarOrani() {
      var alisFiyati = parseFloat(document.getElementById('purchase_price').value) || 0;
      var satisFiyati = parseFloat(document.getElementById('sale_price').value) || 0;

      if (alisFiyati > 0) {
         var karOrani = ((satisFiyati - alisFiyati) / alisFiyati) * 100;
         document.getElementById('profit_margin').value = karOrani.toFixed(2) + '%';
      } else {
         document.getElementById('profit_margin').value = '';
      }
   }


</script>


<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
<?php endif; ?>
</body>
</html>