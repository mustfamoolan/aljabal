<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Representative extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'image',
        'address',
        'is_active',
        'fcm_token',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'balance' => 'decimal:2',
        ];
    }

    /**
     * Check if representative is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        return asset('storage/' . $this->image);
    }

    /**
     * Route notifications for the FCM channel.
     */
    public function routeNotificationForFcm(): ?string
    {
        return $this->fcm_token;
    }

    /**
     * Get the transactions for the representative.
     */
    public function transactions()
    {
        return $this->hasMany(RepresentativeTransaction::class);
    }

    /**
     * Get the withdrawal requests for the representative.
     */
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    /**
     * Get the withdrawal setting exception for the representative.
     */
    public function withdrawalSetting()
    {
        return $this->hasOne(WithdrawalSetting::class);
    }

    /**
     * Get the orders for the representative.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the available balance (after deducting pending withdrawals).
     */
    public function getAvailableBalanceAttribute(): float
    {
        $pendingAmount = $this->getPendingWithdrawalsAmountAttribute();
        return max(0, (float) $this->balance - $pendingAmount);
    }

    /**
     * Get the sum of pending withdrawal requests.
     */
    public function getPendingWithdrawalsAmountAttribute(): float
    {
        return (float) $this->withdrawalRequests()
            ->where('status', \App\Enums\WithdrawalStatus::PENDING)
            ->sum('amount');
    }

    /**
     * Get the minimum withdrawal amount for this representative.
     */
    public function getMinWithdrawalAmountAttribute(): float
    {
        return WithdrawalSetting::getMinWithdrawalForRepresentative($this);
    }

    /**
     * Add balance to the representative.
     */
    public function addBalance(float $amount, string $type, string $description, ?User $user = null): RepresentativeTransaction
    {
        $balanceBefore = (float) $this->balance;
        $this->increment('balance', $amount);
        $balanceAfter = (float) $this->fresh()->balance;

        return RepresentativeTransaction::create([
            'representative_id' => $this->id,
            'type' => $type,
            'amount' => $amount,
            'status' => \App\Enums\TransactionStatus::COMPLETED,
            'description' => $description,
            'created_by' => $user?->id,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
        ]);
    }

    /**
     * Deduct balance from the representative.
     */
    public function deductBalance(float $amount, string $description, ?User $user = null): RepresentativeTransaction
    {
        $balanceBefore = (float) $this->balance;
        $this->decrement('balance', $amount);
        $balanceAfter = (float) $this->fresh()->balance;

        return RepresentativeTransaction::create([
            'representative_id' => $this->id,
            'type' => \App\Enums\TransactionType::WITHDRAWAL,
            'amount' => $amount,
            'status' => \App\Enums\TransactionStatus::COMPLETED,
            'description' => $description,
            'created_by' => $user?->id,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
        ]);
    }

    /**
     * Check if representative can withdraw the specified amount.
     */
    public function canWithdraw(float $amount): bool
    {
        return $this->available_balance >= $amount && $amount >= $this->min_withdrawal_amount;
    }
}
