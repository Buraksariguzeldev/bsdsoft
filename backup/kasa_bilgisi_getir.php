<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

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

// kasa_bilgisi_getir.php dosyasındaki HTML çıktı bölümünü değiştirin
echo '<div class="card shadow-sm mb-4">';
echo '<div class="card-body">';
echo '<h5 class="card-title mb-3">Kasa Seçimi</h5>';
echo '<div class="d-flex flex-wrap gap-2 mb-3">';

foreach ($cash_registers as $cash_register) {
    $isSelected = ($selected_cash_register == $cash_register['id']) ? 'active' : '';
    echo '<button type="button" 
          onclick="changeCashRegister(' . $cash_register['id'] . ')" 
          class="btn ' . ($isSelected ? 'btn-primary' : 'btn-outline-primary') . '">
          ' . htmlspecialchars($cash_register['register_name']) . '
          </button>';
}

echo '</div>';
echo '<div class="alert alert-info mb-0">';
echo 'Seçili Kasa: <strong>';
if ($selected_cash_register) {
    $register_name = $cash_registers[array_search($selected_cash_register, array_column($cash_registers, 'id'))]['register_name'];
    echo htmlspecialchars($register_name);
} else {
    echo 'Kasa seçilmedi';
}
echo '</strong>';
echo '</div>';
echo '</div>';
echo '</div>';

?>
