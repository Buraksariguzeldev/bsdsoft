<?php
include($_SERVER['DOCUMENT_ROOT'] .'/assets/src/php/kullanici_adi.php');

include 'siteurl.php';
include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/cssfont.php');
include 'header.php';
include 'styles.php';
include 'fontheader.php';
include 'tarih_saat.php';


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
         <?php if (!$kullanici_adi): ?>
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






      <?php if ($kullanici_adi): ?>
      <div class="bsd-welcome">
         Hoş geldiniz,
         <span class="bsd-hys-kullanici-adi" style="color: <?= $kullanici_color ?>;">
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


      if ($kullanici_adi) {


if ($kullanici_adi)  { 
      $menu_items['site_haritasi'] = ['url' => 'siteharitasi.php',
      'icon' => 'fas fa-sitemap', 'text' => 'Site Haritasi'];

}

         $menu_items['Ürunler'] = [
            'url' => '#',
            'icon' => 'fas fa-boxes',
            'text' => 'Ürunler',
            'submenu' => [
               'Urun Duzenle' => ['url' => 'urunler/urun_ekle.php',
                  'icon' => 'fas
            fa-plus-circle',
                  'text' => 'Ürun ekle'],

               'Ürun listesi' => ['url' => 'urunler/urun_listesi.php',
                  'icon' => 'fas fa-list-ul',
                  'text' => 'urun listesi'],

            ]
         ];

         $menu_items['Ürun Grubu'] = [
            'url' => '#',
            'icon' => 'fas fa-th-large',
            'text' => 'Ürun Grubu',
            'submenu' => [
               'Urun Grubu Ekle' => ['url' => 'urun_gruplari/urun_grubu_ekle.php',
                  'icon' => 'fas
            fa-plus-circle',
                  'text' => 'Ürun Grubu ekle'],

               'Ürun Grubu listesi' => ['url' =>
                  'urun_gruplari/urun_grubu_listesi.php',
                  'icon' => 'fas fa-list-ul',
                  'text' => 'urun Grubu listesi'],

            ]
         ];


         $menu_items['Marka'] = [
            'url' => '#',
            'icon' => 'fas fa-registered',
            'text' => 'Marka',
            'submenu' => [
               'Marka Ekle' => ['url' => 'marka/marka_ekle.php',
                  'icon' => 'fas
            fa-plus-circle',
                  'text' => 'Marka ekle'],

               'Marka' => ['url' =>
                  'marka/marka_listesi.php',
                  'icon' => 'fas fa-list-ul',
                  'text' => 'Marka listesi'],

            ]
         ];
         $menu_items['musteriler'] = [
            'url' => '#',
            'icon' => 'fas fa-users',
            'text' => 'Musteriler',
            'submenu' => [
               'Musteri düzenle' => ['url' => 'musteriler/musteri_ekle.php',
                  'icon' => 'fas
            fa-user-shield',
                  'text' => 'Müsteri ekle'],

               'Musteri görüntüle' => ['url' => 'musteriler/musteriler.php',
                  'icon' => 'fas fa-user-edit',
                  'text' => 'Musteri listesi'],

            ]
         ];

         $menu_items['Kasalar'] = [
            'url' => '#',
            'icon' => 'fas fa-money-check-alt',
            'text' => 'Kasalar',
            'submenu' => [
               'Kasa görüntüle' => ['url' =>
                  'kasa_yonetim/kasa_listesi.php',
                  'icon' => 'fas fa-university',
                  'text' => 'Kasa Görüntüleme'],
            ]
         ];


         $menu_items['Satis Yap'] = [
            'url' => '#',
            'icon' => 'fas fa-shopping-cart',
            'text' => 'Satis Yap',
            'submenu' => [
               'Satis Ekrani' => ['url' => 'satis_yonetimi/satis_paneli.php',
                  'icon' =>
                  'fas fa-cash-register',
                  'text' => 'Satis Ekrani'],

               'Satis Listesi' => ['url' => 'satislar/satis_listesi.php',
                  'icon' => 'fa fa-list-alt',
                  'text' => 'Satis Listesi'],

               'Satis Raporu' => ['url' => 'satislar/satis_raporu.php',
                  'icon' => 'fa fa-chart-line',
                  'text' => 'Satis Raporu'],

               'Urun Satisi' => ['url' => '/satislar/urun_raporlari.php',
                  'icon' => 'bi bi-graph-up',
                  'text' => 'Urun Raporu'],

               'Satis Takip' => ['url' => 'satislar/satis_takip.php',
                  'icon' => 'fa fa-clipboard-list',
                  'text' => 'Satis Takip'],
            ]
         ];


         $menu_items['Güncelleme'] = [
            'url' => '#',
            'icon' => 'fas fa-sync-alt',
            'text' => 'Güncelleme',
            'submenu' => [
               'Fiyat değişikliği' => ['url' => 'update/hizli_fiyat_guncelle.php',
                  'icon' => 'fas
            fa-tag',
                  'text' => 'Fiyat Değişikliği'],

               'Resim Düzenle' => ['url' => 'update/görsel_yonetimi.php',
                  'icon' => 'fas fa-edit',
                  'text' => 'Resim düzenle'],

               'Hizli grup' => ['url' =>
                  'update/urun_navigasyon_guncelle.php',
                  'icon' => 'fas fa-edit',
                  'text' => 'Hizli Gruplandir'],

            ]
         ];

         $menu_items['modules'] = [
            'url' => '#',
            'icon' => 'fas fa-th',
            'text' => 'Modules',
            'submenu' => [
               'Modules php' => ['url' => 'modules/modules.php',
                  'icon' => 'fas
            fa-cogs',
                  'text' => 'Modules'],

               'Data' => ['url' => 'modules/data/data.php',
                  'icon' => 'fas fa-database',
                  'text' => 'Data'],

            ]
         ];
      }



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
      <!-- Sayfa içeriği buraya gelecek -->
   </div>

   <?php

   include 'script.php';
   ?>

</body>
</html>