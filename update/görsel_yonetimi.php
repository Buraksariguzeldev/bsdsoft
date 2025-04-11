<?php
session_start();


include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

try {
    // DB bağlantısı başarılı
} catch (PDOException $e) {
    die("Veri tabanına bağlanılamadı: " . $e->getMessage());
}

// Resim durumu filtresi
$image_filter = '';
if (isset($_GET['filter']) && $_GET['filter'] == 'no-image') {
    $image_filter = " AND (image_path IS NULL OR image_path = '')";
} elseif (isset($_GET['filter']) && $_GET['filter'] == 'has-image') {
    $image_filter = " AND (image_path IS NOT NULL AND image_path != '')";
}

// Ürün bilgilerini çek
$query = "SELECT * FROM products WHERE 1=1 $image_filter ORDER BY product_name ASC";
$stmt = $vt->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Resim yollarını güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['images'] as $id => $newPath) {
        $newPath = trim($newPath);

        // Mevcut resim yolunu veri tabanından çek
        $stmt = $vt->prepare("SELECT image_path FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($newPath) && $newPath !== $product['image_path']) {
            $oldPath = $product['image_path'];

            // Dosya adı değiştirme işlemi
            if (!empty($oldPath) && file_exists($oldPath)) {
                $newDir = pathinfo($newPath, PATHINFO_DIRNAME);
                if (!is_dir($newDir)) {
                    mkdir($newDir, 0777, true); // Yeni dizin oluştur
                }

                // Dosya adını değiştir
                if (rename($oldPath, $newPath)) {
                    echo "Dosya başarıyla taşındı: $oldPath -> $newPath<br>";
                } else {
                    echo "Dosya taşınırken bir hata oluştu: $oldPath<br>";
                    continue;
                }
            }

            // Veri tabanını güncelle
            $stmt = $vt->prepare("UPDATE products SET image_path = :image_path WHERE id = :id");
            $stmt->execute([
                ':image_path' => $newPath,
                ':id' => $id
            ]);
        }
    }

    echo "<p>Resim yolları ve dosyalar başarıyla güncellendi!</p>";
    header('Location: görsel_yonetimi.php');
    exit;
}

// Giriş kontrolü
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  
    <title>Resim Yönetimi</title>
  

</head>
<body>
        <?php if (!$kullanici_adi): ?>

      

<a href="../auth/login.php" class="btn btn-link text-decoration-none">
    İçerikleri görmek için giriş yapın
</a>
     
     
         <?php else: ?>
   
       <h5>
          <i class="bi bi-image-alt"></i>
          Ürün Resim Yönetimi
       </h5>

       <!-- Filtreleme -->
       <div class="bg-secondary p-3 text-center">
           <a href="görsel_yonetimi.php?filter=has-image" class="btn btn-warning">
               <i class="bi bi-card-image  "></i> Resim Olanlar
           </a>
           <hr>
           <a href="görsel_yonetimi.php?filter=no-image" class="btn btn-warning
           ">
               <i class="fas fa-times-circle"></i> 
               Resimsiz Olanlar
           </a>
           <hr>
           <a href="görsel_yonetimi.php" class="btn btn-warning">
               <i class="fas fa-sync-alt"></i> Tümünü Göster
           </a>
       </div>

<hr>
       <!-- Form -->
<form method="post" action="">
<?php $sira = 1; // Sıra numarasını başlat ?>

<div class="table-responsive-md">
    <table class="table table-bordered table-striped table-hover align-middle
    text-center p-3">
        <thead class="table-dark text-center">
            <tr>
                <th>#</th>
                <th>ID</th>
     <th><i class="bi bi-tag"></i> Ürün Adı</th>  
     <th><i class="bi bi-image"></i> Resim</th>  
                <th class=""><i class="bi bi-folder-symlink p-3"></i>Resim Yolu</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): 
                $imagePath = !empty($product['image_path']) ? $product['image_path'] : '/uploads/no-image.png';
            ?>
                <tr>
                    <td><?= $sira++ ?></td> <!-- İlk başta yazdır, sonra artır -->
                    <td><?= htmlspecialchars($product['id']) ?></td>
                    <td class="text-nowrap"><?= htmlspecialchars($product['product_name']) ?></td>
                    <td class="text-center">
                        <img src="<?= htmlspecialchars($imagePath) ?>" 
                             alt="Ürün Resmi" 
                             class="img-thumbnail d-block mx-auto" 
                             style="width: 50px; height: 50px; object-fit: cover;"
                             onerror="this.src='../uploads/no-image.png'">
                    </td>
                    <td>
                        <div class="d-flex align-items-center w-100">
                            <button type="button" class="btn btn-primary btn-sm toggle-input" data-id="<?= $product['id'] ?>">
                                <i class="bi-pencil"></i>
                            </button>
                            <input type="text" 
                                   name="images[<?= $product['id'] ?>]" 
                                   value="<?= htmlspecialchars($imagePath) ?>" 
                                   class="form-control form-control-sm ms-2 toggle-target collapsed-input" 
                                   id="input-<?= $product['id'] ?>" 
                                   readonly>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".toggle-input").forEach(button => {
        button.addEventListener("click", function() {
            let inputField = document.getElementById("input-" + this.getAttribute("data-id"));
            
            if (inputField.classList.contains("collapsed-input")) {
                inputField.classList.remove("collapsed-input");
                inputField.classList.add("expanded-input");
                document.querySelector(".table-responsive-md").classList.add("expand-table");
                inputField.removeAttribute("readonly");
                inputField.focus();
            } else {
                inputField.classList.add("collapsed-input");
                inputField.classList.remove("expanded-input");
                document.querySelector(".table-responsive-md").classList.remove("expand-table");
                inputField.setAttribute("readonly", true);
            }
        });
    });
});
</script>

<style>
/* Tablo sağa genişlesin */
.expand-table {
    overflow-x: auto;
    transition: 0.3s;
    padding-right: 100px;
}

/* İlk başta küçük input */
.toggle-target {
    width: 150px;
    transition: width 0.3s ease-in-out;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Açıldığında geniş input */
.expanded-input {
    width: 100%;
}
</style>
           <button type="submit" class="btn btn-primary">
               <i class="fas fa-sync-alt"></i> Resim Yollarını Güncelle
           </button>
       </form>
   </d>

   <!-- Bootstrap JS, Popper.js, Font Awesome -->

       <?php endif; ?>
</body>
</html>