<?php

namespace App\Enums;

enum UnitType: string
{
    case WEIGHT = 'weight';
    case CARTON = 'carton';
    case SET = 'set';
    case PIECE = 'piece';

    public function label(): string
    {
        return match($this) {
            self::WEIGHT => 'وزن',
            self::CARTON => 'كارتون',
            self::SET => 'مجموعة',
            self::PIECE => 'قطعة / مفرد',
        };
    }
}
