<?php

namespace App\Enums;

enum OrderStatus: string
{
    case NEW = 'new';
    case SENT_TO_GATEWAY = 'sent_to_gateway';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'جديد',
            self::SENT_TO_GATEWAY => 'تم الإرسال للوسيط',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NEW => 'bg-info',
            self::SENT_TO_GATEWAY => 'bg-success',
        };
    }

    public function canBeCompleted(): bool
    {
        return $this === self::NEW;
    }

    public function canBeCancelled(): bool
    {
        return $this === self::NEW;
    }
}
