<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Payment extends Model
{
    use HasFactory, Auditable;

    public const STATUS_PENDING            = 'pending';
    public const STATUS_AUTHORIZED         = 'authorized';
    public const STATUS_CAPTURED           = 'captured';
    public const STATUS_PAID               = 'paid';
    public const STATUS_FAILED             = 'failed';
    public const STATUS_REFUNDED           = 'refunded';
    public const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';

    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'paid_at',
        'idempotency_key',
        'raw_response',
        'exchange_rate',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'paid_at'       => 'datetime',
        'raw_response'  => 'array',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_AUTHORIZED,
            self::STATUS_CAPTURED,
            self::STATUS_PAID,
            self::STATUS_FAILED,
            self::STATUS_REFUNDED,
            self::STATUS_PARTIALLY_REFUNDED,
        ];
    }

    public static function paidLikeStatuses(): array
    {
        return [
            self::STATUS_AUTHORIZED,
            self::STATUS_CAPTURED,
            self::STATUS_PAID,
            self::STATUS_REFUNDED,
            self::STATUS_PARTIALLY_REFUNDED,
        ];
    }

    public static function failedLikeStatuses(): array
    {
        return [
            self::STATUS_FAILED,
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'payment_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaidLike(Builder $query): Builder
    {
        return $query->whereIn('status', self::paidLikeStatuses());
    }

    public function scopeFailedLike(Builder $query): Builder
    {
        return $query->whereIn('status', self::failedLikeStatuses());
    }

    public function scopeGateway(Builder $query, ?string $gateway): Builder
    {
        if (blank($gateway)) {
            return $query;
        }

        return $query->where('gateway', $gateway);
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('gateway', 'like', "%{$term}%")
                ->orWhere('transaction_id', 'like', "%{$term}%")
                ->orWhere('currency', 'like', "%{$term}%")
                ->orWhere('status', 'like', "%{$term}%")
                ->orWhereHas('order', function (Builder $orderQuery) use ($term) {
                    $orderQuery->where('order_number', 'like', "%{$term}%");
                });
        });
    }

    public function isPaidLike(): bool
    {
        return in_array($this->status, self::paidLikeStatuses(), true);
    }

    public function isFailedLike(): bool
    {
        return in_array($this->status, self::failedLikeStatuses(), true);
    }

    public function isRefunded(): bool
    {
        return in_array($this->status, [
            self::STATUS_REFUNDED,
            self::STATUS_PARTIALLY_REFUNDED,
        ], true);
    }
}
