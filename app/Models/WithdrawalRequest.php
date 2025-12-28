<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'representative_id',
        'amount',
        'status',
        'bank_name',
        'account_number',
        'account_holder_name',
        'iban',
        'notes',
        'requested_at',
        'approved_by',
        'approved_at',
        'rejected_reason',
        'is_direct_withdrawal',
    ];

    protected function casts(): array
    {
        return [
            'status' => WithdrawalStatus::class,
            'amount' => 'decimal:2',
            'requested_at' => 'datetime',
            'approved_at' => 'datetime',
            'is_direct_withdrawal' => 'boolean',
        ];
    }

    /**
     * Get the representative that owns the withdrawal request.
     */
    public function representative(): BelongsTo
    {
        return $this->belongsTo(Representative::class);
    }

    /**
     * Get the user who approved the withdrawal request.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the transactions associated with the withdrawal request.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(RepresentativeTransaction::class);
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', WithdrawalStatus::PENDING);
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', WithdrawalStatus::APPROVED);
    }

    /**
     * Scope a query to only include rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', WithdrawalStatus::REJECTED);
    }

    /**
     * Check if request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === WithdrawalStatus::PENDING;
    }

    /**
     * Check if request can be approved.
     */
    public function canBeApproved(): bool
    {
        return $this->isPending();
    }

    /**
     * Approve the withdrawal request.
     */
    public function approve(User $user, ?string $notes = null): void
    {
        $this->update([
            'status' => WithdrawalStatus::APPROVED,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Reject the withdrawal request.
     */
    public function reject(User $user, string $reason): void
    {
        $this->update([
            'status' => WithdrawalStatus::REJECTED,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejected_reason' => $reason,
        ]);
    }

    /**
     * Get the status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->getLabel();
    }
}
