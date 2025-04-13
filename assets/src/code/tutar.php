<?php
// Bu dosya satis_paneli.php içinde include ediliyor.
// Sepet verisinin $_SESSION['sepet'] içinde olduğunu varsayalım.
$sepet = isset($_SESSION['sepet']) ? $_SESSION['sepet'] : [];
$toplam_tutar = 0;

// Toplam tutarı hesapla
foreach ($sepet as $urun_id => $urun_detay) {
    if (isset($urun_detay['fiyat']) && isset($urun_detay['miktar'])) {
        $toplam_tutar += $urun_detay['fiyat'] * $urun_detay['miktar'];
    }
}
$toplamTutarFormatted = number_format($toplam_tutar, 2, ',', '.');
$sepetteUrunVar = !empty($sepet);
?>
<div class="card shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="card-title mb-3"><i class="fas fa-calculator me-2 text-success"></i>Ödeme Bilgileri</h5>

        <div class="row mb-3 align-items-center bg-light p-3 rounded">
            <div class="col-md-6">
                <h4 class="mb-0">Ödenecek Tutar:</h4>
            </div>
            <div class="col-md-6 text-md-end">
                <h3 class="mb-0 text-success fw-bold display-5" id="toplam-tutar-deger" style="background-color: #e9ecef; padding: 0.25em 0.5em; border-radius: 0.2em;">
                    <?php echo $toplamTutarFormatted; ?> TL
                </h3>
                <!-- Gizli alan, form gönderimi için toplam tutarı saklar -->
                <input type="hidden" name="total_amount" id="total_amount_hidden" value="<?php echo $toplam_tutar; ?>">
            </div>
        </div>

        <!-- Alınan Tutar ve Para Üstü (Nakit Ödeme İçin) -->
        <div id="nakit-odeme-alani">
            <div class="mb-3">
                <label for="received_amount" class="form-label">Alınan Nakit Tutar:</label>
                <div class="input-group">
                    <input type="number" step="0.01" name="received_amount" id="received_amount" class="form-control form-control-lg" placeholder="0,00" <?php echo !$sepetteUrunVar ? 'disabled' : ''; ?>>
                    <span class="input-group-text">TL</span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Para Üstü:</label>
                <p class="form-control-plaintext fs-5 fw-bold " id="para-ustu-deger"></p>
                 <small id="para-ustu-aciklama" class="text-muted d-none">Müşteriye iade edilecek tutar.</small>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toplamTutarInput = document.getElementById('total_amount_hidden');
    const alinanTutarInput = document.getElementById('received_amount');
    const paraUstuAlani = document.getElementById('para-ustu-deger');
    const paraUstuAciklama = document.getElementById('para-ustu-aciklama');

    function paraUstuHesapla() {
        const toplamTutar = parseFloat(toplamTutarInput.value) || 0;
        const alinanTutar = parseFloat(alinanTutarInput.value) || 0;
        let paraUstu = 0;

        paraUstuAlani.classList.remove('text-danger', 'text-success', 'text-muted'); // Önceki renkleri temizle
        paraUstuAciklama.classList.add('d-none'); // Açıklamayı gizle

        if (alinanTutar >= toplamTutar) {
            paraUstu = alinanTutar - toplamTutar;
            paraUstuAlani.textContent = paraUstu.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' TL';
            paraUstuAlani.classList.add('text-success');
            paraUstuAciklama.textContent = 'Müşteriye iade edilecek tutar.';
            paraUstuAciklama.classList.remove('d-none');
        } else if (alinanTutar > 0) { // Kısmi ödeme yapıldıysa
            let eksikTutar = toplamTutar - alinanTutar;
            paraUstuAlani.textContent = 'Eksik: ' + eksikTutar.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' TL';
            paraUstuAlani.classList.add('text-danger');
            paraUstuAciklama.textContent = 'Müşterinin ödemesi yetersiz.';
            paraUstuAciklama.classList.remove('d-none');
        } else { // Henüz ödeme alınmadıysa
            paraUstuAlani.textContent = '0,00 TL';
            paraUstuAlani.classList.add('text-muted');
        }
    }

    if (alinanTutarInput) {
        alinanTutarInput.addEventListener('input', paraUstuHesapla);
    }

    window.guncelleToplamTutarArayuzu = function(yeniToplamTutarFloat, yeniToplamTutarFormatted) {
        const toplamTutarGosterge = document.getElementById('toplam-tutar-deger');
        if (toplamTutarGosterge && yeniToplamTutarFormatted !== undefined) {
            toplamTutarGosterge.textContent = yeniToplamTutarFormatted + ' TL';
        }
        if (toplamTutarInput && yeniToplamTutarFloat !== undefined) {
            toplamTutarInput.value = yeniToplamTutarFloat;
        }

        if (alinanTutarInput) {
            paraUstuHesapla();
            alinanTutarInput.disabled = yeniToplamTutarFloat <= 0;
        }
    }

    if(alinanTutarInput && alinanTutarInput.value){
        paraUstuHesapla();
    }
});
</script>