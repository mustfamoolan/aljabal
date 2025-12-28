<?php

namespace App\Enums;

enum WeightUnit: string
{
    case KILOGRAM = 'kilogram';
    case GRAM = 'gram';

    public function label(): string
    {
        return match($this) {
            self::KILOGRAM => 'كيلو',
            self::GRAM => 'غرام',
        };
    }
}
