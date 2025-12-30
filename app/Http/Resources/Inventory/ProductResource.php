<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'product_type' => $this->product_type->value,
            'product_type_label' => $this->product_type->label(),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'name' => $this->supplier->name,
                ];
            }),
            'author' => $this->author,
            'publisher' => $this->publisher,
            'purchase_price' => $this->purchase_price,
            'retail_price' => $this->retail_price,
            'wholesale_price' => $this->wholesale_price,
            'quantity' => $this->quantity,
            'min_quantity' => $this->min_quantity,
            'available_quantity' => $this->available_quantity,
            'unit_type' => $this->unit_type?->value,
            'unit_type_label' => $this->unit_type?->label(),
            'weight_unit' => $this->weight_unit?->value,
            'weight_unit_label' => $this->weight_unit?->label(),
            'weight_value' => $this->weight_value,
            'carton_quantity' => $this->carton_quantity,
            'set_quantity' => $this->set_quantity,
            'shelf' => $this->shelf,
            'compartment' => $this->compartment,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'color' => $this->color,
            'first_purchase_date' => $this->first_purchase_date?->toDateString(),
            'last_purchase_date' => $this->last_purchase_date?->toDateString(),
            'is_active' => $this->is_active,
            'is_low_stock' => $this->checkLowStock(),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => storage_url($image->image_path),
                        'order' => $image->image_order,
                    ];
                });
            }),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ];
                });
            }),
            'purchase_history' => $this->whenLoaded('purchaseHistory', function () {
                return $this->purchaseHistory->map(function ($purchase) {
                    return [
                        'id' => $purchase->id,
                        'quantity' => $purchase->quantity,
                        'purchase_price' => $purchase->purchase_price,
                        'total_amount' => $purchase->total_amount,
                        'purchase_date' => $purchase->purchase_date->toDateString(),
                        'supplier' => $purchase->supplier ? [
                            'id' => $purchase->supplier->id,
                            'name' => $purchase->supplier->name,
                        ] : null,
                        'created_by' => $purchase->createdBy ? [
                            'id' => $purchase->createdBy->id,
                            'name' => $purchase->createdBy->name,
                        ] : null,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
