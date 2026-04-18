<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WaseetWebhookController extends Controller
{
    public function handle(Request $request)
    {
        \Illuminate\Support\Facades\Log::info("Incoming Waseet Webhook:", $request->all());

        $apiKey = $request->header('X-API-KEY');
        $setting = \App\Models\GatewaySetting::first();

        // Security check
        if (!$setting || $apiKey !== $setting->api_key) {
            \Illuminate\Support\Facades\Log::warning("Unauthorized Webhook Attempt. Provided Key: $apiKey");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $waseetOrderId = $request->input('order_id');
        $newWaseetStatus = $request->input('new_status');
        $oldWaseetStatus = $request->input('old_status') ?? 'غير معروف';

        $order = \App\Models\Order::where('waseet_order_id', $waseetOrderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update Waseet Status field
        $order->update([
            'waseet_status' => $newWaseetStatus
        ]);

        // Log the status change (History)
        \Illuminate\Support\Facades\DB::table('order_status_logs')->insert([
            'order_id' => $order->id,
            'status' => $newWaseetStatus,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Automate internal status mapping
        try {
            $orderService = app(\App\Services\Orders\OrderService::class);
            $newInternalStatus = $this->mapWaseetToInternalStatus($newWaseetStatus);

            if ($newInternalStatus && $newInternalStatus !== $order->status) {
                $orderService->changeOrderStatus($order, $newInternalStatus);
            } else {
                // If internal status hasn't changed, still send the specific Waseet status notification
                $notificationService = app(\App\Services\Notifications\NotificationService::class);
                $notificationService->sendOrderStatusNotification($order, $oldWaseetStatus, $newWaseetStatus);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to sync status for order {$order->id}: " . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'Webhook processed successfully'
        ]);
    }

    /**
     * Map Al-Waseet statuses to internal OrderStatus enum
     */
    protected function mapWaseetToInternalStatus(string $waseetStatus): ?\App\Enums\OrderStatus
    {
        return match ($waseetStatus) {
            'واصل', 'مباع', 'تم تسليم المبالغ' => \App\Enums\OrderStatus::COMPLETED,
            'راجع', 'تم استلام الراجع', 'إيداع راجع' => \App\Enums\OrderStatus::RETURNED,
            'ملغي' => \App\Enums\OrderStatus::CANCELLED,
            'قيد المعالجة', 'تم التجهيز' => \App\Enums\OrderStatus::PREPARED,
            default => null,
        };
    }
}
