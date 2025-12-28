<?php

namespace App\Enums;

enum WithdrawalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';

    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'معلق',
            self::APPROVED => 'موافق عليه',
            self::REJECTED => 'مرفوض',
            self::PROCESSING => 'قيد المعالجة',
            self::COMPLETED => 'مكتمل',
        };
    }

    public function getBadgeClass(): string
    {
        return match($this) {
            self::PENDING => 'bg-warning',
            self::APPROVED => 'bg-success',
            self::REJECTED => 'bg-danger',
            self::PROCESSING => 'bg-info',
            self::COMPLETED => 'bg-success',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

