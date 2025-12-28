<?php

namespace Database\Seeders\Inventory;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'جديد',
            'عرض خاص',
            'الأكثر مبيعاً',
            'مميز',
            'مخفض',
            'عالي الجودة',
            'صديق للبيئة',
            'مستورد',
            'محلي',
            'محدود الكمية',
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }

        $this->command->info('تم إنشاء التاغات بنجاح');
    }
}
