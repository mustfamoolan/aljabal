<?php

namespace App\Services\Orders;

use App\Enums\OrderStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
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
        protected RepresentativeAccountService $accountService
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
     * Create a new order.
     */
    public function createOrder(array $customerData, ?Representative $representative = null, ?User $user = null): Order
    {
        return DB::transaction(function () use ($customerData, $representative, $user) {
            $order = Order::create([
                'customer_name' => $customerData['customer_name'],
                'customer_address' => $customerData['customer_address'],
                'customer_phone' => $customerData['customer_phone'],
                'customer_phone_2' => $customerData['customer_phone_2'] ?? null,
                'customer_social_media' => $customerData['customer_social_media'] ?? null,
                'customer_notes' => $customerData['customer_notes'] ?? null,
                'status' => OrderStatus::NEW,
                'representative_id' => $representative?->id,
                'created_by' => $user?->id,
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
        $totalAmount = $order->calculateTotal();
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

            // Deduct preparation commission if exists
            if ($order->preparation_commission > 0) {
                $balanceBefore = (float) $representative->balance;
                $representative->decrement('balance', $order->preparation_commission);
                $balanceAfter = (float) $representative->fresh()->balance;

                // Create transaction for commission deduction
                RepresentativeTransaction::create([
                    'representative_id' => $representative->id,
                    'type' => TransactionType::DEDUCTION,
                    'amount' => (float) $order->preparation_commission,
                    'status' => TransactionStatus::COMPLETED,
                    'description' => "عمولة تجهيز طلب #{$order->id}",
                    'created_by' => $order->createdBy?->id,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                ]);
            }

            // Update order status
            $order->update([
                'status' => OrderStatus::COMPLETED,
                'completed_at' => now(),
            ]);

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

        $order->update([
            'status' => $status,
            'completed_at' => $status === OrderStatus::COMPLETED ? now() : null,
        ]);

        return $order->fresh();
    }
}

