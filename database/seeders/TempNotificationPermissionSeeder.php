<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TempNotificationPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'notifications.view',
            'notifications.send',
            'notifications.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::findByName('admin');
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }
    }
}
