<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            ['name' => 'بغداد', 'code' => 'BG'],
            ['name' => 'البصرة', 'code' => 'BA'],
            ['name' => 'الموصل', 'code' => 'NI'],
            ['name' => 'أربيل', 'code' => 'AR'],
            ['name' => 'كركوك', 'code' => 'KI'],
            ['name' => 'السليمانية', 'code' => 'SU'],
            ['name' => 'دهوك', 'code' => 'DA'],
            ['name' => 'ديالى', 'code' => 'DI'],
            ['name' => 'الأنبار', 'code' => 'AN'],
            ['name' => 'بابل', 'code' => 'BB'],
            ['name' => 'كربلاء', 'code' => 'KA'],
            ['name' => 'النجف', 'code' => 'NA'],
            ['name' => 'المثنى', 'code' => 'MU'],
            ['name' => 'القادسية', 'code' => 'QA'],
            ['name' => 'واسط', 'code' => 'WA'],
            ['name' => 'ذي قار', 'code' => 'DQ'],
            ['name' => 'ميسان', 'code' => 'MA'],
            ['name' => 'صلاح الدين', 'code' => 'SD'],
        ];

        foreach ($governorates as $governorate) {
            Governorate::updateOrCreate(
                ['name' => $governorate['name']],
                $governorate
            );
        }
    }
}
