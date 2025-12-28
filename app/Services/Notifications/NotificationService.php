<?php

namespace App\Services\Notifications;

use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
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

            // Send notification to each user and save to database
            foreach ($users as $user) {
                try {
                    // Save notification to database
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

                    // Send FCM notification if user has token
                    if ($user->fcm_token) {
                        Log::info('Sending FCM notification to user', [
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                            'fcm_token' => substr($user->fcm_token, 0, 20) . '...',
                            'product_id' => $product->id,
                        ]);

                        try {
                            $user->notify($notification);
                            Log::info('FCM notification sent successfully to user', [
                                'user_id' => $user->id,
                                'product_id' => $product->id,
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error sending FCM notification', [
                                'user_id' => $user->id,
                                'product_id' => $product->id,
                                'error' => $e->getMessage(),
                                'error_class' => get_class($e),
                                'trace' => $e->getTraceAsString(),
                            ]);

                            // Continue with other users even if one fails
                        }
                    } else {
                        Log::warning('User does not have FCM token', [
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                        ]);
                    }
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
    public function saveNotificationToDatabase(User $user, array $data): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'title' => $data['title'],
            'body' => $data['body'],
            'data' => $data['data'] ?? null,
        ]);
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
