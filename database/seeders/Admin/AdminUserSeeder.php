<?php

namespace Database\Seeders\Admin;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        $admin = User::firstOrCreate(
            ['phone' => '07742209251'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('12345678'),
                'type' => UserType::ADMIN,
                'is_active' => true,
            ]
        );

        // Assign admin role if it exists
        if ($admin->wasRecentlyCreated || !$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $this->command->info('تم إنشاء مستخدم المدير بنجاح');
        $this->command->info('رقم الهاتف: 07742209251');
        $this->command->info('كلمة المرور: 12345678');
    }
}
