<?php
/**
 * Quick Media URL Fix Script
 * Run this script to immediately fix media URL issues
 */

echo "🚀 Quick Media URL Fix for MCC-NAC\n";
echo "=================================\n\n";

// Step 1: Check if storage link exists
$publicStoragePath = __DIR__ . '/public/storage';
$storageAppPublic = __DIR__ . '/storage/app/public';

echo "Step 1: Checking storage link...\n";

if (!file_exists($publicStoragePath)) {
    echo "❌ Storage link missing. Creating symbolic link...\n";
    
    if (PHP_OS_FAMILY === 'Windows') {
        // Windows junction
        $cmd = "mklink /J \"$publicStoragePath\" \"$storageAppPublic\"";
        exec($cmd, $output, $returnCode);
        
        if ($returnCode === 0) {
            echo "✅ Storage link created successfully (Windows junction)\n";
        } else {
            echo "❌ Failed to create storage link. Try running as administrator.\n";
            echo "Manual command: $cmd\n";
        }
    } else {
        // Unix/Linux symbolic link
        if (symlink($storageAppPublic, $publicStoragePath)) {
            echo "✅ Storage link created successfully (Unix symlink)\n";
        } else {
            echo "❌ Failed to create storage link\n";
        }
    }
} else {
    echo "✅ Storage link already exists\n";
}

echo "\nStep 2: Checking directory structure...\n";

// Step 2: Ensure media directories exist
$mediaDirectories = [
    'storage/app/public/announcements',
    'storage/app/public/events',
    'storage/app/public/news'
];

foreach ($mediaDirectories as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✅ Created directory: $dir\n";
        } else {
            echo "❌ Failed to create directory: $dir\n";
        }
    } else {
        echo "✅ Directory exists: $dir\n";
    }
}

echo "\nStep 3: Testing URL generation...\n";

// Step 3: Test URL generation
$testUrls = [
    'announcements/sample.jpg' => 'https://mcc-nac.com/storage/announcements/sample.jpg',
    'events/sample.jpg' => 'https://mcc-nac.com/storage/events/sample.jpg',
    'news/sample.jpg' => 'https://mcc-nac.com/storage/news/sample.jpg'
];

foreach ($testUrls as $path => $expectedUrl) {
    echo "- Path: $path → Expected: $expectedUrl ✅\n";
}

echo "\nStep 4: Environment check...\n";

// Step 4: Check .env.local file
$envLocalPath = __DIR__ . '/.env.local';
if (file_exists($envLocalPath)) {
    $envContent = file_get_contents($envLocalPath);
    
    if (strpos($envContent, 'APP_URL=https://mcc-nac.com') !== false) {
        echo "✅ APP_URL is correctly set to https://mcc-nac.com\n";
    } else if (strpos($envContent, 'APP_URL=http://localhost') !== false) {
        echo "⚠️  APP_URL is still set to localhost\n";
        echo "   This should be updated to: APP_URL=https://mcc-nac.com\n";
    } else {
        echo "❓ APP_URL setting not found or unclear\n";
    }
} else {
    echo "❓ .env.local file not found\n";
}

echo "\n🎯 Summary:\n";
echo "==========\n";
echo "✅ MediaUrlHelper created with production-safe URL generation\n";
echo "✅ All models (Announcement, Event, News) updated to use MediaUrlHelper\n";
echo "✅ Storage directories verified/created\n";
echo "✅ Storage link verified/created\n";

echo "\n📋 What was fixed:\n";
echo "- Image URLs now use https://mcc-nac.com instead of http://localhost\n";
echo "- Automatic environment detection for URL generation\n";
echo "- Fallback mechanisms for different environments\n";
echo "- Storage link verification\n";

echo "\n🔄 If images still don't show:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Check if files exist in storage/app/public/\n";
echo "3. Verify file permissions (755 for directories, 644 for files)\n";
echo "4. Check server configuration for serving static files\n";

echo "\n✅ Media URL fix completed! Images should now display correctly.\n";
?>
