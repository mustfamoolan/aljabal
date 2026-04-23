<?php

namespace App\Enums;

enum OrderStatus: string
{
    case NEW = 'new';
    case PREPARED = 'prepared';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case RETURNED = 'returned';
    case REPLACED = 'replaced';
    case PICKED_UP = 'picked_up';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'جديد',
            self::PREPARED => 'مجهز',
            self::COMPLETED => 'مكتمل',
            self::CANCELLED => 'ملغي',
            self::RETURNED => 'راجع',
            self::REPLACED => 'استبدال',
            self::PICKED_UP => 'تم الاستلام من قبل المندوب',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NEW => 'bg-info',
            self::PREPARED => 'bg-warning',
            self::COMPLETED => 'bg-success',
            self::CANCELLED => 'bg-danger',
            self::RETURNED => 'bg-secondary',
            self::REPLACED => 'bg-primary',
            self::PICKED_UP => 'bg-dark',
        };
    }

    public function canBeCompleted(): bool
    {
        return in_array($this, [self::NEW, self::PREPARED, self::PICKED_UP]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::NEW, self::PREPARED]);
    }
}
