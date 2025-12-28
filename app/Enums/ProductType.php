<?php

namespace App\Enums;

enum ProductType: string
{
    case ORIGINAL = 'original';
    case NORMAL = 'normal';

    public function label(): string
    {
        return match($this) {
            self::ORIGINAL => 'أصلي',
            self::NORMAL => 'عادي',
        };
    }
}
