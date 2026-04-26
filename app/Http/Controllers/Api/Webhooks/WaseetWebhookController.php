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
            \Illuminate\Support\Facades\Log::warning("Order with Waseet ID {$waseetOrderId} NOT FOUND in database.");
            return response()->json(['message' => 'Order not found'], 404);
        }

        \Illuminate\Support\Facades\Log::info("Processing status update for Order #{$order->id} (Waseet ID: {$waseetOrderId}): {$order->waseet_status} -> {$newWaseetStatus}");

        // Update Waseet Status field
        $order->update([
            'waseet_status' => $newWaseetStatus
        ]);

        // Log the status change (History)
        \App\Models\OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $order->status ? $order->status->value : 'unknown',
            'waseet_status' => $newWaseetStatus,
            'notes' => "تحديث تلقائي من الوسيط: من {$oldWaseetStatus} إلى {$newWaseetStatus}",
        ]);

        // Send notification about the waseet status change
        try {
            $notificationService = app(\App\Services\Notifications\NotificationService::class);
            $notificationService->sendOrderStatusNotification($order, $oldWaseetStatus, $newWaseetStatus);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send notification for order {$order->id}: " . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'Webhook processed successfully'
        ]);
    }
}
