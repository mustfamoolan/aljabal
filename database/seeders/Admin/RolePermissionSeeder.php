<?php

namespace Database\Seeders\Admin;

use App\Models\EmployeeType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management permissions
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            // Role management permissions
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',

            // Permission management permissions
            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',

            // Employee management permissions
            'employees.view',
            'employees.create',
            'employees.update',
            'employees.delete',

            // Representative management permissions
            'representatives.view',
            'representatives.create',
            'representatives.update',
            'representatives.delete',

            // Admin permissions
            'admin.access',

            // Inventory permissions
            'inventory.products.view',
            'inventory.products.create',
            'inventory.products.update',
            'inventory.products.delete',
            'inventory.categories.view',
            'inventory.categories.create',
            'inventory.categories.update',
            'inventory.categories.delete',
            'inventory.suppliers.view',
            'inventory.suppliers.create',
            'inventory.suppliers.update',
            'inventory.suppliers.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $preparerRole = Role::firstOrCreate(['name' => 'preparer']);
        $callHandlerRole = Role::firstOrCreate(['name' => 'call_handler']);

        // Assign all permissions to admin role
        $adminRole->givePermissionTo(Permission::all());

        // Assign specific permissions to employee roles
        $employeePermissions = [
            'employees.view',
            'employees.create',
            'employees.update',
        ];

        $employeeRole->givePermissionTo($employeePermissions);
        $preparerRole->givePermissionTo($employeePermissions);
        $callHandlerRole->givePermissionTo($employeePermissions);

        // Assign inventory permissions to admin (already has all via Permission::all())
        // You can assign specific inventory permissions to other roles here if needed

        // Create Employee Types
        $preparerEmployeeType = EmployeeType::firstOrCreate(['name' => 'مجهز'], [
            'description' => 'موظف مسؤول عن التجهيز',
            'is_active' => true,
        ]);

        $callHandlerEmployeeType = EmployeeType::firstOrCreate(['name' => 'موظف رد'], [
            'description' => 'موظف مسؤول عن رد المكالمات',
            'is_active' => true,
        ]);

        // Link Employee Types to Roles
        $preparerEmployeeType->roles()->syncWithoutDetaching([$preparerRole->id, $employeeRole->id]);
        $callHandlerEmployeeType->roles()->syncWithoutDetaching([$callHandlerRole->id, $employeeRole->id]);
    }
}
