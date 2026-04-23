<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidNotification;

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
            ->data([
                'type' => 'low_stock',
                'id' => (string) $this->product->id,
                'name' => $this->product->name,
                'quantity' => (string) $this->product->quantity,
                'url' => $productUrl,
            ])
            ->notification(
                FcmNotification::create()
                    ->title('تنبيه: مخزون منخفض')
                    ->body("المنتج {$this->product->name} وصل للحد الأدنى. الكمية: {$this->product->quantity}")
            )
            ->android(
                AndroidConfig::create()
                    ->notification(AndroidNotification::create()->setChannelId('high_importance_channel'))
            );
    }
}
