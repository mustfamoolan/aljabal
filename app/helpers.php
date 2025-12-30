<?php

if (!function_exists('storage_url')) {
    /**
     * Get the URL for a storage file
     * Works with or without symbolic link
     *
     * @param string $path The storage path relative to storage/app/public
     * @return string|null The URL to access the file
     */
    function storage_url(string $path): ?string
    {
        return \App\Helpers\StorageHelper::url($path);
    }
}
