<?php

namespace App\Notifications;

use App\Models\OrderPreparationCommissionSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

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
        return ['database', FcmChannel::class];
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        return FcmMessage::create()
            ->setData([
                'type' => 'commission_update',
                'new_value' => (string) $this->setting->commission_value,
                'old_value' => (string) $this->oldValue,
            ])
            ->setNotification(
                FcmNotification::create()
                    ->setTitle('تحديث عمولة التجهيز')
                    ->setBody("تم تغيير قيمة عمولة التجهيز إلى " . number_format($this->setting->commission_value) . " د.ع")
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
