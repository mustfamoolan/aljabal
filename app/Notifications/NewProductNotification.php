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
        return ['database', FcmChannel::class];
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        return FcmMessage::create()
            ->setData([
                'type' => 'new_product',
                'id' => (string) $this->product->id,
                'name' => $this->product->name,
            ])
            ->setNotification(
                FcmNotification::create()
                    ->setTitle('منتج جديد متوفر')
                    ->setBody("تمت إضافة منتج جديد للمخزن: {$this->product->name}")
                    ->setImage($this->product->image_url)
            );
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
