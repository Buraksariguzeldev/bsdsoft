<tr data-id="<?= $product['id'] ?>">
    <!-- diğer sütunlar aynı kalacak -->
    <td class="text-start">
        <a href="#" class="text-decoration-none fw-bold" onclick="toggleMenu('menu_<?= $product['id'] ?>'); return false;">
            <?= htmlspecialchars($product['product_name']) ?>
        </a>
        <div id="menu_<?= $product['id'] ?>" class="bg-light border rounded p-2 mt-1 d-none">
            <a href="urun_duzenle.php?id=<?= $product['id'] ?>" class="d-block text-primary">  
                <i class="bi bi-pencil-square"></i> Düzenle  
            </a>  
            <hr class="my-1">
            <a href="#" class="d-block text-danger" onclick="urunSil(<?= $product['id'] ?>, event)">  
                <i class="bi bi-trash"></i> Sil  
            </a>  
        </div>
    </td>
    <!-- diğer sütunlar aynı kalacak -->
</tr>
