<?php

namespace App\Enums;

enum UnitType: string
{
    case PIECE = 'piece';
    case SET = 'set';

    public function label(): string
    {
        return match($this) {
            self::PIECE => 'قطعة',
            self::SET => 'مجموعة',
        };
    }
}
