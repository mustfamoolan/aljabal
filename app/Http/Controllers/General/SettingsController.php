<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\Representatives\UpdateWithdrawalSettingsRequest;
use App\Models\GiftSetting;
use App\Models\Representative;
use App\Models\WithdrawalSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display general settings page.
     */
    public function index(): View
    {
        $this->authorize('representatives.view');

        // Withdrawal settings
        $generalSetting = WithdrawalSetting::general()->first();
        $exceptions = WithdrawalSetting::whereNotNull('representative_id')
            ->with('representative')
            ->latest()
            ->get();

        // Gift settings
        $gifts = GiftSetting::gifts()->active()->orderBy('name')->get();
        $giftBoxes = GiftSetting::giftBoxes()->active()->orderBy('min_books')->get();

        return view('general.settings', compact('generalSetting', 'exceptions', 'gifts', 'giftBoxes'));
    }

    /**
     * Update general withdrawal settings.
     */
    public function updateWithdrawalGeneral(UpdateWithdrawalSettingsRequest $request): RedirectResponse
    {
        $this->authorize('representatives.update');

        $generalSetting = WithdrawalSetting::general()->first();

        if ($generalSetting) {
            $generalSetting->update([
                'min_withdrawal_amount' => $request->min_withdrawal_amount,
                'updated_by' => auth()->id(),
            ]);
        } else {
            WithdrawalSetting::create([
                'representative_id' => null,
                'min_withdrawal_amount' => $request->min_withdrawal_amount,
                'is_exception' => false,
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('general.settings.index')
            ->with('success', 'تم تحديث إعدادات السحب العامة بنجاح.');
    }

    /**
     * Create an exception for a representative.
     */
    public function createWithdrawalException(Request $request): RedirectResponse
    {
        $this->authorize('representatives.update');

        $request->validate([
            'representative_id' => ['required', 'exists:representatives,id'],
            'min_withdrawal_amount' => ['required', 'numeric', 'min:0'],
        ]);

        // Check if exception already exists
        $existing = WithdrawalSetting::where('representative_id', $request->representative_id)->first();
        if ($existing) {
            return redirect()->back()
                ->withErrors(['error' => 'يوجد استثناء موجود بالفعل لهذا المندوب. يمكنك تعديله.']);
        }

        WithdrawalSetting::create([
            'representative_id' => $request->representative_id,
            'min_withdrawal_amount' => $request->min_withdrawal_amount,
            'is_exception' => true,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('general.settings.index')
            ->with('success', 'تم إنشاء الاستثناء بنجاح.');
    }

    /**
     * Update an exception.
     */
    public function updateWithdrawalException(WithdrawalSetting $withdrawalSetting, UpdateWithdrawalSettingsRequest $request): RedirectResponse
    {
        $this->authorize('representatives.update');

        $withdrawalSetting->update([
            'min_withdrawal_amount' => $request->min_withdrawal_amount,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('general.settings.index')
            ->with('success', 'تم تحديث الاستثناء بنجاح.');
    }

    /**
     * Delete an exception.
     */
    public function deleteWithdrawalException(WithdrawalSetting $withdrawalSetting): RedirectResponse
    {
        $this->authorize('representatives.update');

        $withdrawalSetting->delete();

        return redirect()->route('general.settings.index')
            ->with('success', 'تم حذف الاستثناء بنجاح.');
    }

    /**
     * Store a new gift setting.
     */
    public function storeGiftSetting(Request $request): RedirectResponse
    {
        $this->authorize('representatives.update');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:gift,gift_box'],
            'price' => ['nullable', 'numeric', 'min:0', 'required_if:type,gift'],
            'min_books' => ['nullable', 'integer', 'min:1', 'required_if:type,gift_box'],
            'max_books' => ['nullable', 'integer', 'min:1', 'required_if:type,gift_box'],
            'box_price' => ['nullable', 'numeric', 'min:0', 'required_if:type,gift_box'],
        ]);

        GiftSetting::create($validated);

        return redirect()->route('general.settings.index')
            ->with('success', 'تم إنشاء إعداد الهدية بنجاح.');
    }

    /**
     * Update a gift setting.
     */
    public function updateGiftSetting(GiftSetting $giftSetting, Request $request): RedirectResponse
    {
        $this->authorize('representatives.update');

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

        return redirect()->route('general.settings.index')
            ->with('success', 'تم تحديث إعداد الهدية بنجاح.');
    }

    /**
     * Delete a gift setting.
     */
    public function deleteGiftSetting(GiftSetting $giftSetting): RedirectResponse
    {
        $this->authorize('representatives.update');

        $giftSetting->delete();

        return redirect()->route('general.settings.index')
            ->with('success', 'تم حذف إعداد الهدية بنجاح.');
    }
}
