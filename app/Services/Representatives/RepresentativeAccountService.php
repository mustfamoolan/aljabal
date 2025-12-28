<?php

namespace App\Services\Representatives;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\WithdrawalStatus;
use App\Models\Notification;
use App\Models\Representative;
use App\Models\RepresentativeTransaction;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;

class RepresentativeAccountService
{
    /**
     * Add balance to representative with transaction record.
     */
    public function addBalance(
        Representative $representative,
        float $amount,
        string $type,
        ?string $description = null,
        ?User $user = null
    ): RepresentativeTransaction {
        return DB::transaction(function () use ($representative, $amount, $type, $description, $user) {
            $balanceBefore = (float) $representative->balance;
            $representative->increment('balance', $amount);
            $balanceAfter = (float) $representative->fresh()->balance;

            // Generate default description if not provided
            $defaultDescriptions = [
                'deposit' => 'إيداع رصيد',
                'commission' => 'عمولة',
                'bonus' => 'مكافأة',
            ];
            $finalDescription = $description ?? ($defaultDescriptions[$type] ?? 'إضافة رصيد');

            return RepresentativeTransaction::create([
                'representative_id' => $representative->id,
                'type' => TransactionType::from($type),
                'amount' => $amount,
                'status' => TransactionStatus::COMPLETED,
                'description' => $finalDescription,
                'created_by' => $user?->id,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
            ]);
        });
    }

    /**
     * Deduct balance from representative with transaction record.
     */
    public function deductBalance(
        Representative $representative,
        float $amount,
        string $description,
        ?User $user = null
    ): RepresentativeTransaction {
        return DB::transaction(function () use ($representative, $amount, $description, $user) {
            $balanceBefore = (float) $representative->balance;
            $representative->decrement('balance', $amount);
            $balanceAfter = (float) $representative->fresh()->balance;

            return RepresentativeTransaction::create([
                'representative_id' => $representative->id,
                'type' => TransactionType::WITHDRAWAL,
                'amount' => $amount,
                'status' => TransactionStatus::COMPLETED,
                'description' => $description,
                'created_by' => $user?->id,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
            ]);
        });
    }

    /**
     * Create a withdrawal request.
     */
    public function createWithdrawalRequest(Representative $representative, array $data): WithdrawalRequest
    {
        return DB::transaction(function () use ($representative, $data) {
            $minAmount = $this->getMinWithdrawalAmount($representative);
            $amount = (float) $data['amount'];

            // Validate minimum amount
            if ($amount < $minAmount) {
                throw new \Exception("المبلغ يجب أن يكون على الأقل {$minAmount}");
            }

            // Validate available balance
            if (!$representative->canWithdraw($amount)) {
                throw new \Exception("الرصيد المتاح غير كافي");
            }

            $request = WithdrawalRequest::create([
                'representative_id' => $representative->id,
                'amount' => $amount,
                'status' => WithdrawalStatus::PENDING,
                'notes' => $data['notes'] ?? null,
                'requested_at' => now(),
                'is_direct_withdrawal' => false,
            ]);

            // Send notification to admins
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();

            if ($admins->isNotEmpty()) {
                $representative = $request->representative;
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'withdrawal_request',
                        'title' => 'طلب سحب جديد',
                        'body' => "طلب سحب جديد من {$representative->name} بمبلغ " . format_currency($request->amount),
                        'data' => [
                            'withdrawal_request_id' => $request->id,
                            'representative_id' => $representative->id,
                            'representative_name' => $representative->name,
                            'amount' => $request->amount,
                            'url' => route('admin.withdrawals.show', $request),
                        ],
                    ]);
                }
            }

            return $request;
        });
    }

    /**
     * Approve a withdrawal request.
     */
    public function approveWithdrawalRequest(WithdrawalRequest $request, User $user, ?string $notes = null): void
    {
        DB::transaction(function () use ($request, $user, $notes) {
            $representative = $request->representative;

            // Calculate available balance excluding the current request
            // because it's already included in pending withdrawals
            $pendingAmount = (float) $representative->withdrawalRequests()
                ->where('status', \App\Enums\WithdrawalStatus::PENDING)
                ->where('id', '!=', $request->id) // Exclude current request
                ->sum('amount');
            
            $availableBalance = max(0, (float) $representative->balance - $pendingAmount);

            // Validate available balance
            if ($availableBalance < (float) $request->amount) {
                throw new \Exception("الرصيد المتاح غير كافي. الرصيد المتاح: " . format_currency($availableBalance) . " والمبلغ المطلوب: " . format_currency($request->amount));
            }

            // Approve the request
            $request->approve($user, $notes);

            // Deduct balance
            $balanceBefore = (float) $representative->balance;
            $representative->decrement('balance', $request->amount);
            $balanceAfter = (float) $representative->fresh()->balance;

            // Create transaction
            RepresentativeTransaction::create([
                'representative_id' => $representative->id,
                'type' => TransactionType::WITHDRAWAL,
                'amount' => $request->amount,
                'status' => TransactionStatus::COMPLETED,
                'description' => "سحب رصيد - طلب #{$request->id}",
                'withdrawal_request_id' => $request->id,
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
            ]);
        });
    }

    /**
     * Reject a withdrawal request.
     */
    public function rejectWithdrawalRequest(WithdrawalRequest $request, User $user, string $reason): void
    {
        $request->reject($user, $reason);
    }

    /**
     * Direct withdrawal by admin without request.
     */
    public function directWithdraw(Representative $representative, array $data, User $user): WithdrawalRequest
    {
        return DB::transaction(function () use ($representative, $data, $user) {
            $amount = (float) $data['amount'];

            // Validate available balance
            if ((float) $representative->available_balance < $amount) {
                throw new \Exception("الرصيد المتاح غير كافي");
            }

            // Create withdrawal request (direct)
            $request = WithdrawalRequest::create([
                'representative_id' => $representative->id,
                'amount' => $amount,
                'status' => WithdrawalStatus::APPROVED,
                'notes' => $data['description'] ?? null,
                'requested_at' => now(),
                'approved_by' => $user->id,
                'approved_at' => now(),
                'is_direct_withdrawal' => true,
            ]);

            // Deduct balance
            $balanceBefore = (float) $representative->balance;
            $representative->decrement('balance', $amount);
            $balanceAfter = (float) $representative->fresh()->balance;

            // Create transaction
            RepresentativeTransaction::create([
                'representative_id' => $representative->id,
                'type' => TransactionType::WITHDRAWAL,
                'amount' => $amount,
                'status' => TransactionStatus::COMPLETED,
                'description' => $data['description'] ?? "سحب مباشر من المدير",
                'withdrawal_request_id' => $request->id,
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
            ]);

            return $request;
        });
    }

    /**
     * Get minimum withdrawal amount for a representative.
     */
    public function getMinWithdrawalAmount(Representative $representative): float
    {
        return \App\Models\WithdrawalSetting::getMinWithdrawalForRepresentative($representative);
    }

    /**
     * Get account statistics for a representative.
     */
    public function getAccountStatistics(Representative $representative): array
    {
        $transactions = $representative->transactions;

        return [
            'total_deposits' => (float) $transactions
                ->where('type', TransactionType::DEPOSIT)
                ->where('status', TransactionStatus::COMPLETED)
                ->sum('amount'),
            'total_withdrawals' => (float) $transactions
                ->where('type', TransactionType::WITHDRAWAL)
                ->where('status', TransactionStatus::COMPLETED)
                ->sum('amount'),
            'total_commissions' => (float) $transactions
                ->where('type', TransactionType::COMMISSION)
                ->where('status', TransactionStatus::COMPLETED)
                ->sum('amount'),
            'total_bonuses' => (float) $transactions
                ->where('type', TransactionType::BONUS)
                ->where('status', TransactionStatus::COMPLETED)
                ->sum('amount'),
            'total_deductions' => (float) $transactions
                ->where('type', TransactionType::DEDUCTION)
                ->where('status', TransactionStatus::COMPLETED)
                ->sum('amount'),
            'total_transactions' => $transactions->count(),
            'pending_withdrawals' => (float) $representative->pending_withdrawals_amount,
        ];
    }
}

