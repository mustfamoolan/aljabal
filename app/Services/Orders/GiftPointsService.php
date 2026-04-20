<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\User;
use App\Models\Representative;
use App\Models\GiftPointsSetting;
use App\Models\GiftPointsException;

class GiftPointsService
{
    /**
     * Calculate points for an order.
     */
    public function calculatePointsForOrder(Order $order): int
    {
        // Check for exceptions first (representative or user)
        if ($order->representative_id) {
            $exception = GiftPointsException::getForRepresentative($order->representative_id);
            if ($exception) {
                return $exception->points_per_order;
            }
        }

        if ($order->created_by) {
            $exception = GiftPointsException::getForUser($order->created_by);
            if ($exception) {
                return $exception->points_per_order;
            }
        }

        // If no exception, use general settings
        // Get the first active setting
        $settings = GiftPointsSetting::active()->first();

        if (!$settings) {
            return 0; // Return 0 if settings not found or not active
        }

        return $settings->points_per_order;
    }

    /**
     * Award points to the creator of the order
     */
    public function awardPoints(Order $order): bool
    {
        $points = $this->calculatePointsForOrder($order);

        if ($points <= 0) {
            return false;
        }

        // Save earned points on the order record
        $order->earned_gift_points = $points;
        $order->save();

        // Add points to the respective creator (Representative or User)
        if ($order->representative_id) {
            $rep = Representative::find($order->representative_id);
            if ($rep) {
                $rep->total_gift_points += $points;
                return $rep->save();
            }
        } elseif ($order->created_by) {
            $user = User::find($order->created_by);
            if ($user && $user->employeeType && $user->employeeType->name === 'أدمن') {
                // Determine logic for normal admins? Usually, it's employees. Let's just add to user.
                $user->total_gift_points += $points;
                return $user->save();
            } else if ($user) {
                $user->total_gift_points += $points;
                return $user->save();
            }
        }

        return false;
    }

    /**
     * Reverse awarded points from the creator of the order
     */
    public function reversePoints(Order $order): bool
    {
        $points = $order->earned_gift_points ?? 0;

        if ($points <= 0) {
            return false;
        }

        // Deduct points from the respective creator (Representative or User)
        if ($order->representative_id) {
            $rep = Representative::find($order->representative_id);
            if ($rep) {
                $rep->total_gift_points = max(0, $rep->total_gift_points - $points);
                return $rep->save();
            }
        } elseif ($order->created_by) {
            $user = User::find($order->created_by);
            if ($user) {
                $user->total_gift_points = max(0, $user->total_gift_points - $points);
                return $user->save();
            }
        }

        return false;
    }

    /**
     * Get all active commission settings.
     */
    public function getSettings()
    {
        return GiftPointsSetting::active()->get();
    }

    /**
     * Get commission setting by ID.
     */
    public function getSetting(int $id): ?GiftPointsSetting
    {
        return GiftPointsSetting::find($id);
    }

    /**
     * Create a new commission setting.
     */
    public function createSetting(array $data): GiftPointsSetting
    {
        return GiftPointsSetting::create($data);
    }

    /**
     * Update a commission setting.
     */
    public function updateSetting(GiftPointsSetting $setting, array $data): bool
    {
        return $setting->update($data);
    }

    /**
     * Delete a commission setting.
     */
    public function deleteSetting(GiftPointsSetting $setting): bool
    {
        return $setting->delete();
    }

    /**
     * Get all commission exceptions.
     */
    public function getExceptions()
    {
        return GiftPointsException::with('representative', 'user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get commission exception by ID.
     */
    public function getException(int $id): ?GiftPointsException
    {
        return GiftPointsException::find($id);
    }

    /**
     * Create a new commission exception.
     */
    public function createException(array $data): GiftPointsException
    {
        return GiftPointsException::create($data);
    }

    /**
     * Update a commission exception.
     */
    public function updateException(GiftPointsException $exception, array $data): bool
    {
        return $exception->update($data);
    }

    /**
     * Delete a commission exception.
     */
    public function deleteException(GiftPointsException $exception): bool
    {
        return $exception->delete();
    }
}
