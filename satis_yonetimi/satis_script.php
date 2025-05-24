<script>
   function addQuickProduct(barcode) {
      var form = document.createElement('form');
      form.method = 'POST';

      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'barcode';
      input.value = barcode;

      form.appendChild(input);
      document.body.appendChild(form);
      form.submit();
   }

   document.getElementById('barcode-form').addEventListener('submit', function(e) {
      e.preventDefault();
      this.submit();
   });

   document.getElementById('barcode').focus();

   function openBarcodeScanner(event) {
      event.preventDefault();
      window.open('../assets/src/php/camera.php', 'BarcodeScanner', 'width=640,height=480,toolbar=no,statusbar=no,menubar=no');
   }
   function updateProduct(productId, action) {
      var form = document.createElement('form');
      form.method = 'POST';

      var inputId = document.createElement('input');
      inputId.type = 'hidden';
      inputId.name = 'product_id';
      inputId.value = productId;

      var inputAction = document.createElement('input');
      inputAction.type = 'hidden';
      inputAction.name = 'action';
      inputAction.value = action;

      form.appendChild(inputId);
      form.appendChild(inputAction);
      document.body.appendChild(form);
      form.submit();
   }

   function calculateChange() {
      const totalAmount = <?php echo $total; ?>;
      const receivedAmount = parseFloat(document.getElementById('received_amount').value) || 0;
      const change = receivedAmount - totalAmount;
      document.getElementById('change').textContent = change.toFixed(2) + ' TL';
   }

   // Mevcut JavaScript kodunun sonuna ekle
   document.getElementById('product_name').addEventListener('input', function(e) {
      const searchValue = e.target.value;
      if (searchValue.length < 2) {
         document.getElementById('search-results').style.display = 'none';
         return;
      }

      const formData = new FormData();
      formData.append('product_name', searchValue);

      fetch('satis_paneli.php', {
         method: 'POST',
         body: formData,
         headers: {
            'X-Requested-With': 'XMLHttpRequest'
         }
      })
      .then(response => response.json())
      .then(data => {
         const resultsDiv = document.getElementById('search-results');
         resultsDiv.innerHTML = '';

         if (data.length > 0) {
            resultsDiv.style.display = 'block';
            data.forEach(product => {
               const div = document.createElement('div');
               div.style.padding = '10px';
               div.style.borderBottom = '1px solid #ddd';
               div.style.cursor = 'pointer';
               div.innerHTML = `${product.product_name} - ${product.sale_price} TL`;

               div.addEventListener('mouseenter', () => {
                  div.style.backgroundColor = '#f0f0f0';
               });

               div.addEventListener('mouseleave', () => {
                  div.style.backgroundColor = 'white';
               });

               div.onclick = function() {
                  document.getElementById('barcode').value = product.barcode;
                  document.getElementById('barcode-form').submit();
               };

               resultsDiv.appendChild(div);
            });
         } else {
            resultsDiv.style.display = 'none';
         }
      });
   });
   function openNewCustomerForm() {
      // Yeni müşteri formunu açacak popup penceresi
      window.open('../müşteriler/musteri_ekle.php', 'NewCustomer', 'width=500,height=600,toolbar=no,statusbar=no,menubar=no');
   }

   // Sayfa yüklendiğinde ve müşteri seçimi değiştiğinde kontrol et
   document.addEventListener('DOMContentLoaded', updateButtonVisibility);
   document.getElementById('customer_id').addEventListener('change', updateButtonVisibility);

   // Bu kısmı bulun ve aşağıdakiyle değiştirin
   function updateButtonVisibility() {
      const customerSelect = document.getElementById('customer_id');
      const completeSaleBtn = document.getElementById('complete-sale-btn');
      const customerSaleBtn = document.getElementById('customer-sale-btn');

      const total = <?php echo $total; ?>;

      if (total <= 0) {
         completeSaleBtn.style.display = 'none';
         customerSaleBtn.style.display = 'none';
         return;
      }

      if (customerSelect.value) {
         completeSaleBtn.style.display = 'none';
         customerSaleBtn.style.display = 'block';
      } else {
         completeSaleBtn.style.display = 'block';
         customerSaleBtn.style.display = 'none';
      }
   }

   // Müşteri seçimi değiştiğinde kaydet
   document.getElementById('customer_id').addEventListener('change', function () {
      const selectedCashRegister = "<?php echo $_SESSION['selected_cash_register']; ?>"; // Seçilen kasa
      const selectedCustomer = this.value; // Seçilen müşteri

      // Fetch ile musteri_kaydet.php'ye gönder
      fetch('musteri_kaydet.php', {
         method: 'POST',
         headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
         },
         body: `customer_id=${selectedCustomer}&cash_register=${selectedCashRegister}`,
      }).then(response => response.json())
      .then(data => {
         if (data.success) {
            console.log("Müşteri kaydedildi!");
         } else {
            console.error("Müşteri kaydedilirken bir hata oluştu:", data.message);
         }
      });
   });
   // Kasa bilgilerini yükle
   function loadCashRegisterInfo() {
      fetch('kasa_bilgisi_getir.php')
      .then(response => response.text())
      .then(data => {
         document.getElementById('cash-register-info').innerHTML = data;
      });
   }

   // Sayfa yüklendiğinde kasa bilgilerini getir
   document.addEventListener('DOMContentLoaded', loadCashRegisterInfo);

   // satis_paneli.php dosyasındaki script tagları içine ekleyin
   function changeCashRegister(registerId) {
      fetch('kasa_bilgisi_getir.php', {
         method: 'POST',
         headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
         },
         body: `cash_register_id=${registerId}`
      })
      .then(response => response.json())
      .then(data => {
         if (data.success) {
            window.location.reload();
         }
      });
   }

</script>