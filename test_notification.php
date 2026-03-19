<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\Notifications\NotificationService;

$product = Product::first();
if ($product) {
    echo "Sending test notification for product: " . $product->name . "\n";
    app(NotificationService::class)->sendNewProductNotification($product);
    echo "Done.\n";
} else {
    echo "No product found.\n";
}
