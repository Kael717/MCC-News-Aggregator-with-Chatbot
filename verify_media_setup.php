<?php
/**
 * Media Setup Verification Script
 * This script verifies that media files are properly configured for production
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "🔍 MCC-NAC Media Setup Verification\n";
echo "==================================\n\n";

// Check environment configuration
echo "📋 Environment Configuration:\n";
echo "- APP_URL: " . env('APP_URL') . "\n";
echo "- APP_ENV: " . env('APP_ENV') . "\n";
echo "- Current Domain: " . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'CLI') . "\n\n";

// Check storage directory
$storagePath = storage_path('app/public');
echo "📁 Storage Directory Check:\n";
echo "- Storage path: $storagePath\n";
echo "- Exists: " . (is_dir($storagePath) ? "✅ Yes" : "❌ No") . "\n";
echo "- Writable: " . (is_writable($storagePath) ? "✅ Yes" : "❌ No") . "\n\n";

// Check public storage link
$publicStoragePath = public_path('storage');
echo "🔗 Public Storage Link Check:\n";
echo "- Public storage path: $publicStoragePath\n";
echo "- Link exists: " . (file_exists($publicStoragePath) ? "✅ Yes" : "❌ No") . "\n";
echo "- Is symbolic link: " . (is_link($publicStoragePath) ? "✅ Yes" : "❌ No") . "\n";

if (is_link($publicStoragePath)) {
    echo "- Link target: " . readlink($publicStoragePath) . "\n";
}
echo "\n";

// Test MediaUrlHelper
if (class_exists('App\Helpers\MediaUrlHelper')) {
    echo "🧪 MediaUrlHelper Test:\n";
    
    // Test with sample path
    $testPath = 'announcements/test-image.jpg';
    
    try {
        $testUrl = App\Helpers\MediaUrlHelper::getMediaUrl($testPath);
        echo "- Sample path: $testPath\n";
        echo "- Generated URL: $testUrl\n";
        echo "- Helper status: ✅ Working\n";
    } catch (Exception $e) {
        echo "- Helper status: ❌ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "🧪 MediaUrlHelper: ❌ Not found\n";
}
echo "\n";

// Check sample media files
echo "📸 Sample Media Files Check:\n";
$mediaDirectories = [
    'announcements',
    'events', 
    'news'
];

foreach ($mediaDirectories as $dir) {
    $dirPath = storage_path("app/public/$dir");
    echo "- $dir directory: " . (is_dir($dirPath) ? "✅ Exists" : "❌ Missing") . "\n";
    
    if (is_dir($dirPath)) {
        $files = glob($dirPath . '/*');
        echo "  Files count: " . count($files) . "\n";
        
        if (count($files) > 0) {
            $sampleFile = basename($files[0]);
            $sampleUrl = "https://mcc-nac.com/storage/$dir/$sampleFile";
            echo "  Sample URL: $sampleUrl\n";
        }
    }
}
echo "\n";

// Recommendations
echo "💡 Recommendations:\n";

if (!file_exists($publicStoragePath)) {
    echo "❗ Run: php artisan storage:link\n";
}

if (env('APP_URL') !== 'https://mcc-nac.com') {
    echo "❗ Update APP_URL in .env to: https://mcc-nac.com\n";
}

echo "✅ Clear caches: php artisan cache:clear\n";
echo "✅ Clear config: php artisan config:clear\n";
echo "✅ Clear views: php artisan view:clear\n\n";

echo "🎯 Expected Image URL Format:\n";
echo "https://mcc-nac.com/storage/announcements/filename.jpg\n";
echo "https://mcc-nac.com/storage/events/filename.jpg\n";
echo "https://mcc-nac.com/storage/news/filename.jpg\n\n";

echo "✅ Verification completed!\n";
?>
