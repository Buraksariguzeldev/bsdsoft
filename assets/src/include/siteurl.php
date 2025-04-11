<?php
// Daha önce tanımlandı mı kontrol et
if (!function_exists('site_url')) {
    function site_url($yol = '') {
        $server_name = $_SERVER['SERVER_NAME'];
        $base_url = ($server_name == 'localhost' || $server_name == 'bsdsoft.wuaze.com') ? '/' : '';
        return $base_url . ltrim($yol, '/');
    }
}
?>

