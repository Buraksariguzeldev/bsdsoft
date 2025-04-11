<?php
session_start();

// Kullanıcının geldiği sayfayı al
$return_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../../index.php';

// Session'ı temizle
session_destroy();

// Kullanıcıyı geldiği sayfaya yönlendir
header("Location: " . $return_url);
exit;
?>
