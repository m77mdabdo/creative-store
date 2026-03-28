<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'reason',
        'refund_shipping',
        'refund_tax',
        'shipping_refund_amount',
        'tax_refund_amount',
        'requested_at',
        'processed_at',
    ];

    protected $casts = [
        'refund_shipping' => 'boolean',
        'refund_tax' => 'boolean',
        'shipping_refund_amount' => 'decimal:2',
        'tax_refund_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public const STATUS_REQUESTED = 'requested';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_PARTIAL_REFUNDED = 'partial_refunded';

    public static function statuses(): array
    {
        return [
            self::STATUS_REQUESTED,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_RECEIVED,
            self::STATUS_REFUNDED,
            self::STATUS_PARTIAL_REFUNDED,
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'return_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('reason', 'like', "%{$search}%")
              ->orWhereHas('order', function (Builder $orderQ) use ($search) {
                  $orderQ->where('order_number', 'like', "%{$search}%")
                         ->orWhereHas('user', function (Builder $userQ) use ($search) {
                             $userQ->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                         });
              });
        });
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_REQUESTED => 'badge badge-warning',
            self::STATUS_APPROVED => 'badge badge-info',
            self::STATUS_RECEIVED => 'badge badge-primary',
            self::STATUS_REFUNDED => 'badge badge-success',
            self::STATUS_PARTIAL_REFUNDED => 'badge badge-warning',
            self::STATUS_REJECTED => 'badge badge-danger',
            default => 'badge badge-secondary',
        };
    }

    public function getItemsRefundTotalAttribute(): float
    {
        if (! $this->relationLoaded('items')) {
            return (float) $this->items()->sum('refund_amount');
        }

        return (float) $this->items->sum('refund_amount');
    }

    public function getGrandRefundTotalAttribute(): float
    {
        return (float) $this->items_refund_total
            + (float) ($this->shipping_refund_amount ?? 0)
            + (float) ($this->tax_refund_amount ?? 0);
    }
}
