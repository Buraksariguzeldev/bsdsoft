<?php
session_start();
$rol_kontrol_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/rol_kontrol.php';

if (file_exists($rol_kontrol_path)) {
    include($rol_kontrol_path);
    if (function_exists('rol_kontrol')) {
        rol_kontrol(1);
    }
}

// Sayfa içeriği buradan devam eder...
?>

<?php


include($_SERVER['DOCUMENT_ROOT'] . '/assets/src/include/navigasyon.php');

?>
<!DOCTYPE html>
<html lang="tr">
<head>

    <title>Buraksariguzeldev Dosya Yöneticisi</title>


</head>
<body>
  

  
    <h5><i class="fas fa-folder-open"></i> Son Değiştirilme Tarihi</h5>
    <br>
       <h2> <i class="fas fa-calendar"></i>Tarihe Göre
        Filtrele:</h2>
    <form method="get" class="filter-form ">
        
      <input class="bsd-takvim1" type="date" id="search_date"
name="search_date" value="<?php echo isset($_GET['search_date']) ?
$_GET['search_date'] : date('Y-m-d'); ?>"></input>
        
        <button class="bsd-btn1" type="submit"><i class="fas fa-filter"></i> Filtrele</button>
        <button class="bsd-btn1" type="submit" name="clear" onclick="clearDate()"><i class="fas fa-calendar-times"></i> Tüm Zamanlar</button>
        <button class="bsd-btn1" type="submit" name="today" onclick="setToday()"><i class="fas fa-calendar-day"></i> Bugün</button>
    </form>

    <?php
    date_default_timezone_set('Europe/Istanbul');
    $baseDirectory = $_SERVER['DOCUMENT_ROOT'];
    $searchDate = isset($_GET['search_date']) && !isset($_GET['clear']) ? $_GET['search_date'] : null;
    if (isset($_GET['today'])) {
        $searchDate = date('Y-m-d');
    }

    $fileList = [];
    $fileCount = 0;
    $folderCount = 0;

    function scanDirectory($dir, &$fileList, &$fileCount, &$folderCount, $searchDate) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $filePath = $dir . "/" . $file;
                $fileMTime = filemtime($filePath);
                
                $isDir = is_dir($filePath);
                if ($searchDate === null || date('Y-m-d', $fileMTime) === $searchDate) {
                    $fileList[] = [
                        'name' => $file,
                        'path' => $filePath,
                        'mtime' => $fileMTime,
                        'is_dir' => $isDir
                    ];
                    if ($isDir) {
                        $folderCount++;
                    } else {
                        $fileCount++;
                    }
                }
                
                if ($isDir) {
                    scanDirectory($filePath, $fileList, $fileCount, $folderCount, $searchDate);
                }
            }
        }
    }

    scanDirectory($baseDirectory, $fileList, $fileCount, $folderCount, $searchDate);

    usort($fileList, function($a, $b) {
        return $b['mtime'] - $a['mtime'];
    });
    ?>
<hr>

<?php // İstatistikler ?>

  <h5><i class="fas fa-chart-bar"></i> İstatistikler</h5>
<div class="stats p-3 border rounded bg-light">
    <span class="bsd-arama1">
        <?php if ($searchDate): ?>
            <i class="fas fa-calendar-check"></i> <?php echo date('d.m.Y', strtotime($searchDate)); ?>
            tarihine ait arama sonuçları:
        <?php else: ?>
            <i class="fas fa-infinity"></i> Tüm zamanlara ait:
        <?php endif; ?>
    </span>
    <p><i class="fas fa-file"></i> Değiştirilen dosya: <?php echo $fileCount; ?></p>
    <p><i class="fas fa-folder"></i> Değiştirilen klasör: <?php echo $folderCount; ?></p>
</div>

<h5><i class="fas fa-list"></i> Dosya ve Klasör Listesi</h5>
<ul class="list-group">
    <?php foreach ($fileList as $file): ?>
        <?php 
            $icon = $file['is_dir'] ? "fa-folder" : "fa-file"; 
            $fileDate = date("d.m.Y", $file['mtime']);
            $fileHour = date("H:i:s", $file['mtime']);
        ?>
        <li class="list-group-item">
            <i class="fas <?php echo $icon; ?> file-icon"></i>
            <span class="file-name fw-bold"><?php echo $file['name']; ?></span>
            
            <div class="file-time text-muted">
                <br>
                <span class="file-date">
                    <i class="far fa-calendar-alt"></i> Son değiştirilme tarihi: 
                    <span class="timestamp" data-time="<?php echo $file['mtime']; ?>"><?php echo $fileDate; ?></span>
                </span>
                <span class="file-hour">
                    <i class="far fa-clock"></i> Son değiştirilme saati: 
                    <span class="timestamp" data-time="<?php echo $file['mtime']; ?>"><?php echo $fileHour; ?></span>
                </span>
            </div>

            <br>
            <span class="days-passed">
                <i class="fas fa-history"></i> Değiştirilme: 
                <span data-time="<?php echo $file['mtime']; ?>"></span>
            </span>
        </li>
    <?php endforeach; ?>
</ul>
    
   
    </ul>


<?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/src/include/footer.php"; ?>

    <script>
    function updateTimes() {
        var now = new Date().getTime();
        var elements = document.getElementsByClassName('days-passed');

        for (var i = 0; i < elements.length; i++) {
            var mtimeSpan = elements[i].querySelector('span[data-time]');
            var mtime = parseInt(mtimeSpan.getAttribute('data-time')) * 1000;

            var timeDiff = getTimeDifference(now, mtime);
            mtimeSpan.textContent = timeDiff.text;
            mtimeSpan.className = timeDiff.class;
        }
    }

    function getTimeDifference(now, time) {
        var diff = now - time;
        var days = Math.floor(diff / (1000 * 60 * 60 * 24));
        var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((diff % (1000 * 60)) / 1000);

        var text = days + "g " + hours + "s " + minutes + "d " + seconds + "sn önce";
        var className;

        if (days < 1) {
            className = "time-recent";
        } else if (days < 7) {
            className = "time-medium";
        } else {
            className = "time-old";
        }

        return { text: text, class: className };
    }

    function clearDate() {
        document.getElementById('search_date').value = '';
    }

    function setToday() {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();

        today = yyyy + '-' + mm + '-' + dd;
        document.getElementById('search_date').value = today;
    }

    setInterval(updateTimes, 1000);
    updateTimes();
    </script>
    
    
</body>
</html>
