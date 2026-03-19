<?php

namespace App\Http\Controllers\Api\Representatives;

use App\Http\Controllers\Controller;
use App\Models\RepresentativeTransaction;
use App\Models\WithdrawalRequest;
use App\Enums\WithdrawalStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RepresentativeFinancialController extends Controller
{
    /**
     * Get financial summary for the authenticated representative.
     */
    public function summary(Request $request): JsonResponse
    {
        $rep = $request->user();

        $totalEarnings = $rep->transactions()
            ->where('type', 'earning')
            ->where('status', 'completed')
            ->sum('amount');

        $monthlyEarnings = $rep->transactions()
            ->where('type', 'earning')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $pendingWithdrawals = WithdrawalRequest::where('representative_id', $rep->id)
            ->where('status', WithdrawalStatus::PENDING)
            ->sum('amount');

        $minWithdrawalAmount = \App\Models\WithdrawalSetting::getMinWithdrawalForRepresentative($rep);

        return response()->json([
            'balance'             => (float) $rep->balance,
            'total_earnings'      => (float) $totalEarnings,
            'monthly_earnings'    => (float) $monthlyEarnings,
            'pending_withdrawals' => (float) $pendingWithdrawals,
            'min_withdrawal_amount' => (float) $minWithdrawalAmount,
        ]);
    }

    /**
     * Get paginated list of transactions for the authenticated representative.
     */
    public function transactions(Request $request): JsonResponse
    {
        $rep = $request->user();

        $transactions = RepresentativeTransaction::where('representative_id', $rep->id)
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        $items = $transactions->map(function ($tx) {
            return [
                'id'             => $tx->id,
                'type'           => $tx->type instanceof \BackedEnum ? $tx->type->value : $tx->type,
                'type_label'     => $tx->type_label ?? '',
                'amount'         => (float) $tx->amount,
                'status'         => $tx->status instanceof \BackedEnum ? $tx->status->value : $tx->status,
                'status_label'   => $tx->status_label ?? '',
                'description'    => $tx->description,
                'balance_before' => (float) $tx->balance_before,
                'balance_after'  => (float) $tx->balance_after,
                'created_at'     => $tx->created_at?->toISOString(),
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'per_page'     => $transactions->perPage(),
                'total'        => $transactions->total(),
            ],
        ]);
    }

    /**
     * Get paginated list of withdrawal requests for the authenticated representative.
     */
    public function withdrawalRequests(Request $request): JsonResponse
    {
        $rep = $request->user();

        $withdrawals = WithdrawalRequest::where('representative_id', $rep->id)
            ->orderByDesc('requested_at')
            ->paginate($request->get('per_page', 20));

        $items = $withdrawals->map(function ($wr) {
            return [
                'id'           => $wr->id,
                'amount'       => (float) $wr->amount,
                'method'       => $wr->method,
                'method_label' => $this->methodLabel($wr->method),
                'phone_number' => $wr->phone_number,
                'status'       => $wr->status instanceof \BackedEnum ? $wr->status->value : $wr->status,
                'status_label' => $wr->status_label ?? '',
                'notes'        => $wr->notes,
                'requested_at' => $wr->requested_at?->toISOString(),
                'approved_at'  => $wr->approved_at?->toISOString(),
                'rejected_reason' => $wr->rejected_reason,
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $withdrawals->currentPage(),
                'last_page'    => $withdrawals->lastPage(),
                'per_page'     => $withdrawals->perPage(),
                'total'        => $withdrawals->total(),
            ],
        ]);
    }

    /**
     * Submit a new withdrawal request.
     */
    public function storeWithdrawal(Request $request): JsonResponse
    {
        $rep = $request->user();
        $minAmount = \App\Models\WithdrawalSetting::getMinWithdrawalForRepresentative($rep);

        $validator = Validator::make($request->all(), [
            'amount'         => ['required', 'numeric', 'min:' . $minAmount],
            'method'         => ['required', 'in:zaincash,superqi,balance,buy_books'],
            'phone_number'   => ['required_if:method,zaincash,balance', 'nullable', 'string', 'max:20'],
            'account_number' => ['nullable', 'string', 'max:50', function ($attribute, $value, $fail) use ($request) {
                if ($request->method === 'superqi' && empty($request->phone_number) && empty($value)) {
                    $fail('يجب إدخال رقم الهاتف أو رقم الحساب لطريقة سوبر كي.');
                }
            }],
            'notes'          => ['nullable', 'string', 'max:500'],
        ], [
            'amount.required'       => 'المبلغ مطلوب.',
            'amount.min'            => "الحد الأدنى للسحب هو " . number_format($minAmount) . " د.ع.",
            'method.required'       => 'طريقة السحب مطلوبة.',
            'method.in'             => 'طريقة السحب غير صالحة.',
            'phone_number.required_if' => 'رقم الهاتف مطلوب لهذه الطريقة.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'بيانات غير صحيحة.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Check sufficient balance
        if ((float) $rep->balance < (float) $request->amount) {
            return response()->json([
                'message' => 'رصيدك الحالي غير كافٍ لإتمام عملية السحب.',
            ], 422);
        }

        // Check no pending withdrawal already exists
        $hasPending = WithdrawalRequest::where('representative_id', $rep->id)
            ->where('status', WithdrawalStatus::PENDING)
            ->exists();

        if ($hasPending) {
            return response()->json([
                'message' => 'لديك طلب سحب معلق بالفعل. يرجى انتظار معالجته قبل تقديم طلب جديد.',
            ], 422);
        }

        $withdrawal = WithdrawalRequest::create([
            'representative_id' => $rep->id,
            'amount'            => $request->amount,
            'method'            => $request->method,
            'phone_number'      => $request->phone_number,
            'account_number'    => $request->account_number,
            'status'            => WithdrawalStatus::PENDING,
            'notes'             => $request->notes,
            'requested_at'      => now(),
        ]);

        return response()->json([
            'message' => 'تم تقديم طلب السحب بنجاح. سيتم مراجعته قريباً.',
            'data'    => [
                'id'           => $withdrawal->id,
                'amount'       => (float) $withdrawal->amount,
                'method'       => $withdrawal->method,
                'method_label' => $this->methodLabel($withdrawal->method),
                'status'       => 'pending',
                'requested_at' => $withdrawal->requested_at->toISOString(),
            ],
        ]);
    }

    private function methodLabel(?string $method): string
    {
        return match($method) {
            'zaincash'  => 'زين كاش',
            'superqi'   => 'سوبر كي',
            'balance'   => 'رصيد',
            'buy_books' => 'شراء كتب',
            default     => $method ?? '',
        };
    }
}
