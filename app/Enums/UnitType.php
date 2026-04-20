<?php

namespace App\Enums;

enum UnitType: string
{
    case PIECE = 'piece';
    case SET = 'set';
    case WEIGHT = 'weight';
    case CARTON = 'carton';
    case COLLECTION = 'collection';
    case TWO_PARTS = 'two_parts';

    public function label(): string
    {
        return match($this) {
            self::PIECE => 'قطعة',
            self::SET => 'مجموعة / سيت',
            self::WEIGHT => 'وزن',
            self::CARTON => 'كارتون',
            self::COLLECTION => 'سلسلة مجموعة',
            self::TWO_PARTS => 'جزئين',
        };
    }
}
