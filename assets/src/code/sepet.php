<?php
// Bu dosya satis_paneli.php içinde include ediliyor.
// Sepet verisinin $_SESSION['sepet'] içinde olduğunu varsayalım.
$sepet = isset($_SESSION['sepet']) ? $_SESSION['sepet'] : [];
$sepetteUrunVar = !empty($sepet); // Sepetin dolu olup olmadığını kontrol et
?>
<div class="card shadow-sm mb-4" id="sepet-alani">
    <div class="card-header bg-light py-3">
        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i>Alışveriş Sepeti</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-borderless table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="ps-3" style="width: 40%;">Ürün</th>
                        <th scope="col" style="width: 20%; min-width: 100px;">Birim Fiyat</th>
                        <th scope="col" class="text-center" style="width: 15%; min-width: 130px;">Miktar</th>
                        <th scope="col" class="text-end" style="width: 20%;">Ara Toplam</th>
                        <th scope="col" class="text-center pe-3" style="width: 5%;"></th>
                    </tr>
                </thead>
                <tbody id="sepet-tbody">
                    <?php if ($sepetteUrunVar):
                        foreach ($sepet as $urun_id => $urun):
                            $urun_resmi = isset($urun['resim']) && $urun['resim'] ? '/uploads/' . $urun['resim'] : '/img/default_urun.png'; // Varsayılan resim
                    ?>
                        <tr id="sepet-urun-<?php echo $urun_id; ?>">
                            <td class="align-middle ps-3">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo htmlspecialchars($urun_resmi); ?>" alt="<?php echo htmlspecialchars($urun['isim'] ?? 'Ürün'); ?>" class="img-fluid me-2" style="max-width: 50px; max-height: 50px;">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($urun['isim'] ?? 'Bilinmeyen Ürün'); ?></h6>
                                        <?php if (isset($urun['barkod']) && $urun['barkod']): ?>
                                            <small class="text-muted d-block">Barkod: <?php echo htmlspecialchars($urun['barkod']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle"><?php echo number_format($urun['fiyat'] ?? 0, 2, ',', '.'); ?> TL</td>
                            <td class="align-middle text-center">
                                <div class="input-group input-group-sm justify-content-center">
                                    <button class="btn btn-outline-secondary btn-sm miktar-azalt" type="button" data-id="<?php echo $urun_id; ?>"><i class="fas fa-minus"></i></button>
                                    <input type="number" class="form-control form-control-sm text-center miktar-input" value="<?php echo htmlspecialchars($urun['miktar'] ?? 1); ?>" min="1" data-id="<?php echo $urun_id; ?>" style="max-width: 60px;">
                                    <button class="btn btn-outline-secondary btn-sm miktar-arttir" type="button" data-id="<?php echo $urun_id; ?>"><i class="fas fa-plus"></i></button>
                                </div>
                            </td>
                            <td class="align-middle text-end ara-toplam"><?php echo number_format(($urun['fiyat'] ?? 0) * ($urun['miktar'] ?? 0), 2, ',', '.'); ?> TL</td>
                            <td class="align-middle text-center pe-3">
                                <button class="btn btn-danger btn-sm urun-sil" data-id="<?php echo $urun_id; ?>"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    <?php
                        endforeach;
                    else:
                    ?>
                        <tr id="sepet-bos-mesaj">
                            <td colspan="5" class="text-center text-muted p-4">
                                <i class="fas fa-info-circle me-2"></i>Sepetiniz şu anda boş.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sepet güncelleme işlemleri için JavaScript (genellikle satis_script.php içinde olur) -->
<script>
// Bu script genellikle satis_script.php içinde veya ayrı bir JS dosyasında yer alır.
// Burada sadece örnek yapı gösteriliyor.

document.addEventListener('DOMContentLoaded', function() {
    const sepetAlani = document.getElementById('sepet-alani');

    if (sepetAlani) {
        sepetAlani.addEventListener('click', function(e) {
            const target = e.target.closest('button'); // Butona tıklandı mı?
            if (!target) return;

            const urunId = target.dataset.id;

            if (target.classList.contains('miktar-azalt')) {
                guncelleMiktar(urunId, 'azalt');
            } else if (target.classList.contains('miktar-arttir')) {
                guncelleMiktar(urunId, 'arttir');
            } else if (target.classList.contains('urun-sil')) {
                guncelleMiktar(urunId, 'sil');
            }
        });

        sepetAlani.addEventListener('change', function(e) {
            const target = e.target;
            if (target.classList.contains('miktar-input')) {
                const urunId = target.dataset.id;
                const yeniMiktar = parseInt(target.value, 10);
                if (!isNaN(yeniMiktar) && yeniMiktar >= 0) { // 0 girilirse silinmeli
                     guncelleMiktar(urunId, 'ayarla', yeniMiktar);
                } else {
                    // Geçersiz giriş, eski değeri geri yükle (veya hata göster)
                    // Bu kısım için eski değeri saklamak gerekebilir.
                    console.warn("Geçersiz miktar girişi:", target.value);
                }
            }
        });
    }

    function guncelleMiktar(urunId, islemTipi, yeniMiktar = null) {
        console.log(`Ürün ID: ${urunId}, İşlem: ${islemTipi}, Yeni Miktar: ${yeniMiktar}`);
        // Burada AJAX isteği ile sunucu tarafındaki sepet güncellenir.
        // Örnek AJAX isteği (jQuery ile daha kolay olabilir):
        fetch('sepet_guncelle_ajax.php', { // Bu endpoint'in oluşturulması gerekir
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // AJAX olduğunu belirtmek için
            },
            body: JSON.stringify({
                urun_id: urunId,
                islem: islemTipi,
                miktar: yeniMiktar
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                guncelleSepetArayuzu(data.sepetHtml, data.toplamTutarFormatted, data.sepetBos);
                console.log("Sepet güncellendi:", data);
            } else {
                alert('Hata: ' + (data.message || 'Bilinmeyen bir hata oluştu.'));
            }
        })
        .catch(error => {
            console.error('Sepet güncelleme hatası:', error);
            alert('Sepet güncellenirken bir ağ hatası oluştu.');
        });
    }

    function guncelleSepetArayuzu(sepetHtml, toplamTutarFormatted, sepetBos) {
        const tbody = document.getElementById('sepet-tbody');
        const tutarAlani = document.getElementById('toplam-tutar-deger');
        const satisButonlari = document.querySelectorAll('#payment-form button[type="submit"], #musteri-sale-btn');

        if (tbody && sepetHtml !== undefined) {
            tbody.innerHTML = sepetHtml;
        }
        if (tutarAlani && toplamTutarFormatted !== undefined) {
            tutarAlani.textContent = toplamTutarFormatted + ' TL';
        }
        satisButonlari.forEach(btn => {
            btn.disabled = sepetBos;
        });
    }
});
</script>
