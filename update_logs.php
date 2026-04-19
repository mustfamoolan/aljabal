<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\OrderStatusLog;

echo "Processing orders...\n";

$orders = Order::whereNotNull('waseet_order_id')->get();

foreach ($orders as $order) {
    echo "Updating order #{$order->id}\n";
    
    // Check if we already have the basic logs
    $hasCreated = OrderStatusLog::where('order_id', $order->id)->where('waseet_status', 'تم استلام الطلب الجديد')->exists();
    
    if (!$hasCreated) {
        // 1. Add "Order Created" Log
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => 'new',
            'waseet_status' => 'تم استلام الطلب الجديد',
            'notes' => 'بدء رحلة الطلب من خلال المندوب.',
            'created_at' => $order->created_at,
        ]);
        
        // 2. Add "Prepared/Sent" Log
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => 'prepared',
            'waseet_status' => 'تم تجهيز الطلب وإرساله للوسيط',
            'notes' => 'تم تغليف المنتج وتسليمه لشركة الشحن.',
            'created_at' => $order->created_at->addMinutes(15),
        ]);
    }
}

echo "Done!\n";
