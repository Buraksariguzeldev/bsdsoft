<?php

include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

?>

<!DOCTYPE html>
<html lang="tr">
<head>

     <title> Ana Sayfa </title>


</head>
<body>

   
   <div class="container mt-5">

      <?php if (!$kullanici_adi): ?>
      <!-- Giriş yapmamış kullanıcılar için içerik -->
      <div class="alert alert-info text-center">
         <h5><i class="fas fa-home"></i> Hoş Geldiniz </h5>
         <p>
            bsdsoft.wuaze.com sitesine hoş geldiniz. Burada çeşitli içeriklere erişebilir, giriş yapabilir veya kayıt olabilirsiniz.
         </p>
      </div>
      <?php else : ?>

      <!-- Giriş yapmış kullanıcılar için içerik -->
      <div class="alert alert-success text-center">
         <h5 class="card-title"><i class="fas fa-user-check"></i> Hoş geldiniz, <?php echo htmlspecialchars($kullanici_adi); ?></h5>
         <p>
            Sisteme başarıyla giriş yaptınız. Özel içeriklere erişebilirsiniz.
         </p>
         <div class="btn-group" role="group">


         </div>
      </div>
      
      
      
      
      <?php 
      
      
      include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/widget/satiswg.php');
      
      
      
      endif; ?>



   </div>

</body>
</html>