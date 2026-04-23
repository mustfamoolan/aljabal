<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewProductNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Product $product
    ) {
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        return FcmMessage::create()
            ->data([
                'type' => 'new_product',
                'id' => (string) $this->product->id,
                'name' => $this->product->name,
            ])
            ->notification(
                FcmNotification::create()
                    ->title('منتج جديد متوفر')
                    ->body("تمت إضافة منتج جديد للمخزن: {$this->product->name}")
                    ->image($this->product->image_url)
            )
            ->android([
                'notification' => [
                    'channel_id' => 'high_importance_channel',
                ],
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_product',
            'product_id' => $this->product->id,
            'title' => 'منتج جديد متوفر',
            'body' => "تمت إضافة منتج جديد للمخزن: {$this->product->name}",
            'image' => $this->product->image_url,
        ];
    }
}
