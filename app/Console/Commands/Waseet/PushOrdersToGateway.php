<?php

namespace App\Console\Commands\Waseet;

use Illuminate\Console\Command;

class PushOrdersToGateway extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waseet:push-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push all local Waseet order IDs to the Gateway for tracking';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🚀 Starting to push all local Waseet IDs to Gateway...");

        $orderIds = \App\Models\Order::whereNotNull('waseet_order_id')
            ->pluck('waseet_order_id')
            ->unique()
            ->values()
            ->toArray();

        $count = count($orderIds);
        if ($count === 0) {
            $this->warn("No orders found with Waseet IDs.");
            return;
        }

        $this->info("Found $count unique IDs to push.");

        $setting = \App\Models\GatewaySetting::first();
        if (!$setting || !$setting->is_connected) {
            $this->error("Gateway is not connected. Please check settings.");
            return;
        }

        $gatewayService = app(\App\Services\GatewayIntegrationService::class);
        $url = rtrim(config('services.gateway.url', 'https://salesflowi.cloud'), '/') . '/api/gateway/track-bulk';

        // Chunk IDs to avoid payload size issues
        $chunks = array_chunk($orderIds, 50);
        $successCount = 0;

        foreach ($chunks as $chunk) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(30)
                    ->withHeaders([
                        'Project' => $setting->project_name,
                        'X-API-KEY' => $setting->api_key,
                    ])
                    ->post($url, ['ids' => $chunk]);

                if ($response->successful()) {
                    $successCount += count($chunk);
                    $this->line("✅ Pushed " . count($chunk) . " IDs...");
                } else {
                    $this->error("❌ Failed to push chunk: " . $response->body());
                }
            } catch (\Exception $e) {
                $this->error("❌ Error pushing chunk: " . $e->getMessage());
            }
        }

        $this->info("✨ Finished. Pushed $successCount/$count IDs to Gateway.");
    }
}
