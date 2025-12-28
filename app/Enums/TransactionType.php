<?php

namespace App\Enums;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAWAL = 'withdrawal';
    case COMMISSION = 'commission';
    case BONUS = 'bonus';
    case DEDUCTION = 'deduction';
    case REFUND = 'refund';

    public function getLabel(): string
    {
        return match($this) {
            self::DEPOSIT => 'إيداع',
            self::WITHDRAWAL => 'سحب',
            self::COMMISSION => 'عمولة',
            self::BONUS => 'مكافأة',
            self::DEDUCTION => 'خصم',
            self::REFUND => 'استرداد',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

