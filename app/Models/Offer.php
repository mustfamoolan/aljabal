<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'price',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
