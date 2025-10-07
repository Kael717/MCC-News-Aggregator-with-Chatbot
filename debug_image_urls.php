<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Announcement;

echo "=== Debugging Image URLs ===\n";

// Find announcements with images
$announcements = Announcement::where(function($query) {
    $query->whereNotNull('image_path')
          ->orWhereNotNull('image_paths');
})->take(3)->get();

if ($announcements->count() > 0) {
    foreach ($announcements as $announcement) {
        echo "\n--- Announcement ID: {$announcement->id} ---\n";
        echo "Title: {$announcement->title}\n";
        echo "Image Path: " . ($announcement->image_path ?? 'null') . "\n";
        echo "Image Paths: " . ($announcement->image_paths ?? 'null') . "\n";
        echo "Media URL: " . ($announcement->mediaUrl ?? 'null') . "\n";
        echo "All Image URLs: " . json_encode($announcement->allImageUrls) . "\n";
        echo "Has Media: " . $announcement->hasMedia . "\n";
        
        // Check if file exists
        if ($announcement->image_path) {
            $fullPath = storage_path('app/public/' . $announcement->image_path);
            echo "Full file path: {$fullPath}\n";
            echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
        }
        
        // Check Storage URL generation
        if ($announcement->image_path) {
            echo "Storage URL: " . \Storage::disk('public')->url($announcement->image_path) . "\n";
            echo "Asset URL: " . asset('storage/' . $announcement->image_path) . "\n";
        }
        
        echo "APP_URL: " . config('app.url') . "\n";
        echo "Request is secure: " . (request()->isSecure() ? 'YES' : 'NO') . "\n";
        echo "App environment: " . config('app.env') . "\n";
    }
} else {
    echo "No announcements with images found.\n";
}

echo "\n=== Storage Configuration ===\n";
echo "Public disk root: " . config('filesystems.disks.public.root') . "\n";
echo "Public disk URL: " . config('filesystems.disks.public.url') . "\n";
echo "Storage path: " . storage_path('app/public') . "\n";
echo "Public path: " . public_path('storage') . "\n";

// Check if storage link exists
$linkExists = is_link(public_path('storage'));
echo "Storage link exists: " . ($linkExists ? 'YES' : 'NO') . "\n";

if ($linkExists) {
    echo "Storage link target: " . readlink(public_path('storage')) . "\n";
}

echo "\n=== End Debug ===\n";
