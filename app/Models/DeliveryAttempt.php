<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;


class DeliveryAttempt extends Model
{
    use HasFactory , Auditable ;

    protected $fillable = [
        'shipment_id',
        'attempt_no',
        'status',
        'driver_name',
        'driver_phone',
        'location',
        'failure_reason',
        'notes',
        'attempted_at',
        'next_attempt_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'next_attempt_at' => 'datetime',
    ];

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_FAILED = 'failed';
    public const STATUS_RESCHEDULED = 'rescheduled';
    public const STATUS_CANCELLED = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_SCHEDULED,
            self::STATUS_OUT_FOR_DELIVERY,
            self::STATUS_DELIVERED,
            self::STATUS_FAILED,
            self::STATUS_RESCHEDULED,
            self::STATUS_CANCELLED,
        ];
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SCHEDULED => 'badge badge-secondary',
            self::STATUS_OUT_FOR_DELIVERY => 'badge badge-info',
            self::STATUS_DELIVERED => 'badge badge-success',
            self::STATUS_FAILED => 'badge badge-danger',
            self::STATUS_RESCHEDULED => 'badge badge-warning',
            self::STATUS_CANCELLED => 'badge badge-dark',
            default => 'badge badge-secondary',
        };
    }
}
