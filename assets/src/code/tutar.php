   <div class="payment-form">
      <form id="payment-form" method="POST" action="satisi_tamamla.php">
         <div class="form-row">
            <div class="payment-right mt-4">
               <input type="hidden" name="total_amount" value="<?php echo $total; ?>">

               <!-- Alınan Tutar -->
               <div class="form-group">
                  <label for="received_amount">Alınan Tutar (TL):</label>
                  <input type="number" step="0.01" name="received_amount" id="received_amount" class="form-control" onkeyup="calculateChange()" placeholder="Alınan tutarı girin">
               </div>
               <!-- Para Üstü -->
               <div class="form-group mt-3">
                  <label>Para Üstü:</label>
                  <span id="change" class="form-control-plaintext">0.00 TL</span>
               </div>
            </div>
        </div>
      </div>