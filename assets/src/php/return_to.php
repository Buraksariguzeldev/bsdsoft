<?php
session_start();

// Sadece GET isteklerinde yönlendirme kaydedelim
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
}
?>