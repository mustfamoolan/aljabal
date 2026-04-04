<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftPointsSetting;
use App\Models\GiftSetting;
use App\Models\OrderPreparationCommissionSetting;
use App\Models\WithdrawalSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeneralSettingsApiController extends Controller
{
    protected \App\Services\GatewayIntegrationService $gatewayService;

    public function __construct(\App\Services\GatewayIntegrationService $gatewayService)
    {
        $this->gatewayService = $gatewayService;
    }

    /**
     * Get all general settings for the mobile app dashboard.
     */
    public function index(): JsonResponse
    {
        $withdrawalSetting = WithdrawalSetting::general()->first();
        $rewardPointsSetting = GiftPointsSetting::first();
        $orderCommissionSetting = OrderPreparationCommissionSetting::active()->first() ?? OrderPreparationCommissionSetting::first();
        $gifts = GiftSetting::gifts()->active()->get();
        $giftBoxes = GiftSetting::giftBoxes()->active()->get();
        $gatewaySetting = \App\Models\GatewaySetting::first();

        return response()->json([
            'withdrawal' => $withdrawalSetting,
            'reward_points' => $rewardPointsSetting,
            'order_commission' => $orderCommissionSetting,
            'gifts' => $gifts,
            'gift_boxes' => $giftBoxes,
            'gateway' => $gatewaySetting ? [
                'waseet_username' => $gatewaySetting->waseet_username,
                'is_connected' => $gatewaySetting->is_connected,
                'last_sync_at' => $gatewaySetting->last_sync_at ? $gatewaySetting->last_sync_at->toDateTimeString() : null,
                'project_name' => $gatewaySetting->project_name,
            ] : null,
        ]);
    }

    /**
     * Update all general settings.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'min_withdrawal_amount' => ['nullable', 'numeric', 'min:0'],
            'points_per_order' => ['nullable', 'integer', 'min:0'],
            'commission_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Update Withdrawal
        if ($request->has('min_withdrawal_amount')) {
            $withdrawalSetting = WithdrawalSetting::general()->first();
            if ($withdrawalSetting) {
                $withdrawalSetting->update(['min_withdrawal_amount' => $validated['min_withdrawal_amount']]);
            } else {
                WithdrawalSetting::create([
                    'min_withdrawal_amount' => $validated['min_withdrawal_amount'],
                    'is_exception' => false,
                    'created_by' => auth()->id() ?? 1,
                ]);
            }
        }

        // Update Reward Points
        if ($request->has('points_per_order')) {
            $rewardPointsSetting = GiftPointsSetting::first();
            if ($rewardPointsSetting) {
                $rewardPointsSetting->update(['points_per_order' => $validated['points_per_order']]);
            } else {
                GiftPointsSetting::create([
                    'points_per_order' => $validated['points_per_order'],
                    'is_active' => true,
                ]);
            }
        }

        // Update Order Commission
        if ($request->has('commission_value')) {
            $orderCommissionSetting = OrderPreparationCommissionSetting::active()->first() ?? OrderPreparationCommissionSetting::first();
            if ($orderCommissionSetting) {
                $orderCommissionSetting->update(['commission_value' => $validated['commission_value']]);
            } else {
                OrderPreparationCommissionSetting::create([
                    'commission_value' => $validated['commission_value'],
                    'is_active' => true,
                ]);
            }
        }

        return response()->json([
            'message' => 'تم حفظ الإعدادات بنجاح.',
        ]);
    }

    /**
     * Store new gift setting
     */
    public function storeGift(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:gift,gift_box'],
            'price' => ['nullable', 'numeric', 'min:0', 'required_if:type,gift'],
            'min_books' => ['nullable', 'integer', 'min:1', 'required_if:type,gift_box'],
            'max_books' => ['nullable', 'integer', 'min:1', 'required_if:type,gift_box'],
            'box_price' => ['nullable', 'numeric', 'min:0', 'required_if:type,gift_box'],
        ]);

        $giftSetting = GiftSetting::create($validated);

        return response()->json([
            'message' => 'تم إنشاء إعداد الهدية بنجاح.',
            'gift' => $giftSetting
        ]);
    }

    /**
     * Update gift setting
     */
    public function updateGift(Request $request, int $id): JsonResponse
    {
        $giftSetting = GiftSetting::find($id);

        if (!$giftSetting) {
            return response()->json(['error' => 'الهدية غير موجودة'], 404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:gift,gift_box'],
            'price' => ['nullable', 'numeric', 'min:0', 'required_if:type,gift'],
            'min_books' => ['nullable', 'integer', 'min:1', 'required_if:type,gift_box'],
            'max_books' => ['nullable', 'integer', 'min:1', 'required_if:type,gift_box'],
            'box_price' => ['nullable', 'numeric', 'min:0', 'required_if:type,gift_box'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $giftSetting->update($validated);

        return response()->json([
            'message' => 'تم تحديث إعداد الهدية بنجاح.',
            'gift' => $giftSetting
        ]);
    }

    /**
     * Delete gift setting
     */
    public function destroyGift(int $id): JsonResponse
    {
        $giftSetting = GiftSetting::find($id);

        if (!$giftSetting) {
            return response()->json(['error' => 'الهدية غير موجودة'], 404);
        }

        $giftSetting->delete();

        return response()->json([
            'message' => 'تم حذف إعداد الهدية بنجاح.'
        ]);
    }

    /**
     * Connect to Waseet Gateway from API.
     */
    public function connectGateway(Request $request): JsonResponse
    {
        $request->validate([
            'waseet_username' => 'required|string',
            'waseet_password' => 'required|string',
        ]);

        $result = $this->gatewayService->connect(
            $request->waseet_username,
            $request->waseet_password
        );

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 422);
    }

    /**
     * Trigger location sync from API.
     */
    public function syncGatewayLocations(): JsonResponse
    {
        $result = $this->gatewayService->syncLocations();

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 422);
    }
}
