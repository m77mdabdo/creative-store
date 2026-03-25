<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipping_method_id',
        'carrier',
        'tracking_number',
        'status',
        'current_location',
        'carrier_status',
        'shipped_at',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_IN_TRANSIT,
            self::STATUS_DELIVERED,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function method()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('carrier', 'like', "%{$search}%")
                ->orWhere('tracking_number', 'like', "%{$search}%")
                ->orWhere('current_location', 'like', "%{$search}%")
                ->orWhere('carrier_status', 'like', "%{$search}%")
                ->orWhereHas('order', function (Builder $orderQ) use ($search) {
                    $orderQ->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $userQ) use ($search) {
                            $userQ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                })
                ->orWhereHas('method', function (Builder $methodQ) use ($search) {
                    $methodQ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
        });
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'badge badge-warning',
            self::STATUS_PROCESSING,
            self::STATUS_IN_TRANSIT,
            self::STATUS_SHIPPED => 'badge badge-info',
            self::STATUS_DELIVERED => 'badge badge-success',
            self::STATUS_FAILED,
            self::STATUS_CANCELLED => 'badge badge-danger',
            default => 'badge badge-secondary',
        };
    }
}
