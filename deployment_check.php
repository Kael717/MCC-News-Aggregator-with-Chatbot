<?php
/**
 * MCC-NAC Production Deployment Check
 * Upload this file to your production server root and access via browser
 * This will help identify what files are missing
 */

echo "<h1>MCC-NAC Production Deployment Check</h1>";
echo "<p>Checking deployment status for: " . $_SERVER['HTTP_HOST'] . "</p>";

$basePath = __DIR__;
$criticalFiles = [
    'routes/api.php' => 'API routes file (CRITICAL - causing current error)',
    'routes/web.php' => 'Web routes file',
    'routes/console.php' => 'Console routes file',
    'bootstrap/app.php' => 'Laravel bootstrap file',
    'config/app.php' => 'Application configuration',
    'config/database.php' => 'Database configuration',
    'app/Http/Controllers/ChatbotController.php' => 'Chatbot controller',
    'vendor/autoload.php' => 'Composer autoloader',
    'storage/framework' => 'Storage framework directory',
    'public/index.php' => 'Public entry point',
    '.env' => 'Environment configuration file'
];

$criticalDirs = [
    'routes' => 'Routes directory',
    'bootstrap' => 'Bootstrap directory', 
    'config' => 'Configuration directory',
    'app' => 'Application directory',
    'vendor' => 'Composer vendor directory',
    'storage' => 'Storage directory',
    'public' => 'Public directory'
];

echo "<h2>Critical Files Status</h2>";
$missingFiles = [];
foreach ($criticalFiles as $file => $description) {
    $fullPath = $basePath . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ <strong>$file</strong> - EXISTS<br>";
        echo "&nbsp;&nbsp;&nbsp;Description: $description<br>";
    } else {
        echo "❌ <strong>$file</strong> - <span style='color:red'>MISSING</span><br>";
        echo "&nbsp;&nbsp;&nbsp;Description: $description<br>";
        $missingFiles[] = $file;
    }
    echo "<br>";
}

echo "<h2>Critical Directories Status</h2>";
$missingDirs = [];
foreach ($criticalDirs as $dir => $description) {
    $fullPath = $basePath . '/' . $dir;
    if (is_dir($fullPath)) {
        echo "✅ <strong>$dir/</strong> - EXISTS<br>";
        echo "&nbsp;&nbsp;&nbsp;Description: $description<br>";
    } else {
        echo "❌ <strong>$dir/</strong> - <span style='color:red'>MISSING</span><br>";
        echo "&nbsp;&nbsp;&nbsp;Description: $description<br>";
        $missingDirs[] = $dir;
    }
    echo "<br>";
}

echo "<h2>Laravel Bootstrap Test</h2>";
try {
    if (file_exists($basePath . '/vendor/autoload.php')) {
        require_once $basePath . '/vendor/autoload.php';
        echo "✅ Composer autoloader loaded successfully<br>";
        
        if (file_exists($basePath . '/bootstrap/app.php')) {
            $app = require_once $basePath . '/bootstrap/app.php';
            echo "✅ Laravel application bootstrap loaded successfully<br>";
            echo "✅ Application instance created<br>";
        } else {
            echo "❌ bootstrap/app.php not found<br>";
        }
    } else {
        echo "❌ Composer autoloader not found<br>";
        echo "&nbsp;&nbsp;&nbsp;Run: composer install<br>";
    }
} catch (Exception $e) {
    echo "❌ Bootstrap Error: " . $e->getMessage() . "<br>";
    echo "&nbsp;&nbsp;&nbsp;Stack trace: " . $e->getTraceAsString() . "<br>";
}

echo "<h2>File Permissions</h2>";
$checkPaths = ['routes', 'bootstrap', 'config', 'storage', 'public'];
foreach ($checkPaths as $path) {
    $fullPath = $basePath . '/' . $path;
    if (file_exists($fullPath)) {
        $perms = fileperms($fullPath);
        $permStr = substr(sprintf('%o', $perms), -4);
        echo "$path/ permissions: $permStr<br>";
    } else {
        echo "$path/ - NOT FOUND<br>";
    }
}

echo "<h2>Environment Information</h2>";
echo "Current Directory: " . getcwd() . "<br>";
echo "Script Path: " . __DIR__ . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";

echo "<h2>Summary</h2>";
if (empty($missingFiles) && empty($missingDirs)) {
    echo "<p style='color:green; font-weight:bold;'>✅ All critical files and directories are present!</p>";
    echo "<p>If you're still getting errors, try running:</p>";
    echo "<code>php artisan config:clear && php artisan cache:clear && php artisan route:clear</code>";
} else {
    echo "<p style='color:red; font-weight:bold;'>❌ Missing files detected!</p>";
    echo "<p>You need to upload the following missing items:</p>";
    echo "<ul>";
    foreach ($missingFiles as $file) {
        echo "<li>$file</li>";
    }
    foreach ($missingDirs as $dir) {
        echo "<li>$dir/ (entire directory)</li>";
    }
    echo "</ul>";
    echo "<p><strong>Action Required:</strong> Upload the complete Laravel application structure to your server.</p>";
}

echo "<h2>Quick Fix Commands</h2>";
echo "<p>Run these commands on your server:</p>";
echo "<pre>";
echo "cd " . $basePath . "\n";
echo "composer install --no-dev --optimize-autoloader\n";
echo "chmod -R 755 storage/\n";
echo "chmod -R 755 bootstrap/cache/\n";
echo "php artisan storage:link\n";
echo "php artisan config:clear\n";
echo "php artisan cache:clear\n";
echo "php artisan route:clear\n";
echo "php artisan view:clear\n";
echo "</pre>";

echo "<hr>";
echo "<p><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>
