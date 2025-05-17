<?php
// Session başlatılmalı, yoksa $_SESSION çalışmaz
session_start();

// Navigasyon dosyanı dahil et (eğer içinde session set ediliyorsa)
include('assets/src/include/navigasyon.php');

?>
<!DOCTYPE html>
<html lang="tr">
<head>
   <meta charset="UTF-8" />
   <title>Ana Sayfa</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-5">

   <?php if (!$kullanici_adi): ?>
      <!-- Giriş yapmamış kullanıcılar için içerik -->
      <div class="alert alert-info text-center">
         <h5><i class="fas fa-home"></i> Hoş Geldiniz</h5>
         <p>bsdsoft.wuaze.com sitesine hoş geldiniz. Burada çeşitli içeriklere erişebilir, giriş yapabilir veya kayıt olabilirsiniz.</p>
      </div>
   <?php else : ?>
      <!-- Giriş yapmış kullanıcılar için içerik -->
      <div class="alert alert-success text-center">
         <h5 class="card-title"><i class="fas fa-user-check"></i> Hoş geldiniz, <?php echo htmlspecialchars($kullanici_adi); ?></h5>
         <p>Sisteme başarıyla giriş yaptınız. Özel içeriklere erişebilirsiniz.</p>
         <div class="btn-group" role="group">
            <!-- Buraya özel butonlar veya linkler ekleyebilirsin -->
         </div> 
      </div>
   <?php endif;?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>