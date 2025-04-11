<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/assets/src/code/buttons.php"; // Buton dizisini al

// Sayfa adını al
$script_name = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$full_uri = $_SERVER['REQUEST_URI']; // Tam URI (parametrelerle birlikte)

// Buton indexini takip eden statik değişken
static $button_index = [];

// Eğer bu sayfa için ilk kez çağırılıyorsa indexi sıfırla
if (!isset($button_index[$full_uri])) {
    $button_index[$full_uri] = 0;
}

// İlk olarak tam URL ile eşleşmeyi dene (parametreler dahil)
$buttons_for_page = array_values(array_filter($buttons, function ($key) use ($full_uri) {
    return $key === $full_uri;
}, ARRAY_FILTER_USE_KEY));

// Eğer tam URL ile eşleşme bulunamazsa, sadece yol kısmı ile dene
if (empty($buttons_for_page)) {
    $buttons_for_page = array_values(array_filter($buttons, function ($key) use ($script_name) {
        return $key === $script_name;
    }, ARRAY_FILTER_USE_KEY));
}

// Şu anki index ile ilgili buton var mı?
if (!empty($buttons_for_page) && isset($buttons_for_page[$button_index[$full_uri]])) {
    $button = $buttons_for_page[$button_index[$full_uri]];
    ?>
    <button type="submit" class="btn btn-secondary w-100 mb-3">
        <i class="<?= $button['icon'] ?>"></i>
        <?= htmlspecialchars($button['text'], ENT_QUOTES, 'UTF-8') ?>
    </button>
    <?php
    // Her çağrıldığında indexi artır (Böylece sıradaki buton gösterilecek)
    $button_index[$full_uri]++;
}
?>
