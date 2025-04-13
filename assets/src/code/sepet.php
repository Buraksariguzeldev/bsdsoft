<?php
include($_SERVER['DOCUMENT_ROOT'] . '/ajax/sepet_guncelle.php');



// Bu dosya satis_paneli.php içinde include ediliyor.
// Sepet verisinin $_SESSION['sepet'] içinde olduğunu varsayalım.
$sepet = isset($_SESSION['sepet']) ? $_SESSION['sepet'] : [];
$sepetteUrunVar = !empty($sepet); // Sepetin dolu olup olmadığını kontrol et
?>
<style>
.sepet-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
}

.sepet-table th, .sepet-table td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.sepet-table th {
    background-color: #f2f2f2;
}

.sepet-table img {
    max-width: 50px;
    max-height: 50px;
    margin-right: 10px;
    vertical-align: middle;
}

.sepet-table .urun-adi {
    font-weight: bold;
}

.sepet-table .urun-fiyat {
    color: #007bff;
}

.sepet-table .urun-adet {
    text-align: center;
}

.sepet-table .urun-toplam {
    font-weight: bold;
}

.sepet-table .btn-islem {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
}
.sepet-guncelle-btn {
            background-color: #007bff; /* Example: Blue */
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

</style>

<div class="card shadow-sm mb-4" id="sepet-alani">
    <div class="card-header bg-light py-3">
        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i>Alışveriş Sepeti</h5>
    </div>
    <div class="card-body p-3">
    <?php if ($sepetteUrunVar):
    ?>
        <table class="sepet-table">
            <thead>
                <tr>
                    <th>Ürün</th>
                    <th>Fiyat</th>
                    <th>Adet</th>
                    <th>Toplam</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $toplamTutar = 0; // Sepet toplam tutarı için değişken
                foreach ($sepet as $urunId => $urun):
                    $toplam = $urun['fiyat'] * $urun['miktar']; // Ürün toplam fiyatı
                    $toplamTutar += $toplam;
                ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($urun['resim'] ?? 'default_resim.jpg'); ?>" alt="<?php echo htmlspecialchars($urun['isim']); ?>">
                            <span class="urun-adi"><?php echo htmlspecialchars($urun['isim']); ?></span>
                        </td>
                        <td class="urun-fiyat"><?php echo number_format($urun['fiyat'], 2); ?> TL</td>
                        <td class="urun-adet">
                        <button class="sepet-guncelle-btn" onclick="guncelleSepet('azalt', <?php echo $urunId; ?>)">-</button>
                        <?php echo $urun['miktar']; ?>
                        <button  class="sepet-guncelle-btn" onclick="guncelleSepet( 'arttir', <?php echo $urunId; ?>)">+</button>

                        </td>
                        <td class="urun-toplam"><?php echo number_format($toplam, 2); ?> TL</td>
                        <td><button class="btn-islem" onclick="guncelleSepet( 'sil', <?php echo $urunId; ?>)">Sil</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;">Sepet Toplamı:</td>
                    <td class="urun-toplam"><?php echo number_format($toplamTutar, 2); ?> TL</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <?php else:
        echo "Sepet Boş";
        endif;?>
    </div>
</div>



<script>
function guncelleSepet(islem, urunId) {
    // AJAX isteği gönderme
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/sepet_guncelle.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                // Sepeti yeniden yükle veya arayüzü güncelle
                document.getElementById("sepet-alani").innerHTML = response.sepet;
                //toplam tutari da guncelle

            } else {
                alert("Sepet güncellenirken bir hata oluştu: " + response.message);
            }
        } else {
            alert("Sepet güncellenirken bir hata oluştu. Durum kodu: " + xhr.status);
        }
    };
    xhr.onerror = function() {
        alert("Sepet güncellenirken bir ağ hatası oluştu.");
    };
    xhr.send("islem=" + islem + "&urun_id=" + urunId);
}
</script>