function openBarcodeScanner(event) {
    event.preventDefault();
    window.open('../camera.php', 'BarcodeScanner', 'width=640,height=480,toolbar=no,statusbar=no,menubar=no');
}
