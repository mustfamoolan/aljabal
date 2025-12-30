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

if (!function_exists('format_currency')) {
    /**
     * Format a number as currency with Iraqi Dinar symbol
     *
     * @param float|int $amount The amount to format
     * @return string Formatted currency string (e.g., "1,234.56 د.ع")
     */
    function format_currency(float|int $amount): string
    {
        $amount = (float) $amount;
        
        // Format with 2 decimal places, remove trailing zeros
        $formatted = number_format($amount, 2, '.', ',');
        
        // Remove trailing zeros and decimal point if not needed
        $formatted = rtrim(rtrim($formatted, '0'), '.');
        
        return $formatted . ' د.ع';
    }
}
