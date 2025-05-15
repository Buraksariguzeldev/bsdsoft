<?php

session_start();

try {
   include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/config/vt_baglanti.php');
   $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Kasa değişikliği kontrolü
if (isset($_SESSION['previous_register']) && $_SESSION['previous_register'] !== $_SESSION['selected_cash_register']) {
   // Önceki kasadaki müşteri bilgisini sakla
   if (isset($_SESSION['selected_customer'])) {
      if (!isset($_SESSION['customer_history'])) {
         $_SESSION['customer_history'] = [];
      }
      $_SESSION['customer_history'][$_SESSION['previous_register']] = $_SESSION['selected_customer'];
   }

   // Yeni kasada önceden seçili müşteri var mı kontrol et
   if (isset($_SESSION['customer_history'][$_SESSION['selected_cash_register']])) {
      $_SESSION['selected_customer'] = $_SESSION['customer_history'][$_SESSION['selected_cash_register']];
   } else {
      // Yeni kasada önceden seçili müşteri yoksa müşteriyi temizle
      unset($_SESSION['selected_customer']);
   }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
   $productId = $_POST['product_id'] ?? null;
   $action = $_POST['action'];

   if ($productId && ($action === 'increase' || $action === 'decrease' || $action === 'remove')) {
      updateProductQuantity($productId, $action);
   }

   if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
      echo json_encode(['success' => true]);
      exit;
   }

   header("Location: " . $_SERVER['PHP_SELF']);
   exit();
}

// POST işlemlerini kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barcode'])) {
   $barcode = $_POST['barcode'];
   $product = getProductByBarcode($barcode);

   if ($product) {
      if (addProductToCart($product)) {
         if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode(['success' => true]);
            exit;
         }
      }
   }
   header("Location: " . $_SERVER['PHP_SELF']);
   exit();
}

// POST işlemlerini kontrol eden kısımdan önce ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
   $productName = $_POST['product_name'];
   $stmt = $vt->prepare("SELECT * FROM products WHERE product_name LIKE ? LIMIT 10");
   $stmt->execute(['%' . $productName . '%']);
   $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

   if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
      echo json_encode($results);
      exit;
   }
}

// Seçilen kasa bilgisi
$selected_cash_register = $_SESSION['selected_cash_register'] ?? null;

// Her kasa için ayrı sepet oluşturma
if (!isset($_SESSION['carts'])) {
   $_SESSION['carts'] = array();
}

// getProductByBarcode fonksiyonunu güncelle (PHP dosyasında bu fonksiyonu bul ve değiştir)
function getProductByBarcode($barcode) {
   global $vt;

   // Sadece başta miktar kontrolü yap (1*123456 formatı)
   if (strpos($barcode, '*') !== false) {
      $parts = explode('*', $barcode);
      if (count($parts) == 2 && is_numeric($parts[0])) {
         $quantity = $parts[0];
         $actualBarcode = $parts[1];

         // Ürünü bul
         $stmt = $vt->prepare("SELECT * FROM products WHERE barcode = ?");
         $stmt->execute([$actualBarcode]);
         $product = $stmt->fetch(PDO::FETCH_ASSOC);

         if ($product) {
            if ($product['is_kg']) {
               // KG ürünü ise ağırlığı ayarla
               $_POST['barcode'] = $quantity . '*1';
            } else {
               // Normal ürün ise çoğaltma işlemi yap
               $quantity = intval($quantity);
               for ($i = 1; $i < $quantity; $i++) {
                  addProductToCart($product);
               }
            }
            return $product;
         }
         return false;
      }
   }

   // Normal barkod araması
   $stmt = $vt->prepare("SELECT * FROM products WHERE barcode = ?");
   $stmt->execute([$barcode]);
   return $stmt->fetch(PDO::FETCH_ASSOC);
}


// addProductToCart fonksiyonundan ÖNCE bu fonksiyonu ekleyin
function updateProductQuantity($productId, $operation) {
   $selected_cash_register = $_SESSION['selected_cash_register'];
   $cart = &$_SESSION['carts'][$selected_cash_register];

   if (isset($cart[$productId])) {
      if ($operation === 'increase') {
         $cart[$productId]['quantity']++;
      } else if ($operation === 'decrease' && $cart[$productId]['quantity'] > 1) {
         $cart[$productId]['quantity']--;
      } else if ($operation === 'remove') {
         unset($cart[$productId]);
      }
      return true;
   }
   return false;
}

// Sepete ürün ekleme fonksiyonu
function addProductToCart($product) {
   if (!isset($_SESSION['selected_cash_register']) || empty($_SESSION['selected_cash_register'])) {
      return false;
   }

   $selected_cash_register = $_SESSION['selected_cash_register'];

   if (!isset($_SESSION['carts'][$selected_cash_register])) {
      $_SESSION['carts'][$selected_cash_register] = array();
   }

   $cart = &$_SESSION['carts'][$selected_cash_register];

   // Barkodda * işareti var mı kontrol et
   if (isset($_POST['barcode']) && strpos($_POST['barcode'], '*') !== false) {
      $parts = explode('*', $_POST['barcode']);
      if (count($parts) == 2) {
         // Eğer ürün KG bazlı ise
         if ($product['is_kg']) {
            $weight = floatval($parts[0]);
            $cart[$product['id']] = [
               'id' => $product['id'],
               'name' => $product['product_name'],
               'price' => $product['sale_price'],
               'is_kg' => true,
               'weight' => $weight,
               'quantity' => null
            ];
         } else {
            // Normal adet bazlı ürün
            $quantity = intval($parts[1]);
            if (isset($cart[$product['id']])) {
               $cart[$product['id']]['quantity'] += $quantity;
            } else {
               $cart[$product['id']] = [
                  'id' => $product['id'],
                  'name' => $product['product_name'],
                  'price' => $product['sale_price'],
                  'is_kg' => false,
                  'weight' => null,
                  'quantity' => $quantity
               ];
            }
         }
      }
   } else {
      // Normal ekleme işlemi
      if (isset($cart[$product['id']])) {
         if (!$product['is_kg']) {
            $cart[$product['id']]['quantity']++;
         }
      } else {
         $cart[$product['id']] = [
            'id' => $product['id'],
            'name' => $product['product_name'],
            'price' => $product['sale_price'],
            'is_kg' => $product['is_kg'] ?? false,
            'weight' => null,
            'quantity' => $product['is_kg'] ? null : 1
         ];
      }
   }
   return true;
}

// Hızlı ürünleri getiren fonksiyon
function getQuickProducts() {
   global $vt;
   $stmt = $vt->prepare("SELECT * FROM products WHERE hizli_urun = 1");
   $stmt->execute();
   return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



$_SESSION['previous_register'] = $selected_cash_register;


$css = ['bootstrap.css']; // Sadece Bootstrap

include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');
include('success_message.php');
?>