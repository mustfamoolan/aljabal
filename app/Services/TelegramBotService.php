<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    protected string $token;
    protected string $baseUrl;

    public function __construct()
    {
        $this->token = (string) config('services.telegram-bot-api.token', '');
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Send a text message to a specific chat ID.
     */
    public function sendMessage(string|int $chatId, string $text, array $replyMarkup = []): bool
    {
        if (empty($this->token)) {
            Log::warning('Telegram bot token is not set.');
            return false;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if (!empty($replyMarkup)) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        try {
            $response = Http::post("{$this->baseUrl}/sendMessage", $payload);

            if (!$response->successful()) {
                Log::error('Telegram API Error: ' . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Telegram Service Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a photo to a specific chat ID.
     */
    public function sendPhoto(string|int $chatId, string $photoUrl, string $caption = '', array $replyMarkup = []): bool
    {
        if (empty($this->token)) {
            Log::warning('Telegram bot token is not set.');
            return false;
        }

        $payload = [
            'chat_id' => $chatId,
            'photo' => $photoUrl,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ];

        if (!empty($replyMarkup)) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        try {
            $response = Http::post("{$this->baseUrl}/sendPhoto", $payload);

            if (!$response->successful()) {
                Log::error('Telegram API Error (sendPhoto): ' . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Telegram Service Exception (sendPhoto): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Set the Webhook URL for the bot.
     */
    public function setWebhook(string $url): array
    {
        if (empty($this->token)) {
            return ['ok' => false, 'description' => 'Token not set'];
        }

        $response = Http::post("{$this->baseUrl}/setWebhook", [
            'url' => $url,
        ]);

        return $response->json();
    }
}
