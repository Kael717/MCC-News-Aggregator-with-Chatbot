<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class MediaUrlHelper
{
    /**
     * Generate a production-safe URL for media files
     */
    public static function getMediaUrl($path)
    {
        if (empty($path)) {
            return null;
        }

        // Check configuration for forced production URL
        if (config('media.force_production_url', false)) {
            return config('media.production_domain') . config('media.storage_path') . '/' . $path;
        }

        // Auto-detect production environment
        $currentHost = request()->getHost();
        if (app()->environment('production') || 
            $currentHost === 'mcc-nac.com' || 
            str_contains($currentHost, 'mcc-nac.com')) {
            // Force production URL
            return 'https://mcc-nac.com/storage/' . $path;
        }

        // For local development, use Storage facade
        return Storage::disk('public')->url($path);
    }

    /**
     * Generate multiple media URLs
     */
    public static function getMultipleMediaUrls($paths)
    {
        if (empty($paths) || !is_array($paths)) {
            return [];
        }

        return array_map([self::class, 'getMediaUrl'], $paths);
    }

    /**
     * Get the correct base storage URL
     */
    public static function getStorageBaseUrl()
    {
        if (app()->environment('production') || request()->getHost() === 'mcc-nac.com') {
            return 'https://mcc-nac.com/storage';
        }

        return config('app.url') . '/storage';
    }
}
