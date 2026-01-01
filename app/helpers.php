<?php

if (!function_exists('storage_url')) {
    /**
     * Get the URL for a storage file
     * Works with or without symbolic link
     *
     * @param string $path The storage path relative to storage/app/public
     * @return string|null The URL to access the file
     */
    function storage_url($path)
    {
        if (empty($path)) {
            return null;
        }
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
    function format_currency($amount)
    {
        $amount = (float) $amount;
        
        // Format with 2 decimal places, remove trailing zeros
        $formatted = number_format($amount, 2, '.', ',');
        
        // Remove trailing zeros and decimal point if not needed
        $formatted = rtrim(rtrim($formatted, '0'), '.');
        
        return $formatted . ' د.ع';
    }
}

if (!function_exists('permission_label')) {
    /**
     * Get Arabic label for permission name
     *
     * @param string $permissionName
     * @return string
     */
    function permission_label($permissionName)
    {
        $parts = explode('.', $permissionName);
        $module = $parts[0] ?? '';
        $action = $parts[1] ?? '';

        $moduleLabels = [
            'users' => 'المستخدمين',
            'roles' => 'الأدوار',
            'permissions' => 'الصلاحيات',
            'employees' => 'الموظفين',
            'representatives' => 'المندوبين',
            'admin' => 'لوحة التحكم',
            'inventory' => 'المخزون',
            'tags' => 'التاغات',
        ];

        $actionLabels = [
            'view' => 'عرض',
            'create' => 'إنشاء',
            'update' => 'تحديث',
            'delete' => 'حذف',
            'access' => 'الوصول',
        ];

        $moduleLabel = $moduleLabels[$module] ?? $module;
        $actionLabel = $actionLabels[$action] ?? $action;

        if ($module === 'inventory' && isset($parts[2])) {
            $subModule = $parts[1];
            $subAction = $parts[2];
            $subModuleLabels = [
                'products' => 'المنتجات',
                'categories' => 'الفئات',
                'suppliers' => 'الموردين',
            ];
            $moduleLabel = $subModuleLabels[$subModule] ?? $subModule;
            $actionLabel = $actionLabels[$subAction] ?? $subAction;
        }

        // Handle admin.tags.* permissions
        if ($module === 'admin' && isset($parts[1]) && $parts[1] === 'tags' && isset($parts[2])) {
            $moduleLabel = 'التاغات';
            $actionLabel = $actionLabels[$parts[2]] ?? $parts[2];
        }

        return $moduleLabel . ' - ' . $actionLabel;
    }
}

if (!function_exists('get_video_embed_url')) {
    /**
     * Convert YouTube or Vimeo URL to embed URL
     *
     * @param string|null $url The video URL
     * @return string|null The embed URL or null if invalid
     */
    function get_video_embed_url($url)
    {
        if (empty($url)) {
            return null;
        }

        // YouTube URL patterns
        // https://www.youtube.com/watch?v=VIDEO_ID
        // https://youtube.com/watch?v=VIDEO_ID
        // https://youtu.be/VIDEO_ID
        // https://www.youtube.com/embed/VIDEO_ID
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // Vimeo URL patterns
        // https://vimeo.com/VIDEO_ID
        // https://player.vimeo.com/video/VIDEO_ID
        if (preg_match('/(?:vimeo\.com\/|player\.vimeo\.com\/video\/)(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        // If already an embed URL, return as is
        if (str_contains($url, 'youtube.com/embed') || str_contains($url, 'vimeo.com/video')) {
            return $url;
        }

        return null;
    }
}

if (!function_exists('get_video_thumbnail')) {
    /**
     * Get thumbnail URL for YouTube or Vimeo video
     *
     * @param string|null $url The video URL
     * @return string|null The thumbnail URL or null if invalid
     */
    function get_video_thumbnail($url)
    {
        if (empty($url)) {
            return null;
        }

        // YouTube thumbnail
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            return 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
        }

        // Vimeo thumbnail (requires API call, return null for now)
        // Can be implemented later if needed

        return null;
    }
}