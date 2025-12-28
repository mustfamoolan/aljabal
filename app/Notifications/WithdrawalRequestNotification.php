<?php

namespace App\Notifications;

use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

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
        return ['database'];
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
            'message' => "طلب سحب جديد من {$representative->name} بمبلغ " . format_currency($this->withdrawalRequest->amount),
            'withdrawal_request_id' => $this->withdrawalRequest->id,
            'representative_id' => $representative->id,
            'representative_name' => $representative->name,
            'amount' => $this->withdrawalRequest->amount,
            'url' => route('admin.withdrawals.show', $this->withdrawalRequest),
        ];
    }
}
