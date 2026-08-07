<!DOCTYPE html>
<html>
<head>
    <title>Test Images</title>
    <style>
        body { font-family: sans-serif; background: #f1f5f9; padding: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); padding: 10px; text-align: center; }
        img { max-width: 100%; max-height: 150px; object-fit: cover; border-radius: 4px; }
        .filename { font-size: 11px; color: #475569; word-break: break-all; margin-top: 8px; }
    </style>
</head>
<body>
    <h1>Profile Photos</h1>
    <div class="grid">
        <?php
        $dir = __DIR__ . '/storage/profile_photos';
        if (is_dir($dir)) {
            foreach (scandir($dir) as $file) {
                if ($file !== '.' && $file !== '..') {
                    echo "<div class='card'>";
                    echo "<img src='/storage/profile_photos/$file'>";
                    echo "<div class='filename'>$file</div>";
                    echo "</div>";
                }
            }
        }
        ?>
    </div>

    <h1>Kendaraan Photos</h1>
    <div class="grid">
        <?php
        $dir = __DIR__ . '/storage/kendaraan';
        if (is_dir($dir)) {
            foreach (scandir($dir) as $file) {
                if ($file !== '.' && $file !== '..') {
                    echo "<div class='card'>";
                    echo "<img src='/storage/kendaraan/$file'>";
                    echo "<div class='filename'>$file</div>";
                    echo "</div>";
                }
            }
        }
        ?>
    </div>
</body>
</html>
