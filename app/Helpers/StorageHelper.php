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

        // Check if symbolic link exists
        $publicPath = public_path('storage/' . $path);
        if (file_exists($publicPath) || is_link(public_path('storage'))) {
            // Use standard asset() if symbolic link exists
            return asset('storage/' . $path);
        }

        // Otherwise, use direct URL to serve file directly from storage
        $storagePath = storage_path('app/public/' . $path);
        if (file_exists($storagePath)) {
            // Use direct URL instead of route to avoid route not found errors
            return url('/storage/' . $path);
        }

        return null;
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

