<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPreparationCommissionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_value',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'commission_value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get commission for an order based on settings.
     */
    public static function getCommissionForOrder(Order $order): float
    {
        // Check for exceptions first (representative or user)
        if ($order->representative_id) {
            $exception = \App\Models\OrderPreparationCommissionException::getForRepresentative($order->representative_id);
            if ($exception) {
                return (float) $exception->commission_value;
            }
        }

        if ($order->created_by) {
            $exception = \App\Models\OrderPreparationCommissionException::getForUser($order->created_by);
            if ($exception) {
                return (float) $exception->commission_value;
            }
        }

        // If no exception, use general settings
        // Get the first active setting (should be only one)
        $settings = self::where('is_active', true)->first();

        if (!$settings) {
            return 0.0;
        }

        return (float) $settings->commission_value;
    }

    /**
     * Scope for active settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
