<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Oturum süresi doldu veya yetkisiz erişim.']);
    exit;
}

// Kasa değiştirme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cash_register_id'])) {
    $_SESSION['selected_cash_register'] = intval($_POST['cash_register_id']);
    echo json_encode(['success' => true]);
    exit;
}

// Tüm kasaları al
$stmt = $vt->query("SELECT * FROM cash_registers");
$cash_registers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selected_cash_register = $_SESSION['selected_cash_register'] ?? null;

// Müşteri bilgisi alma
// Müşteri bilgisi alma bölümünü değiştir
$customer_name = "";
if (!isset($_SESSION['customers'])) {
    $_SESSION['customers'] = array();
}

$selected_customer = $_SESSION['customers'][$selected_cash_register] ?? null;

// Eğer müşteri ID'si geçerli ve boş değilse, müşteri bilgilerini çek
if (!empty($selected_customer)) {
    $stmt = $vt->prepare("SELECT name FROM customers WHERE id = ?");
    $stmt->execute([$selected_customer]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($customer && isset($customer['name'])) {
        $customer_name = $customer['name'];
    }
}

// Eğer müşteri ID'si geçerli ve boş değilse, müşteri bilgilerini çek
if (!empty($selected_customer) && is_numeric($selected_customer)) {
    $stmt = $vt->prepare("SELECT name FROM customers WHERE id = ?");
    $stmt->execute([$selected_customer]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($customer && isset($customer['name'])) {
        $customer_name = $customer['name'];
    }
}

// **DEBUG**: Hata ayıklamak için bu satırı aç (sadece test sırasında kullan)
# echo '<pre>'; var_dump($_SESSION['customers']); echo '</pre>';

// Seçili kasanın toplam fiyatını hesapla
$total_price = 0;
if (isset($_SESSION['carts'][$selected_cash_register]) && is_array($_SESSION['carts'][$selected_cash_register])) {
    foreach ($_SESSION['carts'][$selected_cash_register] as $item) {
        if (isset($item['price'], $item['is_kg'])) {
            $total_price += $item['is_kg'] ? ($item['price'] * ($item['weight'] ?? 0)) : ($item['price'] * ($item['quantity'] ?? 0));
        }
    }
}

// Kasa bilgisi HTML çıktısı
echo '<div class="card shadow-sm mb-4">';
echo '<div class="card-body">';
echo '<h5 class="card-title mb-3">Kasa Seçimi</h5>';
echo '<div class="d-flex flex-wrap gap-2 mb-3">';

foreach ($cash_registers as $cash_register) {
    $isSelected = ($selected_cash_register == $cash_register['id']) ? 'active' : '';
    echo '<button type="button" 
          onclick="changeCashRegister(' . intval($cash_register['id']) . ')" 
          class="btn ' . ($isSelected ? 'btn-primary' : 'btn-outline-primary') . '">' .
          htmlspecialchars($cash_register['register_name']) . '<br>' .
          '<strong>' . number_format($total_price, 2) . ' TL</strong><br>' .
          (!empty($customer_name) ? '<strong>Müşteri: ' . htmlspecialchars($customer_name) . '</strong>' : 'Müşteri Yok') .
          '</button>';
}

echo '</div>';
echo '</div>';
echo '</div>';
?>