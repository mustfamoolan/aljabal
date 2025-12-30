<?php

namespace Database\Seeders;

use Database\Seeders\Admin\AdminUserSeeder;
use Database\Seeders\Admin\RolePermissionSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RolePermissionSeeder::class);

        // Then seed admin user
        $this->call(AdminUserSeeder::class);

        // Seed inventory data
        $this->call(\Database\Seeders\Inventory\CategorySeeder::class);
        $this->call(\Database\Seeders\Inventory\TagSeeder::class);

        // Seed governorates and districts
        $this->call(GovernorateSeeder::class);
        $this->call(DistrictSeeder::class);
    }
}
