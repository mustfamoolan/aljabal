<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'مدير',
            self::EMPLOYEE => 'موظف',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
