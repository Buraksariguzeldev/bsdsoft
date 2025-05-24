<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barkod Okuma</title>
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 15px;
            font-family: Arial, sans-serif;
        }
        #interactive-container {
            width: 100%;
            height: calc(100vh - 200px); /* Flaş butonu için biraz daha yer açtık */
            position: relative;
            margin-bottom: 15px;
        }
        #scanner-container {
            width: 100%;
            height: 100%;
            background: #f0f0f0;
            border-radius: 8px;
            overflow: hidden;
        }
        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .button-container {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        button {
            flex: 1;
            padding: 12px;
            background: #ff4444;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #ff0000;
        }
        button.flash {
            background: #4CAF50;
        }
        button.flash:hover {
            background: #45a049;
        }
        button.flash.active {
            background: #ff9800;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Barkod Tarama</h1>
    <div id="interactive-container">
        <div id="scanner-container"></div>
    </div>
    <div class="button-container">
        <button class="flash" onclick="toggleFlash()">Flaş Işığı</button>
        <button onclick="stopScanner()">Tarayıcıyı Durdur</button>
    </div>
    
    <script>
        let track = null;
        let flashOn = false;

        async function toggleFlash() {
            if (!track) return;
            
            try {
                const capabilities = track.getCapabilities();
                if (!capabilities.torch) {
                    alert('Üzgünüz, cihazınızda flaş özelliği bulunmuyor.');
                    return;
                }

                flashOn = !flashOn;
                await track.applyConstraints({
                    advanced: [{ torch: flashOn }]
                });

                const flashButton = document.querySelector('.flash');
                if (flashOn) {
                    flashButton.classList.add('active');
                    flashButton.textContent = 'Flaşı Kapat';
                } else {
                    flashButton.classList.remove('active');
                    flashButton.textContent = 'Flaş Işığı';
                }
            } catch (err) {
                console.error('Flaş kontrolünde hata:', err);
                alert('Flaş kontrolünde bir hata oluştu.');
            }
        }

        function startScanner() {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#scanner-container'),
                    constraints: {
                        facingMode: "environment",
                        width: { min: 450 },
                        height: { min: 300 },
                        aspectRatio: { min: 1, max: 2 }
                    }
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: navigator.hardwareConcurrency || 4,
                decoder: {
                    readers: [
                        "code_128_reader",
                        "ean_reader",
                        "ean_8_reader",
                        "code_39_reader",
                        "upc_reader",
                        "upc_e_reader"
                    ]
                },
                locate: true
            }, function(err) {
                if (err) {
                    console.error("Kamera başlatılırken hata oluştu:", err);
                    alert("Kamera başlatılamadı. Lütfen kamera izinlerini kontrol edin.");
                    return;
                }
                Quagga.start();

                // Kamera akışını al ve track değişkenine ata
                const videoElement = document.querySelector('#scanner-container video');
                if (videoElement) {
                    track = videoElement.srcObject?.getVideoTracks()[0];
                }
            });

            Quagga.onDetected(function(result) {
                if (result && result.codeResult && result.codeResult.code) {
                    var barcode = result.codeResult.code;
                    try {
                        window.opener.document.getElementById('barcode').value = barcode;
                        stopScanner();
                        window.close();
                    } catch (e) {
                        console.error("Barkod aktarılırken hata oluştu:", e);
                        alert("Barkod okundu: " + barcode);
                    }
                }
            });
        }

        function stopScanner() {
            if (flashOn) {
                track?.applyConstraints({
                    advanced: [{ torch: false }]
                });
            }
            Quagga.stop();
        }

        window.addEventListener('load', startScanner);
        window.addEventListener('beforeunload', stopScanner);
    </script>
</body>
</html>
