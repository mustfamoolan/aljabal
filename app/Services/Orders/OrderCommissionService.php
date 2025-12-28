<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\OrderPreparationCommissionException;
use App\Models\OrderPreparationCommissionSetting;

class OrderCommissionService
{
    /**
     * Calculate preparation commission for an order.
     */
    public function calculateCommission(Order $order): float
    {
        return OrderPreparationCommissionSetting::getCommissionForOrder($order);
    }

    /**
     * Get all active commission settings.
     */
    public function getCommissionSettings()
    {
        return OrderPreparationCommissionSetting::active()->get();
    }

    /**
     * Get commission setting by ID.
     */
    public function getCommissionSetting(int $id): ?OrderPreparationCommissionSetting
    {
        return OrderPreparationCommissionSetting::find($id);
    }

    /**
     * Create a new commission setting.
     */
    public function createCommissionSetting(array $data): OrderPreparationCommissionSetting
    {
        return OrderPreparationCommissionSetting::create($data);
    }

    /**
     * Update a commission setting.
     */
    public function updateCommissionSetting(OrderPreparationCommissionSetting $setting, array $data): bool
    {
        return $setting->update($data);
    }

    /**
     * Delete a commission setting.
     */
    public function deleteCommissionSetting(OrderPreparationCommissionSetting $setting): bool
    {
        return $setting->delete();
    }

    /**
     * Get all commission exceptions.
     */
    public function getCommissionExceptions()
    {
        return OrderPreparationCommissionException::with('representative', 'user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get commission exception by ID.
     */
    public function getCommissionException(int $id): ?OrderPreparationCommissionException
    {
        return OrderPreparationCommissionException::find($id);
    }

    /**
     * Create a new commission exception.
     */
    public function createCommissionException(array $data): OrderPreparationCommissionException
    {
        return OrderPreparationCommissionException::create($data);
    }

    /**
     * Update a commission exception.
     */
    public function updateCommissionException(OrderPreparationCommissionException $exception, array $data): bool
    {
        return $exception->update($data);
    }

    /**
     * Delete a commission exception.
     */
    public function deleteCommissionException(OrderPreparationCommissionException $exception): bool
    {
        return $exception->delete();
    }
}

