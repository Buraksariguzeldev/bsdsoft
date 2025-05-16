<?php
include('../php/kullanici_adi.php');

// Ensure $kullanici_adi is defined
if (!isset($kullanici_adi)) {
    $kullanici_adi = ''; // or null, depending on how you want to handle it
}

include 'siteurl.php';
include_once('musterifont.php');
include 'header.php';
include 'styles.php';
include 'fontheader.php';
include 'tarih_saat.php';
include 'degisken.php' ;


?>

<!DOCTYPE html>
<html lang="tr">
<head>

</head>
<body>

   <div id="bsd-main">
      <span class="bsd-openbtn" onclick="toggleNav()">&#9776;</span>

      <div class="bsd-middle-section">
         <a href="<?php echo site_url(); ?>">
            <img src="<?php echo site_url('img/bsd_soft.png'); ?>" alt="Logo" class="bsd-logo">
         </a>
      </div>

      <div class="bsd-right-section">
         <?php if (empty($kullanici_adi)): ?>
         <a href="<?php echo site_url('auth/uyeislemleri/girisyap.php'); ?>" class="bsd-login-icon"><i class="fas fa-user"></i></a>
         <?php else : ?>
         <a href="<?php echo site_url('auth/uyeislemleri/cikisyap.php'); ?>" class="bsd-logout-icon"><i class="fas fa-sign-out-alt"></i></a>
         <?php endif; ?>
      </div>
   </div>

   <div id="bsd-mySidebar" class="bsd-sidebar">
      <span class="bsd-closebtn" onclick="closeNav()">&times;</span>

      <img src="<?php echo site_url('img/bsd_soft.png'); ?>" alt="Sidebar Logo" class="bsd-sidebar-logo">

      <div class="bsd-datetime" id="bsd-datetime"></div>






      <?php if (!empty($kullanici_adi)): ?>
      <div class="bsd-welcome">
         Hoş geldiniz,
         <span class="bsd-hys-kullanici-adi" style="color: <?= isset($kullanici_color) ? $kullanici_color : 'inherit' ?>;">
            <?php echo htmlspecialchars($kullanici_adi); ?>
         </span>
      </div>
      <?php


      ?>

      <?php endif; ?>

<?php
      $kullanici_adi = isset($kullanici_adi) ? $kullanici_adi : '';
      $current_page = isset($current_page) ? $current_page : 'ana_sayfa';

     

      $show_menu = !in_array($current_page, ['giris', 'kayit', 'sifre_sifirlama']);

      $menu_items = [];


  include "menu_item.php" ;

      if (isset($additional_menu_items) && is_array($additional_menu_items)) {
         $menu_items = array_merge($menu_items, $additional_menu_items);
      }

      if ($menu_items):
      foreach ($menu_items as $key => $item):
      echo '<div class="bsd-menu-item">';
      echo '<a href="' . ($item['url'] !== '#' ? site_url($item['url']) : '#') . '" class="bsd-navlink1">';
      echo '<span class="bsd-menu-icon"><i class="' . $item['icon'] . '"></i></span>';
      echo $item['text'];
      if (isset($item['submenu'])) {
         echo '<span class="bsd-submenu-toggle"><i class="fas fa-chevron-down"></i></span>';
      }
      echo '</a>';
      if (isset($item['submenu'])):
      echo '<div class="bsd-submenu">';
      foreach ($item['submenu'] as $subitem):
      echo '<a href="' . site_url($subitem['url']) . '" class="bsd-navlink1">';
      echo '<span class="bsd-menu-icon"><i class="' . $subitem['icon'] . '"></i></span>';
      echo $subitem['text'] . '</a>';
      endforeach;
      echo '</div>';
      endif;
      echo '</div>';
      endforeach;
      endif;
      ?>
   </div>

   <div class="bsd-content">
    
<?php
$path = $_SERVER['PHP_SELF'];

// Eğer yol içinde "/auth/" veya "/out/" geçmiyorsa ve dosya index.php değilse, include yapılır
if (
    strpos($path, '/auth/') === false &&
    strpos($path, '/out/') === false &&
    basename($path) !== 'index.php'
) {
    include('giriskontrol.php');
}
?>

      <!-- Sayfa içeriği buraya gelecek -->
   </div>

   <?php

   include 'script.php';
   ?>

</body>
</html>