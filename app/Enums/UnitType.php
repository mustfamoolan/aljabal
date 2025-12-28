<?php

namespace App\Enums;

enum UnitType: string
{
    case WEIGHT = 'weight';
    case CARTON = 'carton';
    case SET = 'set';

    public function label(): string
    {
        return match($this) {
            self::WEIGHT => 'وزن',
            self::CARTON => 'كارتون',
            self::SET => 'مجموعة',
        };
    }
}
