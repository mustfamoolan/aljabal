<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalSetting extends Model
{
    protected $fillable = [
        'representative_id',
        'min_withdrawal_amount',
        'is_exception',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'min_withdrawal_amount' => 'decimal:2',
            'is_exception' => 'boolean',
        ];
    }

    /**
     * Get the representative that owns the setting (if exception).
     */
    public function representative(): BelongsTo
    {
        return $this->belongsTo(Representative::class);
    }

    /**
     * Get the user who created the setting.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the setting.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include general settings.
     */
    public function scopeGeneral($query)
    {
        return $query->whereNull('representative_id');
    }

    /**
     * Scope a query to filter by representative.
     */
    public function scopeForRepresentative($query, Representative $representative)
    {
        return $query->where('representative_id', $representative->id);
    }

    /**
     * Get minimum withdrawal amount for a specific representative.
     */
    public static function getMinWithdrawalForRepresentative(Representative $representative): float
    {
        // Check if there's an exception for this representative
        $exception = self::forRepresentative($representative)->first();
        if ($exception) {
            return (float) $exception->min_withdrawal_amount;
        }

        // Get general setting
        $general = self::general()->first();
        if ($general) {
            return (float) $general->min_withdrawal_amount;
        }

        // Default minimum
        return 0.0;
    }
}
