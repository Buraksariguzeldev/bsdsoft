<?php
// ROOT ve PATH tanımları
if (!defined('ROOT')) {
    define('ROOT', $_SERVER['DOCUMENT_ROOT']);
}
if (!function_exists('site_url')) {
    function site_url($yol = '') {
        $server_name = $_SERVER['SERVER_NAME'];
        $base_url = ($server_name == 'localhost' || $server_name == 'bsdsoft.wuaze.com') ? '/' : '';
        return $base_url . ltrim($yol, '/');
    }
}
if (!function_exists('dosya_yolu')) {
    function dosya_yolu($yol = '') {
        return rtrim(ROOT, '/') . '/' . ltrim($yol, '/');
    }
}
?>