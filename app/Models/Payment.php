<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_id',
        'idempotency_key',
        'amount',
        'currency',
        'exchange_rate',
        'status',
        'raw_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'raw_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_CAPTURED = 'captured';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_AUTHORIZED = 'authorized';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PAID,
            self::STATUS_CAPTURED,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
            self::STATUS_REFUNDED,
            self::STATUS_AUTHORIZED,
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('transaction_id', 'like', "%{$search}%")
                ->orWhere('idempotency_key', 'like', "%{$search}%")
                ->orWhere('gateway', 'like', "%{$search}%")
                ->orWhereHas('order', function (Builder $orderQ) use ($search) {
                    $orderQ->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $userQ) use ($search) {
                            $userQ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
        });
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaidLike(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_PAID,
            self::STATUS_CAPTURED,
        ]);
    }

    public function scopeFailedLike(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
        ]);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PAID, self::STATUS_CAPTURED => 'badge badge-success',
            self::STATUS_PENDING => 'badge badge-warning',
            self::STATUS_FAILED, self::STATUS_CANCELLED => 'badge badge-danger',
            self::STATUS_REFUNDED => 'badge badge-secondary',
            self::STATUS_AUTHORIZED => 'badge badge-info',
            default => 'badge badge-light',
        };
    }

    public function getDisplayGatewayAttribute(): string
    {
        return strtoupper($this->gateway ?: '—');
    }

    public function getDisplayAmountAttribute(): string
    {
        return number_format((float) $this->amount, 2) . ' ' . ($this->currency ?: 'SAR');
    }

    public function getRefundedAmountAttribute(): float
    {
        if (! $this->relationLoaded('refunds')) {
            return (float) $this->refunds()->sum('amount');
        }

        return (float) $this->refunds->sum('amount');
    }
}
