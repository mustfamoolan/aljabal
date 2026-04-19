<?php

namespace App\Services\Orders;

use App\Enums\OrderStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\GiftSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Representative;
use App\Models\RepresentativeTransaction;
use App\Models\User;
use App\Services\Representatives\RepresentativeAccountService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected OrderCommissionService $commissionService,
        protected RepresentativeAccountService $accountService,
        protected GiftPointsService $giftPointsService
    ) {
    }

    /**
     * Get the commission service.
     */
    public function getCommissionService(): OrderCommissionService
    {
        return $this->commissionService;
    }

    /**
     * Calculate delivery fee based on governorate.
     */
    public function calculateDeliveryFee(?int $governorateId): float
    {
        if (!$governorateId) {
            return 0;
        }

        // البصرة: 3000 دينار
        // باقي المحافظات: 5000 دينار
        $basraId = \App\Models\Governorate::where('name', 'البصرة')->value('id');

        if ($basraId && $governorateId == $basraId) {
            return 3000.0;
        }

        return 5000.0;
    }

    /**
     * Calculate gift price based on selected gifts.
     */
    public function calculateGiftPrice(?int $giftId, ?int $giftBoxId): float
    {
        $price = 0.0;

        if ($giftId) {
            $gift = GiftSetting::find($giftId);
            if ($gift && $gift->type === 'gift') {
                $price += (float) ($gift->price ?? 0);
            }
        }

        if ($giftBoxId) {
            $giftBox = GiftSetting::find($giftBoxId);
            if ($giftBox && $giftBox->type === 'gift_box') {
                $price += (float) ($giftBox->box_price ?? 0);
            }
        }

        return $price;
    }

    /**
     * Create a new order.
     */
    public function createOrder(array $customerData, ?Representative $representative = null, ?User $user = null): Order
    {
        return DB::transaction(function () use ($customerData, $representative, $user) {
            $governorateId = $customerData['governorate_id'] ?? null;
            $deliveryFee = $this->calculateDeliveryFee($governorateId);

            $giftId = $customerData['gift_id'] ?? null;
            $giftBoxId = $customerData['gift_box_id'] ?? null;
            $giftPrice = $this->calculateGiftPrice($giftId, $giftBoxId);

            $order = Order::create([
                'customer_name' => $customerData['customer_name'],
                'customer_address' => $customerData['customer_address'],
                'customer_phone' => $customerData['customer_phone'],
                'customer_phone_2' => $customerData['customer_phone_2'] ?? null,
                'customer_social_media' => $customerData['customer_social_media'] ?? null,
                'customer_notes' => $customerData['customer_notes'] ?? null,
                'governorate_id' => $governorateId,
                'district_id' => $customerData['district_id'] ?? null,
                'delivery_fee' => $deliveryFee,
                'gift_id' => $giftId,
                'gift_box_id' => $giftBoxId,
                'gift_price' => $giftPrice,
                'is_withdrawal_order' => $customerData['is_withdrawal_order'] ?? false,
                'is_paid' => $customerData['is_paid'] ?? false,
                'is_replacement' => $customerData['is_replacement'] ?? false,
                'is_return' => $customerData['is_return'] ?? false,
                'status' => OrderStatus::NEW ,
                'representative_id' => $representative?->id,
                'created_by' => $user?->id,
            ]);

            // Send notification to admins
            try {
                app(\App\Services\Notifications\NotificationService::class)->sendNewOrderNotification($order);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error triggering new order notification: ' . $e->getMessage());
            }

            // Record the initial "Order Created" movement
            \App\Models\OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $order->status->value,
                'waseet_status' => 'تم إنشاء الطلب',
                'notes' => 'تم استلام الطلب وبانتظار التجهيز.',
            ]);

            return $order;
        });
    }

    /**
     * Add item to order.
     */
    public function addItemToOrder(Order $order, Product $product, int $quantity, float $customerPrice): OrderItem
    {
        return DB::transaction(function () use ($order, $product, $quantity, $customerPrice) {
            $wholesalePrice = (float) ($product->wholesale_price ?? 0);
            $profitPerItem = max(0, $customerPrice - $wholesalePrice);
            $subtotal = $quantity * $customerPrice;
            $profitSubtotal = $quantity * $profitPerItem;

            if ($product->available_quantity < $quantity) {
                throw new \Exception("الكمية المطلوبة ({$quantity}) غير متوفرة لهذا المنتج ({$product->name}). المتوفر حالياً: {$product->available_quantity}");
            }

            // Reserve stock from available quantity
            $product->decrement('available_quantity', $quantity);

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'wholesale_price' => $wholesalePrice,
                'customer_price' => $customerPrice,
                'profit_per_item' => $profitPerItem,
                'subtotal' => $subtotal,
                'profit_subtotal' => $profitSubtotal,
            ]);

            // Update order totals
            $this->calculateOrderTotals($order);

            return $orderItem;
        });
    }

    /**
     * Update order item.
     */
    public function updateOrderItem(OrderItem $orderItem, int $quantity, float $customerPrice): OrderItem
    {
        return DB::transaction(function () use ($orderItem, $quantity, $customerPrice) {
            $product = $orderItem->product;
            $wholesalePrice = (float) ($product->wholesale_price ?? 0);
            $profitPerItem = max(0, $customerPrice - $wholesalePrice);
            $subtotal = $quantity * $customerPrice;
            $profitSubtotal = $quantity * $profitPerItem;

            $oldQuantity = $orderItem->quantity;
            $delta = $quantity - $oldQuantity;

            if ($product->available_quantity < $delta) {
                throw new \Exception("الكمية المطلوبة ({$quantity}) غير متوفرة لهذا المنتج ({$product->name}). المتوفر حالياً: " . ($product->available_quantity + $oldQuantity));
            }

            // Adjust reserved stock
            if ($delta != 0) {
                $product->decrement('available_quantity', $delta);
            }

            $orderItem->update([
                'quantity' => $quantity,
                'customer_price' => $customerPrice,
                'profit_per_item' => $profitPerItem,
                'subtotal' => $subtotal,
                'profit_subtotal' => $profitSubtotal,
            ]);

            // Update order totals
            $this->calculateOrderTotals($orderItem->order);

            return $orderItem->fresh();
        });
    }

    /**
     * Remove item from order.
     */
    public function removeOrderItem(OrderItem $orderItem): bool
    {
        return DB::transaction(function () use ($orderItem) {
            $order = $orderItem->order;
            
            // Release reserved stock back to available quantity
            $orderItem->product->increment('available_quantity', $orderItem->quantity);
            
            $deleted = $orderItem->delete();

            if ($deleted) {
                // Update order totals
                $this->calculateOrderTotals($order);
            }

            return $deleted;
        });
    }

    /**
     * Calculate order totals.
     */
    public function calculateOrderTotals(Order $order): Order
    {
        $itemsTotal = $order->calculateTotal();
        $deliveryFee = (float) ($order->delivery_fee ?? 0);
        $giftPrice = (float) ($order->gift_price ?? 0);
        $totalAmount = $itemsTotal + $deliveryFee + $giftPrice; // إضافة سعر التوصيل والهدايا إلى السعر الكلي
        $totalProfit = $order->calculateProfit();
        $preparationCommission = $this->commissionService->calculateCommission($order);
        $finalProfit = max(0, $totalProfit - $preparationCommission);

        $order->update([
            'total_amount' => $totalAmount,
            'total_profit' => $totalProfit,
            'preparation_commission' => $preparationCommission,
            'final_profit' => $finalProfit,
        ]);

        return $order->fresh();
    }

    /**
     * Complete order and add profit to representative account.
     */
    public function completeOrder(Order $order): Order
    {
        if (!$order->canBeCompleted()) {
            throw new \Exception('لا يمكن إكمال هذا الطلب.');
        }

        if (!$order->representative_id) {
            throw new \Exception('الطلب لا يحتوي على مندوب.');
        }

        return DB::transaction(function () use ($order) {
            $representative = $order->representative;

            // Calculate totals if not already calculated
            $this->calculateOrderTotals($order);

            // Deduct balance for withdrawal orders or add profit for standard orders
            if ($order->is_withdrawal_order) {
                // Determine deduction amount (books wholesale + delivery fee + gifts offset)
                $amountToDeduct = (float) $order->total_amount;
                
                if ($amountToDeduct > 0) {
                    $this->accountService->deductBalance(
                        $representative,
                        $amountToDeduct,
                        "خصم إجمالي طلب سحب رصيد (شراء مخفض) - طلب #{$order->id}"
                    );
                }
            } else {
                // Add final profit to representative account
                if ($order->final_profit > 0) {
                    $this->accountService->addBalance(
                        $representative,
                        (float) $order->final_profit,
                        TransactionType::COMMISSION->value,
                        "ربح من طلب #{$order->id}",
                        $order->createdBy
                    );
                }
            }

            // Award Gift Points
            $this->giftPointsService->awardPoints($order);

            $oldStatus = $order->status->value;

            // Deduct from physical quantity on completion (already reserved from available_quantity)
            foreach ($order->items as $item) {
                $item->product->decrement('quantity', $item->quantity);
            }

            // Update order status
            $order->update([
                'status' => OrderStatus::COMPLETED,
                'completed_at' => now(),
            ]);

            // Send notification
            try {
                app(\App\Services\Notifications\NotificationService::class)->sendOrderStatusNotification($order, $oldStatus, OrderStatus::COMPLETED->value);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error triggering order completion notification: ' . $e->getMessage());
            }

            return $order->fresh();
        });
    }

    /**
     * Change order status.
     */
    public function changeOrderStatus(Order $order, OrderStatus $status, ?User $user = null): Order
    {
        // If completing order, use completeOrder method
        if ($status === OrderStatus::COMPLETED) {
            return $this->completeOrder($order);
        }

        $oldStatus = $order->status;
        
        // If order was COMPLETED and now changing to something else (cancellation/return)
        // We need to return physical quantity back
        if ($oldStatus === OrderStatus::COMPLETED && in_array($status, [OrderStatus::CANCELLED, OrderStatus::RETURNED])) {
            foreach ($order->items as $item) {
                $item->product->increment('quantity', $item->quantity);
            }
        }

        // If order is being CANCELLED or RETURNED from a non-cancelled state
        // We always release reserved available_quantity
        if (in_array($status, [OrderStatus::CANCELLED, OrderStatus::RETURNED]) &&
            !in_array($oldStatus, [OrderStatus::CANCELLED, OrderStatus::RETURNED])) {
            foreach ($order->items as $item) {
                $item->product->increment('available_quantity', $item->quantity);
            }
        }

        $oldStatusValue = $oldStatus->value;

        $order->update([
            'status' => $status,
            'completed_at' => $status === OrderStatus::COMPLETED ? now() : null,
        ]);

        // Record the movement in timeline
        \App\Models\OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $status->value,
            'waseet_status' => $status->label(),
            'notes' => 'تحديث الحالة يدوياً من قبل الإدارة.',
        ]);

        // Send notification
        try {
            app(\App\Services\Notifications\NotificationService::class)->sendOrderStatusNotification($order, $oldStatus->value, $status->value);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error triggering order status change notification: ' . $e->getMessage());
        }

        return $order->fresh();
    }

    /**
     * Update an order and its details.
     */
    public function updateOrder(Order $order, array $data): Order
    {
        if ($order->status === OrderStatus::COMPLETED) {
            throw new \Exception('لا يمكن تعديل طلب مكتمل.');
        }

        return DB::transaction(function () use ($order, $data) {
            $order->update($data);

            // Sync items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                $this->syncItems($order, $data['items']);
            }

            // Record edit in history
            \App\Models\OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $order->status->value,
                'waseet_status' => 'تعديل بيانات والمنتجات',
                'notes' => 'تم تعديل بيانات الطلب والمنتجات من قبل الإدارة.',
            ]);

            return $order->fresh();
        });
    }

    /**
     * Sync order items and adjust stock.
     */
    protected function syncItems(Order $order, array $itemsData): void
    {
        $existingItems = $order->orderItems()->get()->keyBy('product_id');
        $newProductIds = collect($itemsData)->pluck('product_id')->toArray();

        // 1. Remove items not in the new list
        foreach ($existingItems as $productId => $item) {
            if (!in_array($productId, $newProductIds)) {
                $this->removeOrderItem($item);
            }
        }

        // 2. Add or update items
        foreach ($itemsData as $itemData) {
            $productId = $itemData['product_id'];
            $quantity = (int) $itemData['quantity'];
            $customerPrice = (float) ($itemData['customer_price'] ?? 0);

            if ($existingItems->has($productId)) {
                // Update existing
                $this->updateOrderItem($existingItems[$productId], $quantity, $customerPrice);
            } else {
                // Add new
                $product = Product::findOrFail($productId);
                $this->addItemToOrder($order, $product, $quantity, $customerPrice);
            }
        }

        // Final recalculation of totals
        $this->calculateOrderTotals($order);
    }

    /**
     * Delete an order and handle stock restoration.
     */
    public function deleteOrder(Order $order): bool
    {
        if ($order->status === OrderStatus::COMPLETED) {
            throw new \Exception('لا يمكن حذف طلب مكتمل.');
        }

        return DB::transaction(function () use ($order) {
            // Restore reserved quantities
            foreach ($order->items as $item) {
                $item->product->increment('available_quantity', $item->quantity);
            }

            return $order->delete();
        });
    }
}

