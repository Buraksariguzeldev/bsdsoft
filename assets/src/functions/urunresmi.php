<div class="bg-secondary p-3 mb-3">  
    <i class="bi bi-image"></i>  
    <label for="image" class="form-label">Ürün Resmi:</label>  
    <input type="file" name="image" id="image" class="form-control" accept="image/*"  
           onchange="showImagePreview(event)">  
</div>  <?php   
$imagePath = isset($product['image_path']) ? $product['image_path'] : '';   
?>  <?php if (!empty($imagePath) && file_exists($imagePath)): ?>  <div class="mb-3">  
    <label>Mevcut Resim:</label>  
    <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') ?>"   
         alt="Mevcut Resim" class="img-thumbnail"   
         style="width: 100px; height: 100px;">  
</div>

<?php endif; ?>  <!-- Resim önizleme alanı -->  <div class="mb-3" id="image-preview-container" style="display: none;">  
    <label>Seçilen Resim:</label>  
    <img id="image-preview" src="" alt="Seçilen Resim" class="img-thumbnail"   
         style="width: 100px; height: 100px;">  
</div>  <script>  
// Mevcut resmin önizlemesi, sayfa yüklendiğinde hemen görünsün  
window.onload = function() {  
    const existingImagePath = "<?= htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8') ?>";  
      
    if (existingImagePath.trim() !== '') {  
        const previewContainer = document.getElementById('image-preview-container');  
        const imagePreview = document.getElementById('image-preview');  
        imagePreview.src = existingImagePath;  
        previewContainer.style.display = 'block'; // Önizlemeyi göster  
    }  
};  
  
// Resim önizlemesini göstermek için JavaScript fonksiyonu  
function showImagePreview(event) {  
    const file = event.target.files[0];  
    const previewContainer = document.getElementById('image-preview-container');  
    const imagePreview = document.getElementById('image-preview');  
  
    if (file) {  
        const reader = new FileReader();  
        reader.onload = function(e) {  
            imagePreview.src = e.target.result;  
            previewContainer.style.display = 'block'; // Önizlemeyi göster  
        }  
        reader.readAsDataURL(file);  
    } else {  
        previewContainer.style.display = 'none'; // Resim yoksa önizlemeyi gizle  
    }  
}  
</script> 