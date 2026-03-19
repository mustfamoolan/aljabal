<?php

namespace App\Services\Notifications;

use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use App\Models\Representative;
use App\Models\Order;
use App\Models\WithdrawalRequest;
use App\Models\OrderPreparationCommissionSetting;
use App\Notifications\LowStockNotification;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusNotification;
use App\Notifications\WithdrawalRequestNotification;
use App\Notifications\WithdrawalStatusNotification;
use App\Notifications\NewProductNotification;
use App\Notifications\ProductPriceDiscountNotification;
use App\Notifications\CommissionUpdateNotification;
use App\Notifications\CustomAdminNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send low stock notification for a product
     */
    public function sendLowStockNotification(Product $product): void
    {
        try {
            Log::info('Checking low stock notification for product', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $product->quantity,
                'min_quantity' => $product->min_quantity,
            ]);

            // Get users with inventory permissions
            $users = $this->getUsersWithInventoryPermissions();

            Log::info('Users with inventory permissions', [
                'count' => $users->count(),
                'user_ids' => $users->pluck('id')->toArray(),
            ]);

            if ($users->isEmpty()) {
                Log::warning('No users with inventory permissions found for low stock notification', [
                    'product_id' => $product->id,
                ]);
                return;
            }

            // Create notification instance
            $notification = new LowStockNotification($product);

            // Send notification to each user
            foreach ($users as $user) {
                try {
                    // Send via Laravel Notification System (Database + FCM)
                    $user->notify($notification);

                    // Also save to our custom 'notifications' table for the specific app logic
                    // if it's not already being handled by the 'database' channel in Notification
                    $this->saveNotificationToDatabase($user, [
                        'type' => 'low_stock',
                        'title' => 'تنبيه: مخزون منخفض',
                        'body' => "المنتج {$product->name} وصل للحد الأدنى. الكمية: {$product->quantity}",
                        'data' => [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'quantity' => $product->quantity,
                            'min_quantity' => $product->min_quantity,
                            'url' => route('inventory.products.show', $product),
                        ],
                    ]);

                    Log::info('Low stock notification sent to user', [
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send notification to user', [
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send low stock notification', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get users with inventory permissions (create or update)
     * Includes users with direct permissions or permissions via roles
     */
    public function getUsersWithInventoryPermissions()
    {
        $requiredPermissions = [
            'inventory.products.create',
            'inventory.products.update',
        ];

        // Use Spatie Permission's permission() method which checks both direct and role permissions
        $users = collect();

        foreach ($requiredPermissions as $permission) {
            $usersWithPermission = User::permission($permission)
                ->where('is_active', true)
                ->get();
            $users = $users->merge($usersWithPermission);
        }

        // Get unique users
        $allUsers = $users->unique('id');

        Log::info('Users with inventory permissions (direct or via roles)', [
            'total_count' => $allUsers->count(),
            'user_ids' => $allUsers->pluck('id')->toArray(),
            'user_names' => $allUsers->pluck('name')->toArray(),
        ]);

        return $allUsers;
    }

    /**
     * Save notification to database
     */
    public function saveNotificationToDatabase($notifiable, array $data): Notification
    {
        return Notification::create([
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
            'user_id' => $notifiable instanceof User ? $notifiable->id : null,
            'type' => $data['type'],
            'title' => $data['title'],
            'body' => $data['body'],
            'data' => $data['data'] ?? null,
        ]);
    }

    /**
     * Send new order notification to admins
     */
    public function sendNewOrderNotification(\App\Models\Order $order): void
    {
        try {
            // Get admins to notify
            $admins = User::role('admin')->where('is_active', true)->get();

            if ($admins->isEmpty()) {
                Log::warning('No active admins found for new order notification', ['order_id' => $order->id]);
                return;
            }

            $notification = new \App\Notifications\NewOrderNotification($order);

            foreach ($admins as $admin) {
                try {
                    // Send via Laravel Notification System (Database + FCM)
                    $admin->notify($notification);

                    // Save to our custom 'notifications' table
                    $this->saveNotificationToDatabase($admin, [
                        'type' => 'order',
                        'title' => 'طلب جديد # ' . $order->id,
                        'body' => "تم استلام طلب جديد من {$order->customer_name} بمبلغ " . number_format($order->total_amount) . " د.ع",
                        'data' => [
                            'type' => 'order',
                            'id' => $order->id,
                            'customer_name' => $order->customer_name,
                            'total_amount' => $order->total_amount,
                        ],
                    ]);

                    Log::info('New order notification sent to admin', [
                        'admin_id' => $admin->id,
                        'order_id' => $order->id,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error sending new order notification', [
                        'admin_id' => $admin->id,
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send new order notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send withdrawal request notification to admins
     */
    public function sendWithdrawalRequestNotification(\App\Models\WithdrawalRequest $request): void
    {
        try {
            // Get admins to notify
            $admins = User::role('admin')->where('is_active', true)->get();

            if ($admins->isEmpty()) {
                Log::warning('No active admins found for withdrawal notification', ['request_id' => $request->id]);
                return;
            }

            $notification = new \App\Notifications\WithdrawalRequestNotification($request);

            foreach ($admins as $admin) {
                try {
                    // Send via Laravel Notification System (Database + FCM)
                    $admin->notify($notification);

                    // Save to our custom 'notifications' table
                    $this->saveNotificationToDatabase($admin, [
                        'type' => 'withdrawal_request',
                        'title' => 'طلب سحب جديد',
                        'body' => "طلب سحب جديد من {$request->representative->name} بمبلغ " . number_format($request->amount) . " د.ع",
                        'data' => [
                            'type' => 'withdrawal_request',
                            'id' => $request->id,
                            'representative_id' => $request->representative->id,
                            'representative_name' => $request->representative->name,
                            'amount' => $request->amount,
                            'url' => route('admin.withdrawals.show', $request),
                        ],
                    ]);

                    Log::info('Withdrawal request notification sent to admin', [
                        'admin_id' => $admin->id,
                        'request_id' => $request->id,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error sending withdrawal notification', [
                        'admin_id' => $admin->id,
                        'request_id' => $request->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send withdrawal notifications', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send order status change notification
     */
    public function sendOrderStatusNotification(\App\Models\Order $order, string $oldStatus, string $newStatus): void
    {
        try {
            // Get admins to notify
            $admins = User::role('admin')->where('is_active', true)->get();

            if ($admins->isEmpty()) {
                return;
            }

            $notification = new \App\Notifications\OrderStatusNotification($order, $oldStatus, $newStatus);

            foreach ($admins as $admin) {
                try {
                    // Send via Laravel Notification System (Database + FCM)
                    $admin->notify($notification);

                    // Save to our custom 'notifications' table
                    $this->saveNotificationToDatabase($admin, [
                        'type' => 'order_status_change',
                        'title' => 'تحديث حالة الطلب # ' . $order->id,
                        'body' => "تغيرت حالة الطلب من {$oldStatus} إلى {$newStatus}",
                        'data' => [
                            'type' => 'order_status_change',
                            'id' => $order->id,
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                        ],
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error sending order status notification to admin', [
                        'admin_id' => $admin->id,
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Also notify the representative
            if ($order->representative) {
                try {
                    $representative = $order->representative;
                    $representative->notify($notification);

                    $this->saveNotificationToDatabase($representative, [
                        'type' => 'order_status_change',
                        'title' => 'تحديث حالة الطلب # ' . $order->id,
                        'body' => "تغيرت حالة الطلب من {$oldStatus} إلى {$newStatus}",
                        'data' => [
                            'type' => 'order_status_change',
                            'id' => $order->id,
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                        ],
                    ]);

                    Log::info('Order status notification sent to representative', [
                        'representative_id' => $representative->id,
                        'order_id' => $order->id,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error sending order status notification to representative', [
                        'representative_id' => $order->representative_id,
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send order status notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendWithdrawalStatusNotification(WithdrawalRequest $request, string $status, ?string $reason = null): void
    {
        try {
            $representative = $request->representative;
            if (!$representative) return;

            $title = 'تحديث طلب السحب';
            $body = $status === 'approved' 
                ? "تم الموافقة على طلب السحب الخاص بك بمبلغ " . number_format($request->amount) . " د.ع"
                : "تم رفض طلب السحب الخاص بك. السبب: " . ($reason ?? 'غير محدد');

            // Save to database first
            $this->saveNotificationToDatabase($representative, [
                'type' => 'financial',
                'title' => $title,
                'body' => $body,
                'data' => [
                    'withdrawal_request_id' => $request->id,
                    'status' => $status,
                    'amount' => $request->amount,
                ],
            ]);

            // Attempt to send FCM
            try {
                $notification = new WithdrawalStatusNotification($request, $status, $reason);
                $representative->notify($notification);
            } catch (\Exception $e) {
                Log::error('FCM Error (WithdrawalStatus): ' . $e->getMessage());
            }

            Log::info('Withdrawal status notification processed', [
                'representative_id' => $representative->id,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process withdrawal status notification', ['error' => $e->getMessage()]);
        }
    }

    public function sendNewProductNotification(Product $product): void
    {
        try {
            $representatives = Representative::where('is_active', true)->get();
            $notification = new NewProductNotification($product);

            foreach ($representatives as $rep) {
                // Save to database
                $this->saveNotificationToDatabase($rep, [
                    'type' => 'product',
                    'title' => 'منتج جديد متوفر',
                    'body' => "تمت إضافة منتج جديد للمخزن: {$product->name}",
                    'data' => ['type' => 'new_product', 'id' => $product->id],
                ]);

                // Attempt FCM
                try {
                    $rep->notify($notification);
                } catch (\Exception $e) {
                    Log::error('FCM Error (NewProduct): ' . $e->getMessage());
                }
            }
            Log::info('New product notifications processed', [
                'product_id' => $product->id,
                'count' => $representatives->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process new product notification', ['product_id' => $product->id, 'error' => $e->getMessage()]);
        }
    }

    public function sendProductPriceDiscountNotification(Product $product, float $oldPrice, float $newPrice): void
    {
        try {
            $representatives = Representative::where('is_active', true)->get();
            $notification = new ProductPriceDiscountNotification($product, $oldPrice, $newPrice);

            foreach ($representatives as $rep) {
                // Save to database
                $this->saveNotificationToDatabase($rep, [
                    'type' => 'product',
                    'title' => "تخفيض على السعر: {$product->name}",
                    'body' => "تم تخفيض السعر من " . number_format($oldPrice) . " إلى " . number_format($newPrice) . " د.ع",
                    'data' => ['type' => 'price_discount', 'id' => $product->id, 'old_price' => $oldPrice, 'new_price' => $newPrice],
                ]);

                // Attempt FCM
                try {
                    $rep->notify($notification);
                } catch (\Exception $e) {
                    Log::error('FCM Error (PriceDiscount): ' . $e->getMessage());
                }
            }
            Log::info('Price discount notifications processed', [
                'product_id' => $product->id,
                'count' => $representatives->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process product discount notification', ['product_id' => $product->id, 'error' => $e->getMessage()]);
        }
    }

    public function sendCommissionUpdateNotification(OrderPreparationCommissionSetting $setting, float $oldValue): void
    {
        try {
            $representatives = Representative::where('is_active', true)->get();
            $notification = new CommissionUpdateNotification($setting, $oldValue);

            foreach ($representatives as $rep) {
                // Save to database
                $this->saveNotificationToDatabase($rep, [
                    'type' => 'settings',
                    'title' => 'تحديث عمولة التجهيز',
                    'body' => "تم تغيير قيمة عمولة التجهيز إلى " . number_format($setting->commission_value) . " د.ع",
                    'data' => ['type' => 'commission_update', 'new_value' => $setting->commission_value],
                ]);

                // Attempt FCM
                try {
                    $rep->notify($notification);
                } catch (\Exception $e) {
                    Log::error('FCM Error (CommissionUpdate): ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to process commission update notification', ['error' => $e->getMessage()]);
        }
    }

    public function sendCustomNotification($recipient, string $title, string $body, array $data = []): void
    {
        try {
            // Save to database
            $this->saveNotificationToDatabase($recipient, [
                'type' => 'admin_msg',
                'title' => $title,
                'body' => $body,
                'data' => array_merge(['type' => 'custom'], $data),
            ]);

            // Attempt FCM
            try {
                $notification = new CustomAdminNotification($title, $body, $data);
                $recipient->notify($notification);
            } catch (\Exception $e) {
                Log::error('FCM Error (Custom): ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Failed to process custom notification', ['recipient_id' => $recipient->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Check if product has low stock
     */
    public function checkLowStock(Product $product): bool
    {
        if ($product->min_quantity === null) {
            return false;
        }

        return $product->quantity <= $product->min_quantity;
    }
}
