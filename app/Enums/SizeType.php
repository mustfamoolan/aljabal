<?php

namespace App\Enums;

enum SizeType: string
{
    case LARGE = 'large';      // كبير
    case WAZIRI = 'waziri';    // وزيري
    case RUQAI = 'ruqai';      // رقعي
    case KAFFI = 'kaffi';      // كفي
    case POCKET = 'pocket';    // جيبي

    public function label(): string
    {
        return match ($this) {
            self::LARGE => 'كبير',
            self::WAZIRI => 'وزيري',
            self::RUQAI => 'رقعي',
            self::KAFFI => 'كفي',
            self::POCKET => 'جيبي',
        };
    }
}
