<style>
.payment-section {
    margin-top: 20px;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.payment-section h3 {
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-weight: bold;
    color: #555;
}

.form-control {
    border-radius: 5px;
    border: 1px solid #ccc;
    padding: 10px;
    width: 100%;
    box-sizing: border-box;
    font-size: 16px;
}

.form-control:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 5px;
}

.total-amount {
    font-size: 24px;
    font-weight: bold;
    text-align: center;
}

.para-ustu {
    font-size: 20px;
    text-align: center;
    margin-top: 15px;
}

.para-ustu span {
    font-weight: bold;
    color: #28a745; /* Yeşil renk */
}
</style>

<div class="payment-section">
    <h3>Ödeme Bilgileri</h3>
    <div class="alert alert-info">
        <div class="total-amount">
            Toplam Tutar: <?php echo number_format($total, 2); ?> TL
        </div>
    </div>

    <div class="form-group">
        <label for="received_amount">Alınan Tutar (TL):</label>
        <input type="number" step="0.01" class="form-control" id="received_amount" placeholder="Alınan tutarı girin">
    </div>

    <div class="para-ustu">
        Para Üstü: <span id="change">0.00 TL</span>
    </div>
</div>

<script>
function calculateChange() {
    var totalAmount = <?php echo $total; ?>;
    var receivedAmount = document.getElementById("received_amount").value;
    var change = receivedAmount - totalAmount;

    if (change >= 0) {
        document.getElementById("change").innerText = change.toFixed(2) + " TL";
    } else {
        document.getElementById("change").innerText = "Eksik Ödeme";
    }
}
</script>