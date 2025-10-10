<?php
// File checker for deployment debugging
echo "<h2>Laravel File Structure Check</h2>";

$requiredPaths = [
    '../routes/api.php',
    '../routes/web.php', 
    '../routes/console.php',
    '../bootstrap/app.php',
    '../app',
    '../vendor',
    '../config',
    '../storage'
];

foreach ($requiredPaths as $path) {
    $fullPath = __DIR__ . '/' . $path;
    $exists = file_exists($fullPath);
    $status = $exists ? '✅ EXISTS' : '❌ MISSING';
    echo "<p><strong>$path</strong>: $status</p>";
    
    if (!$exists) {
        echo "<p style='color: red; margin-left: 20px;'>This file/directory is missing and needs to be uploaded!</p>";
    }
}

echo "<hr>";
echo "<p><strong>Current directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Parent directory contents:</strong></p>";
if (is_dir(dirname(__DIR__))) {
    $files = scandir(dirname(__DIR__));
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<p>- $file</p>";
        }
    }
}
?>
