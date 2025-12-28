<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Orders\OrderCommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderCommissionSettingsController extends Controller
{
    public function __construct(
        protected OrderCommissionService $commissionService
    ) {
    }

    /**
     * Display a listing of commission settings.
     */
    public function index(): View
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $settings = $this->commissionService->getCommissionSettings();
        $exceptions = $this->commissionService->getCommissionExceptions();
        $representatives = \App\Models\Representative::all();
        $users = \App\Models\User::all();

        return view('admin.orders.commission-settings', compact('settings', 'exceptions', 'representatives', 'users'));
    }

    /**
     * Store a newly created commission setting.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'commission_value' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $this->commissionService->createCommissionSetting($validated);

            return back()->with('success', 'تم إضافة إعداد العمولة بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Update the specified commission setting.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $setting = $this->commissionService->getCommissionSetting($id);

        if (!$setting) {
            return back()->withErrors(['error' => 'الإعداد غير موجود.']);
        }

        $validated = $request->validate([
            'commission_value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $this->commissionService->updateCommissionSetting($setting, $validated);

            return back()->with('success', 'تم تحديث إعداد العمولة بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified commission setting.
     */
    public function destroy(int $id): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $setting = $this->commissionService->getCommissionSetting($id);

        if (!$setting) {
            return back()->withErrors(['error' => 'الإعداد غير موجود.']);
        }

        try {
            $this->commissionService->deleteCommissionSetting($setting);

            return back()->with('success', 'تم حذف إعداد العمولة بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created commission exception.
     */
    public function storeException(Request $request): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'representative_id' => ['nullable', 'exists:representatives,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'commission_value' => ['required', 'numeric', 'min:0'],
        ]);

        // Ensure only one is set
        if (empty($validated['representative_id']) && empty($validated['user_id'])) {
            return back()->withErrors(['error' => 'يجب اختيار مندوب أو موظف.']);
        }

        if (!empty($validated['representative_id']) && !empty($validated['user_id'])) {
            return back()->withErrors(['error' => 'يجب اختيار مندوب أو موظف فقط، وليس كلاهما.']);
        }

        try {
            $this->commissionService->createCommissionException($validated);

            return back()->with('success', 'تم إضافة الاستثناء بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Update the specified commission exception.
     */
    public function updateException(Request $request, int $id): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $exception = $this->commissionService->getCommissionException($id);

        if (!$exception) {
            return back()->withErrors(['error' => 'الاستثناء غير موجود.']);
        }

        $validated = $request->validate([
            'commission_value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $this->commissionService->updateCommissionException($exception, $validated);

            return back()->with('success', 'تم تحديث الاستثناء بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified commission exception.
     */
    public function destroyException(int $id): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $exception = $this->commissionService->getCommissionException($id);

        if (!$exception) {
            return back()->withErrors(['error' => 'الاستثناء غير موجود.']);
        }

        try {
            $this->commissionService->deleteCommissionException($exception);

            return back()->with('success', 'تم حذف الاستثناء بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
