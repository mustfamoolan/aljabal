<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_address',
        'customer_phone',
        'customer_phone_2',
        'customer_social_media',
        'customer_notes',
        'status',
        'total_amount',
        'total_profit',
        'preparation_commission',
        'final_profit',
        'representative_id',
        'created_by',
        'completed_at',
        'governorate_id',
        'district_id',
        'delivery_fee',
        'gift_id',
        'gift_box_id',
        'gift_price',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total_amount' => 'decimal:2',
            'total_profit' => 'decimal:2',
            'preparation_commission' => 'decimal:2',
            'final_profit' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'gift_price' => 'decimal:2',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the representative who created the order.
     */
    public function representative(): BelongsTo
    {
        return $this->belongsTo(Representative::class);
    }

    /**
     * Get the user who created the order.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the governorate for the order.
     */
    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    /**
     * Get the district for the order.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the gift for the order.
     */
    public function gift(): BelongsTo
    {
        return $this->belongsTo(GiftSetting::class, 'gift_id');
    }

    /**
     * Get the gift box for the order.
     */
    public function giftBox(): BelongsTo
    {
        return $this->belongsTo(GiftSetting::class, 'gift_box_id');
    }

    /**
     * Calculate total amount from order items.
     */
    public function calculateTotal(): float
    {
        return (float) $this->orderItems()->sum('subtotal');
    }

    /**
     * Calculate total profit from order items.
     */
    public function calculateProfit(): float
    {
        return (float) $this->orderItems()->sum('profit_subtotal');
    }

    /**
     * Check if order can be completed.
     */
    public function canBeCompleted(): bool
    {
        return $this->status->canBeCompleted();
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return $this->status->canBeCancelled();
    }

    /**
     * Scope for orders by status.
     */
    public function scopeByStatus($query, OrderStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for orders by representative.
     */
    public function scopeByRepresentative($query, int $representativeId)
    {
        return $query->where('representative_id', $representativeId);
    }
}
