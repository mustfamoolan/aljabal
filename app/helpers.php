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

        return $moduleLabel . ' - ' . $actionLabel;
    }
}