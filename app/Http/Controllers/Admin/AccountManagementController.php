<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Representatives\AddBalanceRequest;
use App\Http\Requests\Representatives\DirectWithdrawalRequest;
use App\Models\Representative;
use App\Services\Representatives\RepresentativeAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountManagementController extends Controller
{
    public function __construct(
        protected RepresentativeAccountService $accountService
    ) {
    }

    /**
     * Display a listing of representatives with their accounts.
     */
    public function index(Request $request): View
    {
        // Allow access for admins
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = Representative::with(['transactions' => function ($q) {
            $q->latest()->limit(1);
        }]);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $representatives = $query->latest()->paginate($request->per_page ?? 15);

        // Calculate totals
        $totalBalance = Representative::sum('balance');
        $totalPending = Representative::with('withdrawalRequests')
            ->get()
            ->sum(function ($rep) {
                return $rep->pending_withdrawals_amount;
            });

        return view('admin.accounts.index', compact('representatives', 'totalBalance', 'totalPending'));
    }

    /**
     * Display the specified representative's account.
     */
    public function show(Representative $representative): View
    {
        // Allow access for admins
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $representative->load(['transactions' => function ($q) {
            $q->with(['creator', 'approver'])->latest()->limit(10);
        }]);

        $statistics = $this->accountService->getAccountStatistics($representative);

        return view('admin.accounts.show', compact('representative', 'statistics'));
    }

    /**
     * Add balance to a representative.
     */
    public function addBalance(Representative $representative, AddBalanceRequest $request): RedirectResponse
    {
        // Allow access for admins
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        try {
            $this->accountService->addBalance(
                $representative,
                (float) $request->amount,
                $request->type,
                $request->description ?? null,
                auth()->user()
            );

            return redirect()->route('admin.accounts.show', $representative)
                ->with('success', 'تم إضافة الرصيد بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Direct withdrawal from a representative.
     */
    public function directWithdraw(Representative $representative, DirectWithdrawalRequest $request): RedirectResponse
    {
        // Allow access for admins
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        try {
            $this->accountService->directWithdraw(
                $representative,
                $request->validated(),
                auth()->user()
            );

            return redirect()->route('admin.accounts.show', $representative)
                ->with('success', 'تم السحب بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get transactions for a representative.
     */
    public function transactions(Representative $representative, Request $request): JsonResponse
    {
        // Allow access for admins
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = $representative->transactions()->with(['creator', 'approver'])->latest();

        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate($request->per_page ?? 15);

        return response()->json($transactions);
    }
}
