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
            'representative_id' => $this->representative_id,
            'representative' => [
                'id' => $this->representative?->id,
                'name' => $this->representative?->name,
            ],
            'created_by' => [
                'id' => $this->createdBy?->id,
                'name' => $this->createdBy?->name,
            ],
            'governorate_id' => $this->governorate_id,
            'governorate' => $this->governorate?->name,
            'district_id' => $this->district_id,
            'district' => $this->district?->name,
            'gift_id' => $this->gift_id,
            'gift_name' => $this->gift?->name,
            'gift_item_price' => $this->gift?->price,
            'gift_box_id' => $this->gift_box_id,
            'gift_box_name' => $this->giftBox?->name,
            'gift_box_price' => $this->giftBox?->box_price,
            'is_withdrawal_order' => (bool)$this->is_withdrawal_order,
            'is_replacement' => (bool)$this->is_replacement,
            'order_items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'waseet_order_id' => $this->waseet_order_id,
            'waseet_tracking_url' => $this->waseet_tracking_url,
            'waseet_status' => $this->waseet_status,
            'status_logs' => $this->statusLogs()->orderBy('created_at', 'desc')->get()->map(fn($log) => [
                'status' => $log->status,
                'status_label' => $log->waseet_status ?: (
                    match($log->status) {
                        'new' => 'تم استلام الطلب الجديد',
                        'prepared' => 'تم تجهيز الطلب',
                        'delivered' => 'تم التسليم من قبل المندوب',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغي',
                        'returned' => 'راجع',
                        default => \App\Enums\OrderStatus::tryFrom($log->status)?->label() ?? $log->status
                    }
                ),
                'notes' => $log->notes,
                'date' => $log->created_at?->format('Y-m-d'),
                'time' => $log->created_at?->format('H:i'),
                'created_at_human' => $log->created_at?->diffForHumans(),
            ]),
            'created_at' => $this->created_at?->toDateTimeString(),
            'completed_at' => $this->completed_at?->toDateTimeString(),
        ];
    }
}
