<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepresentativeTransaction extends Model
{
    protected $fillable = [
        'representative_id',
        'type',
        'amount',
        'status',
        'description',
        'withdrawal_request_id',
        'created_by',
        'approved_by',
        'approved_at',
        'notes',
        'balance_before',
        'balance_after',
    ];

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'status' => TransactionStatus::class,
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the representative that owns the transaction.
     */
    public function representative(): BelongsTo
    {
        return $this->belongsTo(Representative::class);
    }

    /**
     * Get the user who created the transaction.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the transaction.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the withdrawal request associated with the transaction.
     */
    public function withdrawalRequest(): BelongsTo
    {
        return $this->belongsTo(WithdrawalRequest::class);
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', TransactionStatus::PENDING);
    }

    /**
     * Scope a query to only include approved transactions.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', TransactionStatus::APPROVED);
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatus::COMPLETED);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by representative.
     */
    public function scopeForRepresentative($query, Representative $representative)
    {
        return $query->where('representative_id', $representative->id);
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === TransactionStatus::PENDING;
    }

    /**
     * Check if transaction is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === TransactionStatus::APPROVED;
    }

    /**
     * Check if transaction can be approved.
     */
    public function canBeApproved(): bool
    {
        return $this->isPending();
    }

    /**
     * Get the type label in Arabic.
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type->getLabel();
    }

    /**
     * Get the status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->getLabel();
    }
}
