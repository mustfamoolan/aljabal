<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class LowStockNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Product $product
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): FcmMessage
    {
        $productUrl = route('inventory.products.show', $this->product);

        return FcmMessage::create()
            ->setData([
                'type' => 'low_stock',
                'product_id' => (string) $this->product->id,
                'product_name' => $this->product->name,
                'quantity' => (string) $this->product->quantity,
                'min_quantity' => (string) $this->product->min_quantity,
                'url' => $productUrl,
            ])
            ->setNotification(
                FcmNotification::create()
                    ->setTitle('تنبيه: مخزون منخفض')
                    ->setBody("المنتج {$this->product->name} وصل للحد الأدنى. الكمية: {$this->product->quantity}")
            );
    }
}
