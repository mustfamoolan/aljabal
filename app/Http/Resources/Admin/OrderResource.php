<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'customer_name' => $this->customer_name,
            'customer_address' => $this->customer_address,
            'customer_phone' => $this->customer_phone,
            'customer_phone_2' => $this->customer_phone_2,
            'customer_social_media' => $this->customer_social_media,
            'customer_notes' => $this->customer_notes,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_badge' => $this->status->badgeClass(),
            'total_amount' => $this->total_amount,
            'total_profit' => $this->total_profit,
            'preparation_commission' => $this->preparation_commission,
            'final_profit' => $this->final_profit,
            'delivery_fee' => $this->delivery_fee,
            'gift_price' => $this->gift_price,
            'representative' => [
                'id' => $this->representative?->id,
                'name' => $this->representative?->name,
            ],
            'created_by' => [
                'id' => $this->createdBy?->id,
                'name' => $this->createdBy?->name,
            ],
            'governorate' => $this->governorate?->name,
            'district' => $this->district?->name,
            'gift_name' => $this->gift?->name,
            'gift_box_name' => $this->giftBox?->name,
            'order_items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'completed_at' => $this->completed_at?->toDateTimeString(),
        ];
    }
}
