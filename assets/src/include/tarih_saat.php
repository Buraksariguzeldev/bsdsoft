<?php
date_default_timezone_set('Europe/Istanbul'); // İstanbul saat dilimini ayarla

// Çıktıyı yakalamaya başla
ob_start();

// Sayfa işlemi bittikten sonra içeriği alıp düzenle
register_shutdown_function(function () {
    $icerik = ob_get_clean();

    // YYYY-MM-DD → DD Ay YYYY formatına çevir
    $icerik = preg_replace_callback('/\b(\d{4})-(\d{2})-(\d{2})\b/', function ($eslesme) {
        $aylar = [
            "01" => "Ocak", "02" => "Şubat", "03" => "Mart", "04" => "Nisan",
            "05" => "Mayıs", "06" => "Haziran", "07" => "Temmuz", "08" => "Ağustos",
            "09" => "Eylül", "10" => "Ekim", "11" => "Kasım", "12" => "Aralık"
        ];
        return $eslesme[3] . ' ' . $aylar[$eslesme[2]] . ' ' . $eslesme[1];
    }, $icerik);

    // "TL" veya "tl" → "₺" dönüşümü
    $icerik = preg_replace('/\bTL\b|\btl\b/', '₺', $icerik);

    // Düzenlenmiş içeriği ekrana bas
    echo $icerik;
});
?>