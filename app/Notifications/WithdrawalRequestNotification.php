<?php

namespace App\Notifications;

use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class WithdrawalRequestNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public WithdrawalRequest $withdrawalRequest
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
        $representative = $this->withdrawalRequest->representative;
        $title = 'طلب سحب جديد';
        $body = "طلب سحب جديد من {$representative->name} بمبلغ " . number_format($this->withdrawalRequest->amount) . " د.ع";

        return FcmMessage::create()
            ->data([
                'type' => 'withdrawal_request',
                'id' => (string) $this->withdrawalRequest->id,
                'representative_id' => (string) $this->withdrawalRequest->representative_id, // Assuming representative_id exists on withdrawalRequest or is derived
            ])
            ->notification(
                FcmNotification::create()
                    ->title($title)
                    ->body($body)
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $representative = $this->withdrawalRequest->representative;

        return [
            'type' => 'withdrawal_request',
            'title' => 'طلب سحب جديد',
            'body' => "طلب سحب جديد من {$representative->name} بمبلغ " . number_format($this->withdrawalRequest->amount) . " د.ع",
            'data' => [
                'type' => 'withdrawal_request',
                'id' => $this->withdrawalRequest->id,
                'representative_id' => $representative->id,
                'representative_name' => $representative->name,
                'amount' => $this->withdrawalRequest->amount,
                'url' => route('admin.withdrawals.show', $this->withdrawalRequest),
            ],
        ];
    }
}
