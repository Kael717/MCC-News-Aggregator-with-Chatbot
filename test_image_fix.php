<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Image URL Fix ===\n";

// Test the specific path from the error
$testPath = 'announcement-images/obGBBmuRSWxB5lyrLCJ7Bkc9W6c0HJzGONw8ECba.jpg';

echo "Testing path: {$testPath}\n";

// Test different URL generation methods
echo "\n--- URL Generation Methods ---\n";

try {
    // Method 1: Storage facade
    $storageUrl = \Storage::disk('public')->url($testPath);
    echo "1. Storage URL: {$storageUrl}\n";
    echo "   Valid URL: " . (filter_var($storageUrl, FILTER_VALIDATE_URL) ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "1. Storage URL: ERROR - " . $e->getMessage() . "\n";
}

try {
    // Method 2: Asset helper
    $assetUrl = asset('storage/' . $testPath);
    echo "2. Asset URL: {$assetUrl}\n";
    echo "   Valid URL: " . (filter_var($assetUrl, FILTER_VALIDATE_URL) ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "2. Asset URL: ERROR - " . $e->getMessage() . "\n";
}

try {
    // Method 3: Manual construction
    $baseUrl = request()->getSchemeAndHttpHost();
    $manualUrl = rtrim($baseUrl, '/') . '/storage/' . $testPath;
    echo "3. Manual URL: {$manualUrl}\n";
    echo "   Valid URL: " . (filter_var($manualUrl, FILTER_VALIDATE_URL) ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "3. Manual URL: ERROR - " . $e->getMessage() . "\n";
}

try {
    // Method 4: Config-based
    $appUrl = config('app.url', 'http://localhost');
    $configUrl = rtrim($appUrl, '/') . '/storage/' . $testPath;
    echo "4. Config URL: {$configUrl}\n";
    echo "   Valid URL: " . (filter_var($configUrl, FILTER_VALIDATE_URL) ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "4. Config URL: ERROR - " . $e->getMessage() . "\n";
}

// Check file system
echo "\n--- File System Check ---\n";
$fullPath = storage_path('app/public/' . $testPath);
echo "Full file path: {$fullPath}\n";
echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";

if (file_exists($fullPath)) {
    echo "File size: " . filesize($fullPath) . " bytes\n";
    echo "File readable: " . (is_readable($fullPath) ? 'YES' : 'NO') . "\n";
}

// Check storage link
echo "\n--- Storage Link Check ---\n";
$publicStoragePath = public_path('storage');
echo "Public storage path: {$publicStoragePath}\n";
echo "Storage link exists: " . (file_exists($publicStoragePath) ? 'YES' : 'NO') . "\n";

if (file_exists($publicStoragePath)) {
    echo "Is symlink: " . (is_link($publicStoragePath) ? 'YES' : 'NO') . "\n";
    if (is_link($publicStoragePath)) {
        echo "Link target: " . readlink($publicStoragePath) . "\n";
    }
}

// Check if the specific image file exists via public link
$publicImagePath = public_path('storage/' . $testPath);
echo "Public image path: {$publicImagePath}\n";
echo "Public image exists: " . (file_exists($publicImagePath) ? 'YES' : 'NO') . "\n";

echo "\n--- Configuration ---\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "Public disk root: " . config('filesystems.disks.public.root') . "\n";
echo "Public disk URL: " . config('filesystems.disks.public.url') . "\n";

echo "\n=== End Test ===\n";
