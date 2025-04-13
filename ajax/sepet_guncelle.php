<?php
session_start();

// Gerekli dosyaları dahil et
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');

// İşlem türünü ve ürün ID'sini al
$islem = isset($_POST['islem']) ? $_POST['islem'] : null;
$urunId = isset($_POST['urun_id']) ? intval($_POST['urun_id']) : 0;

// Hata mesajı
$response = array('success' => false, 'message' => '');

// Sepeti al veya oluştur
$sepet = isset($_SESSION['sepet']) ? $_SESSION['sepet'] : array();

// İşlemleri gerçekleştir
if ($islem && $urunId > 0) {
    switch ($islem) {
        case 'arttir':
            if (isset($sepet[$urunId])) {
                $sepet[$urunId]['miktar']++;
                $response['message'] = 'Ürün adeti arttırıldı.';
            } else {
                $response['message'] = 'Ürün sepette bulunamadı.';
            }
            break;

        case 'azalt':
            if (isset($sepet[$urunId])) {
                if ($sepet[$urunId]['miktar'] > 1) {
                    $sepet[$urunId]['miktar']--;
                    $response['message'] = 'Ürün adeti azaltıldı.';
                } else {
                    // Adet 1 ise ürünü sil
                    unset($sepet[$urunId]);
                    $response['message'] = 'Ürün sepetten silindi.';
                }
            } else {
                $response['message'] = 'Ürün sepette bulunamadı.';
            }
            break;

        case 'sil':
            if (isset($sepet[$urunId])) {
                unset($sepet[$urunId]);
                $response['message'] = 'Ürün sepetten silindi.';
            } else {
                $response['message'] = 'Ürün sepette bulunamadı.';
            }
            break;

        default:
            $response['message'] = 'Geçersiz işlem.';
            break;
    }

    // Sepeti güncelle
    $_SESSION['sepet'] = $sepet;
    $response['success'] = true;

} else {
    $response['message'] = 'Geçersiz istek.';
}

// Sepet içeriğini yeniden oluştur
ob_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/code/sepet.php');
$sepetHtml = ob_get_clean();

$response['sepet'] = $sepetHtml;

// JSON olarak yanıt döndür
header('Content-Type: application/json');
echo json_encode($response);
?>