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
    max-width: 75px;
    max-height: 75px;
    margin-right: 10px;
    vertical-align: middle;
    border-radius: 5px; /* Köşeleri yuvarlak yapar */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Hafif gölge ekler */
}

.sepet-table .urun-adi {
    font-weight: bold;
    color: #333; /* Daha okunaklı bir renk */
}

.sepet-table .urun-fiyat {
    color: #28a745; /* Yeşil renk */
    font-weight: 500;
}

.sepet-table .urun-adet {
    text-align: center;
}

.sepet-table .urun-toplam {
    font-weight: bold;
    color: #17a2b8; /* Mavi-yeşil renk */
}

.sepet-table .btn-islem {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 8px 12px; /* Daha iyi boyutlandırma */
    cursor: pointer;
    border-radius: 5px; /* Köşeleri yuvarlak yapar */
    transition: background-color 0.3s ease; /* Hover efekti için geçiş */
}

.sepet-table .btn-islem:hover {
    background-color: #c82333; /* Koyu kırmızı */
}

.sepet-guncelle-btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 6px 10px;
    cursor: pointer;
    border-radius: 3px;
    transition: background-color 0.3s ease;
    margin: 0 3px; /* Butonlar arası boşluk */
}

.sepet-guncelle-btn:hover {
    background-color: #0056b3;
}


.card-shadow {
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15); /* Genel kart gölgelendirmesi */
}

.card-header-bg {
    background-color: #e9ecef; /* Daha hafif bir başlık arka planı */
    border-bottom: 1px solid #dee2e6;
    padding: 0.75rem 1.25rem;
}

.table-responsive {
    overflow-x: auto;
}

</style>

<div class="card card-shadow mb-4">
    <div class="card-header card-header-bg py-3">
        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i>Alışveriş Sepeti</h5>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
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
                    $total = 0;
                    if (isset($_SESSION['carts'][$selected_cash_register])) {
                        foreach ($_SESSION['carts'][$selected_cash_register] as $item) {
                            $urun_toplam = $item['price'] * ($item['is_kg'] ? $item['weight'] : $item['quantity']);
                            $total += $urun_toplam;
                    ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                         <img src="<?php echo htmlspecialchars($item['image'] ?? 'img/default_urun.png'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        <span class="urun-adi"><?php echo htmlspecialchars($item['name']); ?></span>
                                    </div>
                                </td>
                                <td class="urun-fiyat"><?php echo number_format($item['price'], 2); ?> TL</td>
                                <td class="urun-adet">
                                    <?php if ($item['is_kg']) : ?>
                                        <?php echo htmlspecialchars($item['weight']); ?> KG
                                    <?php else : ?>
                                        <button class="sepet-guncelle-btn" onclick="updateProduct(<?php echo $item['id']; ?>, 'decrease')"><i class="fas fa-minus"></i></button>
                                        <?php echo htmlspecialchars($item['quantity']); ?>
                                        <button class="sepet-guncelle-btn" onclick="updateProduct(<?php echo $item['id']; ?>, 'increase')"><i class="fas fa-plus"></i></button>
                                    <?php endif; ?>
                                </td>
                                <td class="urun-toplam"><?php echo number_format($urun_toplam, 2); ?> TL</td>
                                <td>
                                    <button class="btn-islem" onclick="updateProduct(<?php echo $item['id']; ?>, 'remove')"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($_SESSION['carts'][$selected_cash_register])): ?>
        <p class="text-center text-muted">Sepetiniz henüz boş.</p>
        <?php endif; ?>
    </div>
</div>

<div class="payment-section mt-4">
    <div class="total-amount alert alert-info">
        <?php echo "<h3 class='mb-0'>Toplam Tutar: " . number_format($total, 2) . " TL</h3>"; ?>
    </div>
</div>
