<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GiftSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'price',
        'min_books',
        'max_books',
        'box_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'box_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the orders that use this gift as a gift.
     */
    public function ordersAsGift(): HasMany
    {
        return $this->hasMany(Order::class, 'gift_id');
    }

    /**
     * Get the orders that use this gift as a gift box.
     */
    public function ordersAsGiftBox(): HasMany
    {
        return $this->hasMany(Order::class, 'gift_box_id');
    }

    /**
     * Scope a query to only include active gift settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include gifts (not gift boxes).
     */
    public function scopeGifts($query)
    {
        return $query->where('type', 'gift');
    }

    /**
     * Scope a query to only include gift boxes.
     */
    public function scopeGiftBoxes($query)
    {
        return $query->where('type', 'gift_box');
    }

    /**
     * Get gift boxes suitable for a specific number of books.
     */
    public function scopeSuitableForBooks($query, int $bookCount)
    {
        return $query->where('type', 'gift_box')
            ->where(function ($q) use ($bookCount) {
                $q->where(function ($subQ) use ($bookCount) {
                    $subQ->whereNull('min_books')
                        ->orWhere('min_books', '<=', $bookCount);
                })
                ->where(function ($subQ) use ($bookCount) {
                    $subQ->whereNull('max_books')
                        ->orWhere('max_books', '>=', $bookCount);
                });
            });
    }
}
