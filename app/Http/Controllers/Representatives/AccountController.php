<?php

namespace App\Http\Controllers\Representatives;

use App\Http\Controllers\Controller;
use App\Http\Requests\Representatives\StoreWithdrawalRequest;
use App\Services\Representatives\RepresentativeAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        protected RepresentativeAccountService $accountService
    ) {
    }

    /**
     * Display the account page.
     */
    public function index(): View
    {
        $representative = auth()->guard('representative')->user();
        $lastTransaction = $representative->transactions()->latest()->first();

        return view('representatives.account.index', [
            'title' => 'الحسابات',
            'representative' => $representative,
            'lastTransaction' => $lastTransaction,
            'minWithdrawalAmount' => $this->accountService->getMinWithdrawalAmount($representative),
        ]);
    }

    /**
     * Create a withdrawal request.
     */
    public function withdraw(StoreWithdrawalRequest $request): RedirectResponse
    {
        try {
            $representative = auth()->guard('representative')->user();
            $this->accountService->createWithdrawalRequest($representative, $request->validated());

            return redirect()->route('representative.account.index')
                ->with('success', 'تم إرسال طلب السحب بنجاح. سيتم مراجعته قريباً.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get transactions for the authenticated representative.
     */
    public function transactions(Request $request): JsonResponse
    {
        $representative = auth()->guard('representative')->user();

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

        // GridJS sends 'limit' as query parameter, but Laravel uses 'per_page'
        $perPage = $request->input('limit', $request->input('per_page', 15));
        $transactions = $query->paginate($perPage);

        // Format data for GridJS
        $formattedData = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'type' => $transaction->type->value,
                'amount' => (float) $transaction->amount,
                'status' => $transaction->status->value,
                'description' => $transaction->description ?? '-',
                'created_at' => $transaction->created_at->toISOString(),
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'total' => $transactions->total(),
            'per_page' => $transactions->perPage(),
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
