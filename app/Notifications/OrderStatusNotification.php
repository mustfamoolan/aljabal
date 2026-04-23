<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order,
        public string $oldStatus,
        public string $newStatus
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
                'type' => 'order_status_change',
                'id' => (string) $this->order->id,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
            ])
            ->notification(
                FcmNotification::create()
                    ->title('تحديث حالة الطلب # ' . $this->order->id)
                    ->body("تغيرت حالة الطلب من {$this->oldStatus} إلى {$this->newStatus}")
            )
            ->android([
                'notification' => [
                    'channel_id' => 'high_importance_channel',
                ],
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_status_change',
            'order_id' => $this->order->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'title' => 'تحديث حالة الطلب # ' . $this->order->id,
            'body' => "تغيرت حالة الطلب من {$this->oldStatus} إلى {$this->newStatus}",
            'data' => [
                'type' => 'order_status_change',
                'id' => $this->order->id,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
            ],
        ];
    }
}
