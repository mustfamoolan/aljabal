<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ProductPriceDiscountNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Product $product,
        public float $oldPrice,
        public float $newPrice
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
                'type' => 'price_discount',
                'id' => (string) $this->product->id,
                'old_price' => (string) $this->oldPrice,
                'new_price' => (string) $this->newPrice,
            ])
            ->notification(
                FcmNotification::create()
                    ->title("تخفيض على السعر: {$this->product->name}")
                    ->body("تم تخفيض السعر من " . number_format($this->oldPrice) . " إلى " . number_format($this->newPrice) . " د.ع")
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
            'type' => 'price_discount',
            'product_id' => $this->product->id,
            'old_price' => $this->oldPrice,
            'new_price' => $this->newPrice,
            'title' => "تخفيض على السعر: {$this->product->name}",
            'body' => "تم تخفيض السعر من " . number_format($this->oldPrice) . " إلى " . number_format($this->newPrice) . " د.ع",
        ];
    }
}
