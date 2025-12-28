<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'wholesale_price',
        'customer_price',
        'profit_per_item',
        'subtotal',
        'profit_subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'wholesale_price' => 'decimal:2',
            'customer_price' => 'decimal:2',
            'profit_per_item' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'profit_subtotal' => 'decimal:2',
        ];
    }

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this order item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withDefault([
            'name' => 'منتج محذوف',
            'sku' => null,
        ]);
    }

    /**
     * Calculate profit for this item.
     */
    public function calculateProfit(): float
    {
        $profit = $this->customer_price - $this->wholesale_price;
        return max(0, (float) $profit);
    }

    /**
     * Calculate subtotal for this item.
     */
    public function calculateSubtotal(): float
    {
        return (float) ($this->quantity * $this->customer_price);
    }

    /**
     * Calculate profit subtotal for this item.
     */
    public function calculateProfitSubtotal(): float
    {
        return (float) ($this->quantity * $this->profit_per_item);
    }
}
