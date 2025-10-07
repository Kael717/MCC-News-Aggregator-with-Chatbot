<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Media URL Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration allows you to override the default media URL generation
    | behavior. This is particularly useful for production environments where
    | the APP_URL might not be set correctly.
    |
    */

    // Force production URL for media files
    'force_production_url' => env('FORCE_PRODUCTION_MEDIA_URL', false),
    
    // Production domain (used when force_production_url is true)
    'production_domain' => env('PRODUCTION_DOMAIN', 'https://mcc-nac.com'),
    
    // Storage path prefix
    'storage_path' => '/storage',
    
    // Allowed domains for media serving
    'allowed_domains' => [
        'https://mcc-nac.com',
        'http://localhost',
        'http://127.0.0.1:8000',
    ],
    
    // Default placeholder images
    'placeholders' => [
        'video' => '/images/video-placeholder.jpg',
        'image' => '/images/image-placeholder.jpg',
    ],
];
