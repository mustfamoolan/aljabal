<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewOrderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): FcmMessage
    {
        return FcmMessage::create()
            ->data([
                'type' => 'order',
                'id' => (string) $this->order->id,
                'customer_name' => $this->order->customer_name,
                'total_amount' => (string) $this->order->total_amount,
            ])
            ->notification(
                FcmNotification::create()
                    ->setTitle('طلب جديد # ' . $this->order->id)
                    ->setBody("تم استلام طلب جديد من {$this->order->customer_name} بمبلغ " . number_format($this->order->total_amount) . " د.ع")
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order',
            'id' => $this->order->id,
            'title' => 'طلب جديد # ' . $this->order->id,
            'body' => "تم استلام طلب جديد من {$this->order->customer_name} بمبلغ " . number_format($this->order->total_amount) . " د.ع",
            'data' => [
                'type' => 'order',
                'id' => $this->order->id,
                'customer_name' => $this->order->customer_name,
                'total_amount' => $this->order->total_amount,
            ],
        ];
    }
}
