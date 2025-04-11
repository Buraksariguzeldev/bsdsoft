<?php
session_start();
try {
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
    $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Tüm kasaları al
$stmt = $vt->query("SELECT * FROM cash_registers");
$cash_registers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kasa seçimi işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cash_register_id'])) {
    $_SESSION['selected_cash_register'] = intval($_POST['cash_register_id']);
    header("Location: satis_paneli.php"); // Yeniden yönlendirme
    exit;
}

$selected_cash_register = $_SESSION['selected_cash_register'] ?? null;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasa Seçimi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h3>Kasa Seçimi</h3>
    <form method="post" action="">
        <div class="d-grid gap-2">
            <?php foreach ($cash_registers as $cash_register): ?>
                <button type="submit" name="cash_register_id" value="<?= htmlspecialchars($cash_register['id']) ?>" class="btn btn-primary">
                    <?= htmlspecialchars($cash_register['register_name']) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </form>
    <p>Seçili Kasa: 
        <?= $selected_cash_register ? htmlspecialchars($cash_registers[array_search($selected_cash_register, array_column($cash_registers, 'id'))]['register_name']) : 'Kasa seçilmedi.' ?>
    </p>
       <a href="dashboard.php">dashboard.php</a>
</div>
</body>
</html>