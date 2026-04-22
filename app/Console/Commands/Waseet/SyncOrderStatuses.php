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
        OrderService $orderService,
        NotificationService $notificationService
    ) {
        $this->info("🚀 Starting Waseet Orders Sync...");

        // Get orders that are prepared or new and have a waseet_order_id
        $orders = Order::whereIn('status', [OrderStatus::PREPARED, OrderStatus::NEW])
            ->whereNotNull('waseet_order_id')
            ->get();

        if ($orders->isEmpty()) {
            $this->info("No active orders found to sync.");
            return;
        }

        $this->info("Found {$orders->count()} active orders to check.");

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
                    'status' => $newWaseetStatus,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Map Waseet status to internal status
                $newInternalStatus = $this->mapWaseetToInternalStatus($newWaseetStatus);

                if ($newInternalStatus && $newInternalStatus !== $order->status) {
                    $this->info("   -> Changing internal status to {$newInternalStatus->name}");
                    $orderService->changeOrderStatus($order, $newInternalStatus);
                } else {
                    // Just a text change (e.g., 'مباع' to 'واصل' which are both COMPLETED, or some intermediate status)
                    $notificationService->sendOrderStatusNotification($order, $oldWaseetStatus, $newWaseetStatus);
                }

                $updatedCount++;

            } catch (\Exception $e) {
                $this->error("Error syncing order #{$order->id}: " . $e->getMessage());
                Log::error("Waseet Sync Error for Order #{$order->id}: " . $e->getMessage());
            }
        }

        $this->info("✨ Sync complete! Updated {$updatedCount} orders.");
    }

    /**
     * Map Al-Waseet statuses to internal OrderStatus enum
     */
    protected function mapWaseetToInternalStatus(string $waseetStatus): ?OrderStatus
    {
        return match ($waseetStatus) {
            'واصل', 'مباع', 'تم تسليم المبالغ', 'تم التسليم للزبون' => OrderStatus::COMPLETED,
            'راجع', 'تم استلام الراجع', 'إيداع راجع' => OrderStatus::RETURNED,
            'ملغي' => OrderStatus::CANCELLED,
            'قيد المعالجة', 'تم التجهيز', 'تم الاستلام من قبل المندوب' => OrderStatus::PREPARED,
            default => null,
        };
    }
}
