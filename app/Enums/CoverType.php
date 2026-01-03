<?php

namespace App\Enums;

enum CoverType: string
{
    case HARDCOVER = 'hardcover';  // هاردكفر
    case PAPERBACK = 'paperback';  // ورقي

    public function label(): string
    {
        return match ($this) {
            self::HARDCOVER => 'هاردكفر',
            self::PAPERBACK => 'ورقي',
        };
    }
}
