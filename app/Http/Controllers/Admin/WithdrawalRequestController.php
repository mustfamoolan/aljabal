<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WithdrawalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Representatives\ApproveWithdrawalRequest;
use App\Http\Requests\Representatives\RejectWithdrawalRequest;
use App\Models\WithdrawalRequest;
use App\Services\Representatives\RepresentativeAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalRequestController extends Controller
{
    public function __construct(
        protected RepresentativeAccountService $accountService
    ) {
    }

    /**
     * Display a listing of withdrawal requests.
     */
    public function index(Request $request): View
    {
        $this->authorize('representatives.view');

        $query = WithdrawalRequest::with(['representative', 'approver'])->latest('requested_at');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', WithdrawalStatus::from($request->status));
        }

        if ($request->has('representative_id')) {
            $query->where('representative_id', $request->representative_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('requested_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('requested_at', '<=', $request->date_to);
        }

        $withdrawals = $query->paginate($request->per_page ?? 15);

        // Statistics
        $pendingCount = WithdrawalRequest::where('status', WithdrawalStatus::PENDING)->count();
        $approvedCount = WithdrawalRequest::where('status', WithdrawalStatus::APPROVED)->count();
        $rejectedCount = WithdrawalRequest::where('status', WithdrawalStatus::REJECTED)->count();

        return view('admin.withdrawals.index', compact('withdrawals', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Display the specified withdrawal request.
     */
    public function show(WithdrawalRequest $withdrawalRequest): View
    {
        $this->authorize('representatives.view');

        $withdrawalRequest->load(['representative', 'approver', 'transactions']);

        return view('admin.withdrawals.show', compact('withdrawalRequest'));
    }

    /**
     * Approve a withdrawal request.
     */
    public function approve(WithdrawalRequest $withdrawalRequest, ApproveWithdrawalRequest $request): RedirectResponse
    {
        // Check if user has permission
        if (!auth()->user()->can('representatives.update')) {
            return redirect()->back()
                ->withErrors(['error' => 'ليس لديك صلاحية للموافقة على طلبات السحب. يرجى التواصل مع المدير.']);
        }

        try {
            $this->accountService->approveWithdrawalRequest(
                $withdrawalRequest,
                auth()->user(),
                $request->validated()['notes'] ?? null
            );

            return redirect()->route('admin.withdrawals.show', $withdrawalRequest)
                ->with('success', 'تم الموافقة على طلب السحب بنجاح.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()
                ->withErrors(['error' => 'ليس لديك صلاحية للموافقة على طلبات السحب. يرجى التواصل مع المدير.']);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Reject a withdrawal request.
     */
    public function reject(WithdrawalRequest $withdrawalRequest, RejectWithdrawalRequest $request): RedirectResponse
    {
        // Check if user has permission
        if (!auth()->user()->can('representatives.update')) {
            return redirect()->back()
                ->withErrors(['error' => 'ليس لديك صلاحية لرفض طلبات السحب. يرجى التواصل مع المدير.']);
        }

        try {
            $this->accountService->rejectWithdrawalRequest(
                $withdrawalRequest,
                auth()->user(),
                $request->validated()['reason']
            );

            return redirect()->route('admin.withdrawals.show', $withdrawalRequest)
                ->with('success', 'تم رفض طلب السحب.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()
                ->withErrors(['error' => 'ليس لديك صلاحية لرفض طلبات السحب. يرجى التواصل مع المدير.']);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
