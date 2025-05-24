<?php
session_start();
$rol_kontrol_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/rol_kontrol.php';

if (file_exists($rol_kontrol_path)) {
    include($rol_kontrol_path);
    if (function_exists('rol_kontrol')) {
        rol_kontrol(1);
    }
}

// Sayfa içeriği buradan devam eder...
?>

<?php


include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

?>

<!DOCTYPE html>
<html lang="tr">
<head>

    <title>Klasör ve Dosya Oluştur</title>

</head>
<body>

    <?php
    $baseDirectory = $_SERVER['DOCUMENT_ROOT'];

    $message = '';
    $messageClass = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dir = $_POST['dir'];
        $type = $_POST['type'];
        $name = $_POST['name'];

        $fullPath = $baseDirectory . '/' . trim($dir, '/') . '/' . $name;

        if ($type === 'folder') {
            if (!file_exists($fullPath)) {
                if (mkdir($fullPath, 0777, true)) {
                    $message = "Klasör başarıyla oluşturuldu: $fullPath";
                    $messageClass = 'success';
                } else {
                    $message = "Klasör oluşturulurken bir hata oluştu: $fullPath";
                    $messageClass = 'error';
                }
            } else {
                $message = "Bu klasör zaten mevcut: $fullPath";
                $messageClass = 'error';
            }
        } elseif ($type === 'file') {
            if (!file_exists($fullPath)) {
                if (file_put_contents($fullPath, '') !== false) {
                    $message = "Dosya başarıyla oluşturuldu: $fullPath";
                    $messageClass = 'success';
                } else {
                    $message = "Dosya oluşturulurken bir hata oluştu: $fullPath";
                    $messageClass = 'error';
                }
            } else {
                $message = "Bu dosya zaten mevcut: $fullPath";
                $messageClass = 'error';
            }
        }
    }
    ?>

    <h5><i class="fas fa-folder-plus"></i> Klasör ve Dosya Oluştur</h5>
    <br>
<form method="post" class="p-3 bg-light rounded shadow-sm">
    <div class="mb-3">
        <label for="dir" class="form-label fw-bold">
            <i class="fas fa-folder me-2"></i> Dizin Yolu:
        </label>
        <input type="text" name="dir" id="dir" class="form-control" placeholder="Dizin yolu" required>
    </div>

    <div class="mb-3">
        <label for="type" class="form-label fw-bold">
            <i class="fas fa-file-alt me-2"></i> Oluşturulacak Tip:
        </label>
        <select name="type" id="type" class="form-select" required>
            <option value="folder">Klasör</option>
            <option value="file">Dosya</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label fw-bold">
            <i class="fas fa-tag me-2"></i> İsim:
        </label>
        <input type="text" name="name" id="name" class="form-control" placeholder="İsim" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">
        <i class="fas fa-plus"></i> Oluştur
    </button>
</form>
    <hr>

    <?php if ($message): ?>
        <p class="<?php echo $messageClass; ?>">
            <i class="<?php echo $messageClass === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    
    <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>
    
</body>
</html>
