<?php

namespace App\Notifications;

use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidNotification;

class WithdrawalStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public WithdrawalRequest $request,
        public string $status,
        public ?string $reason = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        $title = 'تحديث طلب السحب';
        $body = $this->status === 'approved' 
            ? "تم الموافقة على طلب السحب الخاص بك بمبلغ " . number_format($this->request->amount) . " د.ع"
            : "تم رفض طلب السحب الخاص بك. السبب: " . ($this->reason ?? 'غير محدد');

        return FcmMessage::create()
            ->data([
                'type' => 'withdrawal_status',
                'id' => (string) $this->request->id,
                'status' => $this->status,
                'amount' => (string) $this->request->amount,
            ])
            ->notification(
                FcmNotification::create()
                    ->title($title)
                    ->body($body)
            )
            ->android(
                AndroidConfig::create()
                    ->notification(AndroidNotification::create()->setChannelId('high_importance_channel'))
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'withdrawal_status',
            'withdrawal_request_id' => $this->request->id,
            'status' => $this->status,
            'reason' => $this->reason,
            'title' => 'تحديث طلب السحب',
            'body' => $this->status === 'approved' 
                ? "تم الموافقة على طلب السحب الخاص بك بمبلغ " . number_format($this->request->amount) . " د.ع"
                : "تم رفض طلب السحب الخاص بك. السبب: " . ($this->reason ?? 'غير محدد'),
        ];
    }
}
