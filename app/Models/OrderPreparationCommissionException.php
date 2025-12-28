<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPreparationCommissionException extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'user_id',
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
     * Get the representative for this exception.
     */
    public function representative(): BelongsTo
    {
        return $this->belongsTo(Representative::class);
    }

    /**
     * Get the user for this exception.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get exception for a representative.
     */
    public static function getForRepresentative(?int $representativeId): ?self
    {
        if (!$representativeId) {
            return null;
        }

        return self::where('representative_id', $representativeId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get exception for a user.
     */
    public static function getForUser(?int $userId): ?self
    {
        if (!$userId) {
            return null;
        }

        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Scope for active exceptions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
