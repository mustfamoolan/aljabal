<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Representatives\UpdateWithdrawalSettingsRequest;
use App\Models\Representative;
use App\Models\WithdrawalSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WithdrawalSettingsController extends Controller
{
    /**
     * Display withdrawal settings.
     */
    public function index(): View
    {
        $this->authorize('representatives.view');

        $generalSetting = WithdrawalSetting::general()->first();
        $exceptions = WithdrawalSetting::whereNotNull('representative_id')
            ->with('representative')
            ->latest()
            ->get();

        return view('admin.settings.withdrawal', compact('generalSetting', 'exceptions'));
    }

    /**
     * Update general withdrawal settings.
     */
    public function updateGeneral(UpdateWithdrawalSettingsRequest $request): RedirectResponse
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

        return redirect()->route('admin.settings.withdrawal.index')
            ->with('success', 'تم تحديث الإعدادات العامة بنجاح.');
    }

    /**
     * Create an exception for a representative.
     */
    public function createException(Request $request): RedirectResponse
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

        return redirect()->route('admin.settings.withdrawal.index')
            ->with('success', 'تم إنشاء الاستثناء بنجاح.');
    }

    /**
     * Update an exception.
     */
    public function updateException(WithdrawalSetting $withdrawalSetting, UpdateWithdrawalSettingsRequest $request): RedirectResponse
    {
        $this->authorize('representatives.update');

        $withdrawalSetting->update([
            'min_withdrawal_amount' => $request->min_withdrawal_amount,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.settings.withdrawal.index')
            ->with('success', 'تم تحديث الاستثناء بنجاح.');
    }

    /**
     * Delete an exception.
     */
    public function deleteException(WithdrawalSetting $withdrawalSetting): RedirectResponse
    {
        $this->authorize('representatives.update');

        $withdrawalSetting->delete();

        return redirect()->route('admin.settings.withdrawal.index')
            ->with('success', 'تم حذف الاستثناء بنجاح.');
    }
}
