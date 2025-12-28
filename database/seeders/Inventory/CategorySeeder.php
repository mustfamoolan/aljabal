<?php

namespace Database\Seeders\Inventory;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main categories
        $books = Category::create([
            'name' => 'كتب',
            'description' => 'فئة الكتب والمطبوعات',
            'is_active' => true,
        ]);

        $clothing = Category::create([
            'name' => 'ملابس',
            'description' => 'فئة الملابس والأزياء',
            'is_active' => true,
        ]);

        $electronics = Category::create([
            'name' => 'إلكترونيات',
            'description' => 'فئة الأجهزة الإلكترونية',
            'is_active' => true,
        ]);

        $gifts = Category::create([
            'name' => 'هدايا',
            'description' => 'فئة الهدايا والتذكارات',
            'is_active' => true,
        ]);

        $food = Category::create([
            'name' => 'غذائية',
            'description' => 'فئة المواد الغذائية',
            'is_active' => true,
        ]);

        // Create subcategories for books
        Category::create([
            'name' => 'كتب تعليمية',
            'parent_id' => $books->id,
            'description' => 'كتب تعليمية ودراسية',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'روايات',
            'parent_id' => $books->id,
            'description' => 'روايات وأدب',
            'is_active' => true,
        ]);

        // Create subcategories for clothing
        Category::create([
            'name' => 'ملابس رجالية',
            'parent_id' => $clothing->id,
            'description' => 'ملابس للرجال',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'ملابس نسائية',
            'parent_id' => $clothing->id,
            'description' => 'ملابس للنساء',
            'is_active' => true,
        ]);

        $this->command->info('تم إنشاء الفئات بنجاح');
    }
}
