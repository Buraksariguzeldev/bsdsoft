<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

// Veri tabanı bağlantısı
try {
   include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
} catch (PDOException $e) {
   die("Veri tabanına bağlanılamadı: " . $e->getMessage());
}

// Ürün ID kontrolü
if (!isset($_GET['id']) || empty($_GET['id'])) {
   die("Geçersiz ürün ID'si!");
}

$productId = $_GET['id'];

// Ürün bilgilerini çek
$stmt = $vt->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
   die("Ürün bulunamadı!");
}

// Ürün grupları ve markalar
$product_groups = $vt->query("SELECT * FROM product_groups")->fetchAll(PDO::FETCH_ASSOC);
$brands = $vt->query("SELECT * FROM brands")->fetchAll(PDO::FETCH_ASSOC);

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $productName = $_POST['product_name'];
   $barcode = $_POST['barcode'];
   $purchasePrice = $_POST['purchase_price'];
   $salePrice = $_POST['sale_price'];
$profitMargin = str_replace('%', '', $_POST['profit_margin']); // '%' işaretini kaldır
$profitMargin = floatval($profitMargin); // Sayıya çevir
   $productGroup = $_POST['product_group'];
   $brandId = $_POST['brand_id'];
   $unit = $_POST['unit'];
   $hizliUrun = isset($_POST['hizli_urun']) ? 1 : 0;
   $is_kg = ($unit === 'kg') ? 1 : 0;

   $imagePath = $product['image_path']; // Varsayılan olarak eski resim

   // **Resim yükleme işlemi**
   if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
      $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";

      // Hedef klasör yoksa oluştur
      if (!file_exists($targetDir)) {
         mkdir($targetDir, 0777, true);
      }

      $fileTmpPath = $_FILES['image']['tmp_name'];
      $fileName = basename($_FILES['image']['name']);
      $fileSize = $_FILES['image']['size'];
      $fileType = mime_content_type($fileTmpPath);
      $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
      $allowedTypes = ['image/jpeg',
         'image/png',
         'image/gif'];

      // Dosya türü kontrolü
      if (!in_array($fileType, $allowedTypes)) {
         die("Hata: Sadece JPG, PNG ve GIF dosyaları yükleyebilirsiniz.");
      }

      // Dosya boyutu kontrolü (Örneğin 5MB)
      if ($fileSize > 5 * 1024 * 1024) {
         die("Hata: Dosya boyutu 5MB'dan büyük olamaz.");
      }

      // Yeni dosya adı
      $newFileName = $productName . '.' . $fileExtension;
      $targetFilePath = $targetDir . $newFileName;

      if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
         $imagePath = "/uploads/" . $newFileName; // Yolu kaydet
      } else {
         die("Hata: Dosya yüklenirken hata oluştu!");
      }
   }

   // Ürünü güncelle
   $stmt = $vt->prepare("UPDATE products SET product_name = :product_name, barcode = :barcode, purchase_price = :purchase_price, sale_price = :sale_price, profit_margin = :profit_margin, product_group = :product_group, brand_id = :brand_id, unit = :unit, image_path = :image_path, hizli_urun = :hizli_urun, is_kg = :is_kg WHERE id = :id");

   $stmt->execute([
      ':product_name' => $productName,
      ':barcode' => $barcode,
      ':purchase_price' => $purchasePrice,
      ':sale_price' => $salePrice,
      ':profit_margin' => $profitMargin,
      ':product_group' => $productGroup,
      ':brand_id' => $brandId,
      ':unit' => $unit,
      ':image_path' => $imagePath,
      ':hizli_urun' => $hizliUrun,
      ':is_kg' => $is_kg,
      ':id' => $productId,
   ]);

   echo '<div class="container mt-5">
            <div class="alert alert-success text-center">
                <i class="fas fa-check-circle"></i> Ürün başarıyla düzenlendi
            </div>
            <div class="text-center">
                <p>Yönlendiriliyorsunuz...</p>
            </div>
          </div>';

   echo "<script type='text/javascript'>
            setTimeout(function() {
                window.location.href = 'urun_listesi.php';
            }, 1000);
          </script>";

   exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>


</head>
<body>


   <?php if (!$kullanici_adi): ?>



   <a href="../auth/login.php" class="btn btn-link text-decoration-none">
      İçerikleri görmek için giriş yapın
   </a>


   <?php else : ?>



   
      <h5>
         <i class="bi bi-pencil-square"></i>
         Ürün Düzenle
      </h5>

      <form method="post" action="urun_duzenle.php?id=<?= htmlspecialchars($productId) ?>" enctype="multipart/form-data">


         <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/functions/urunadi.php";

         echo '<hr>';

         include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/functions/barkodno.php";

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
   </d>
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

   </script>

   <?php endif; ?>
</body>
</html>