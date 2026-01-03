<?php

namespace App\Models;

use App\Enums\CoverType;
use App\Enums\SizeType;
use App\Enums\UnitType;
use App\Enums\WeightUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'is_original',
        'category_id',
        'subcategory_id',
        'supplier_id',
        'author',
        'publisher',
        'purchase_price',
        'retail_price',
        'wholesale_price',
        'quantity',
        'min_quantity',
        'available_quantity',
        'unit_type',
        'weight_unit',
        'weight_value',
        'size',
        'page_count',
        'is_hardcover',
        'carton_quantity',
        'set_quantity',
        'shelf',
        'compartment',
        'short_description',
        'long_description',
        'video_url',
        'first_purchase_date',
        'last_purchase_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_original' => 'boolean',
            'unit_type' => UnitType::class,
            'weight_unit' => WeightUnit::class,
            'size' => SizeType::class,
            'is_hardcover' => 'boolean',
            'purchase_price' => 'decimal:2',
            'retail_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'weight_value' => 'decimal:2',
            'first_purchase_date' => 'date',
            'last_purchase_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the subcategory that owns the product.
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    /**
     * Get the supplier that owns the product.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('image_order');
    }

    /**
     * Get the tags for the product.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    /**
     * Get the purchase history for the product.
     */
    public function purchaseHistory(): HasMany
    {
        return $this->hasMany(PurchaseHistory::class);
    }

    /**
     * Update product quantity.
     */
    public function updateQuantity(int $quantity, string $operation = 'add'): void
    {
        if ($operation === 'add') {
            $this->quantity += $quantity;
            $this->available_quantity += $quantity;
        } elseif ($operation === 'subtract') {
            $this->quantity = max(0, $this->quantity - $quantity);
            $this->available_quantity = max(0, $this->available_quantity - $quantity);
        }

        $this->save();
    }

    /**
     * Check if product is low stock.
     */
    public function checkLowStock(): bool
    {
        if ($this->min_quantity === null) {
            return false;
        }

        return $this->quantity <= $this->min_quantity;
    }

    /**
     * Get the main image (first image).
     */
    public function getMainImage(): ?ProductImage
    {
        return $this->images()->orderBy('image_order')->first();
    }
}
