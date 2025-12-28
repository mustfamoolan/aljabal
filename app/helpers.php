<?php

if (!function_exists('permission_label')) {
    /**
     * Get Arabic label for permission name
     */
    function permission_label(string $permissionName): string
    {
        $translations = [
            // Users permissions
            'users.view' => 'عرض المستخدمين',
            'users.create' => 'إنشاء مستخدم',
            'users.update' => 'تحديث المستخدم',
            'users.delete' => 'حذف المستخدم',
            
            // Roles permissions
            'roles.view' => 'عرض الأدوار',
            'roles.create' => 'إنشاء دور',
            'roles.update' => 'تحديث الدور',
            'roles.delete' => 'حذف الدور',
            
            // Permissions permissions
            'permissions.view' => 'عرض الصلاحيات',
            'permissions.create' => 'إنشاء صلاحية',
            'permissions.update' => 'تحديث الصلاحية',
            'permissions.delete' => 'حذف الصلاحية',
            
            // Employees permissions
            'employees.view' => 'عرض الموظفين',
            'employees.create' => 'إنشاء موظف',
            'employees.update' => 'تحديث الموظف',
            'employees.delete' => 'حذف الموظف',
            
            // Representatives permissions
            'representatives.view' => 'عرض المندوبين',
            'representatives.create' => 'إنشاء مندوب',
            'representatives.update' => 'تحديث المندوب',
            'representatives.delete' => 'حذف المندوب',
            
            // Admin permissions
            'admin.access' => 'الوصول إلى لوحة التحكم',
            
            // Inventory permissions
            'inventory.products.view' => 'عرض المنتجات',
            'inventory.products.create' => 'إنشاء منتج',
            'inventory.products.update' => 'تحديث المنتج',
            'inventory.products.delete' => 'حذف المنتج',
            'inventory.categories.view' => 'عرض الفئات',
            'inventory.categories.create' => 'إنشاء فئة',
            'inventory.categories.update' => 'تحديث الفئة',
            'inventory.categories.delete' => 'حذف الفئة',
            'inventory.suppliers.view' => 'عرض الموردين',
            'inventory.suppliers.create' => 'إنشاء مورد',
            'inventory.suppliers.update' => 'تحديث المورد',
            'inventory.suppliers.delete' => 'حذف المورد',
        ];
        
        return $translations[$permissionName] ?? $permissionName;
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format currency amount in Iraqi Dinar
     * Removes trailing zeros (keeps English numerals)
     */
    function format_currency(float $amount, int $decimals = 2): string
    {
        // Format number and remove trailing zeros
        $formatted = rtrim(rtrim(number_format($amount, $decimals, '.', ','), '0'), '.');
        
        return $formatted . ' د.ع';
    }
}

if (!function_exists('transaction_type_label')) {
    /**
     * Get Arabic label for transaction type
     */
    function transaction_type_label(string $type): string
    {
        $translations = [
            'deposit' => 'إيداع',
            'withdrawal' => 'سحب',
            'commission' => 'عمولة',
            'bonus' => 'مكافأة',
            'deduction' => 'خصم',
            'refund' => 'استرداد',
        ];
        
        return $translations[$type] ?? $type;
    }
}

