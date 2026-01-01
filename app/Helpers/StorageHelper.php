<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Get the URL for a storage file
     * Works with or without symbolic link
     *
     * @param string $path The storage path relative to storage/app/public
     * @return string|null The URL to access the file
     */
    public static function url($path)
    {
        if (empty($path)) {
            return null;
        }

        // Normalize path (remove leading slashes)
        $path = ltrim($path, '/');

        // First, check if the file actually exists in storage
        $storagePath = storage_path('app/public/' . $path);
        if (!file_exists($storagePath)) {
            return null;
        }

        // Check if symbolic link exists and is valid
        $storageLinkPath = public_path('storage');
        $publicPath = public_path('storage/' . $path);

        // If symbolic link exists and file is accessible through it
        if (is_link($storageLinkPath) && file_exists($publicPath)) {
            // Use standard asset() if symbolic link exists and works
            return asset('storage/' . $path);
        }

        // If symbolic link exists but file is not accessible through it
        // or if symbolic link doesn't exist, use route as fallback
        // This route is defined in routes/web.php and serves files directly from storage
        return url('/storage/' . $path);
    }

    /**
     * Get the URL for a storage file using Storage facade
     * This is an alias for url() but uses Storage facade internally
     *
     * @param string $path The storage path relative to storage/app/public
     * @return string|null The URL to access the file
     */
    public static function storageUrl($path)
    {
        return self::url($path);
    }
}

