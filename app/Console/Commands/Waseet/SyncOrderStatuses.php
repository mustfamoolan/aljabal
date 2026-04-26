<?php

namespace App\Console\Commands\Waseet;

use App\Models\Order;
use App\Enums\OrderStatus;
use App\Services\GatewayIntegrationService;
use App\Services\Orders\OrderService;
use App\Services\Notifications\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SyncOrderStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waseet:sync-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync active order statuses from Waseet Gateway';

    /**
     * Execute the console command.
     */
    public function handle(
        GatewayIntegrationService $gatewayService,
        NotificationService $notificationService
    ) {
        $this->info("🚀 Starting Waseet Orders Sync...");

        // Get orders that are sent to gateway but still need updates
        $orders = Order::where('status', OrderStatus::SENT_TO_GATEWAY)
            ->whereNotNull('waseet_order_id')
            ->get();

        if ($orders->isEmpty()) {
            $this->info("No active orders in 'SENT_TO_GATEWAY' found to sync.");
            return;
        }

        $this->info("Found {$orders->count()} orders to check.");

        $updatedCount = 0;

        foreach ($orders as $order) {
            try {
                $details = $gatewayService->getWaseetOrderDetails($order->waseet_order_id);

                if (empty($details) || !isset($details['status'])) {
                    continue;
                }

                $newWaseetStatus = $details['status'];
                $oldWaseetStatus = $order->waseet_status ?? 'غير معروف';

                if ($newWaseetStatus === $order->waseet_status) {
                    continue; // No change
                }

                $this->info("Order #{$order->id} status changed from {$oldWaseetStatus} to {$newWaseetStatus}");

                // Update Waseet Status field
                $order->update([
                    'waseet_status' => $newWaseetStatus
                ]);

                // Log the status change (History)
                DB::table('order_status_logs')->insert([
                    'order_id' => $order->id,
                    'status' => $order->status->value,
                    'waseet_status' => $newWaseetStatus,
                    'notes' => "تحديث تلقائي (مزامنة): من {$oldWaseetStatus} إلى {$newWaseetStatus}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Send notification
                $notificationService->sendOrderStatusNotification($order, $oldWaseetStatus, $newWaseetStatus);

                $updatedCount++;

            } catch (\Exception $e) {
                $this->error("Error syncing order #{$order->id}: " . $e->getMessage());
                Log::error("Waseet Sync Error for Order #{$order->id}: " . $e->getMessage());
            }
        }

        $this->info("✨ Sync complete! Updated {$updatedCount} orders.");
    }
}
