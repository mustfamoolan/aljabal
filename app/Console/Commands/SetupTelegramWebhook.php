<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramBotService;

class SetupTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:webhook {url?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the Telegram Webhook URL';

    /**
     * Execute the console command.
     */
    public function handle(TelegramBotService $telegram)
    {
        $url = $this->argument('url');
        
        if (!$url) {
            $appUrl = config('app.url');
            $url = "{$appUrl}/api/webhooks/telegram";
        }

        $this->info("Setting webhook to: {$url}");

        $response = $telegram->setWebhook($url);

        if (isset($response['ok']) && $response['ok']) {
            $this->info('Webhook set successfully!');
        } else {
            $this->error('Failed to set webhook: ' . ($response['description'] ?? 'Unknown error'));
        }
    }
}
