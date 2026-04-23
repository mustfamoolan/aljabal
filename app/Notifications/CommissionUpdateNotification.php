<?php

namespace App\Notifications;

use App\Models\OrderPreparationCommissionSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidNotification;

class CommissionUpdateNotification extends Notification
{
    use Queueable;

    public function __construct(
        public OrderPreparationCommissionSetting $setting,
        public float $oldValue
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
                'type' => 'commission_update',
                'new_value' => (string) $this->setting->commission_value,
                'old_value' => (string) $this->oldValue,
            ])
            ->notification(
                FcmNotification::create()
                    ->title('تحديث عمولة التجهيز')
                    ->body("تم تغيير قيمة عمولة التجهيز إلى " . number_format($this->setting->commission_value) . " د.ع")
            )
            ->android(
                AndroidConfig::create()
                    ->notification(AndroidNotification::create()->setChannelId('high_importance_channel'))
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'commission_update',
            'new_value' => $this->setting->commission_value,
            'old_value' => $this->oldValue,
            'title' => 'تحديث عمولة التجهيز',
            'body' => "تم تغيير قيمة عمولة التجهيز إلى " . number_format($this->setting->commission_value) . " د.ع",
        ];
    }
}
