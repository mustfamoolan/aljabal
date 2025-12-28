<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';

    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'معلق',
            self::APPROVED => 'موافق عليه',
            self::REJECTED => 'مرفوض',
            self::COMPLETED => 'مكتمل',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

