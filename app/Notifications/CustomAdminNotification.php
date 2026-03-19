<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class CustomAdminNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public array $data = []
    ) {
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        $fcmMessage = FcmMessage::create()
            ->data(array_merge([
                'type' => 'custom',
            ], array_map('strval', $this->data)))
            ->notification(
                FcmNotification::create()
                    ->title($this->title)
                    ->body($this->body)
            );

        if (isset($this->data['image']) && !empty($this->data['image'])) {
            $fcmMessage->notification(
                FcmNotification::create()
                    ->title($this->title)
                    ->body($this->body)
                    ->image($this->data['image'])
            );
        }

        return $fcmMessage;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'custom',
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }
}
