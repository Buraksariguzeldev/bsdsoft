<?php
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/php/return_to.php');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 sayfa bulunamadı</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: sans-serif;
        }
        .error-container {
            text-align: center;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .btn {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
 </head>
><body>
    <div class="error-container">
            <h1><i class="fas fa-exclamation-triangle"></i>Oops! 404</h1>
            <p>Üzgünüz, aradığınız sayfayı bulamıyoruz!</p>
  <?php if (isset($_SESSION['previous_page'])): ?>
        <a href="<?php echo $_SESSION['previous_page']; ?>" class="btn btn-link mt-3 d-block text-center">
            <i class="fas fa-arrow-left me-2"></i> Geri Dön
        </a></p>
    <?php endif; ?>
   </div>
</body>
</html>