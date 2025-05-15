<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/php/return_to.php');
?>
<!DOCTYPE html>
<html lang="tr">
<head>

    <title>500 - Sunucu Hatası</title>
  
</head>
<body>
    <div class="container">
        <div class="error-container">
            <h1><i class="fas fa-server"></i> 500</h1>
            <p>Sunucu tarafında bir hata oluştu. Lütfen daha sonra tekrar deneyin.</p>
            <?php if (isset($_SESSION['previous_page'])): ?>
                <a href="<?php echo $_SESSION['previous_page']; ?>" class="btn btn-link mt-3 d-block text-center">
                    <i class="fas fa-arrow-left me-2"></i> Geri Dön
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>