<div class="bg-secondary p-3">
<div class="bg-secondary p-3">
  <label class="fw-bold d-flex align-items-center mb-2" for="barcode">
    <i class="bi bi-upc-scan me-2"></i> Barkod:
  </label>

  <div class="input-group">
    <input type="text" name="barcode" id="barcode"
      class="form-control form-control-lg"
      placeholder="Barkodu okutun veya girin..." required autocomplete="off">
    
    <button type="button" class="btn btn-primary p-2" onclick="openBarcodeScanner()">
      <i class="bi bi-camera"></i>
    </button>
  </div>
</div>