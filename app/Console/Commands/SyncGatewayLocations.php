<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GatewayIntegrationService;

class SyncGatewayLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:sync-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Governorates and Districts from the Gateway (Waseet API)';

    /**
     * Execute the console command.
     */
    public function handle(GatewayIntegrationService $gatewayService)
    {
        $this->info('Starting locations synchronization...');
        
        $result = $gatewayService->syncLocations();

        if ($result['success']) {
            $this->info($result['message']);
        } else {
            $this->error($result['message']);
        }
    }
}
