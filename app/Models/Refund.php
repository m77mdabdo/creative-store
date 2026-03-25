<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'return_id',
        'amount',
        'status',
        'transaction_id',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSED,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
        ];
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('transaction_id', 'like', "%{$search}%")
              ->orWhereHas('payment', function (Builder $paymentQ) use ($search) {
                  $paymentQ->where('transaction_id', 'like', "%{$search}%")
                           ->orWhereHas('order', function (Builder $orderQ) use ($search) {
                               $orderQ->where('order_number', 'like', "%{$search}%")
                                      ->orWhereHas('user', function (Builder $userQ) use ($search) {
                                          $userQ->where('name', 'like', "%{$search}%")
                                                ->orWhere('email', 'like', "%{$search}%");
                                      });
                           });
              });
        });
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PROCESSED => 'badge badge-success',
            self::STATUS_PENDING => 'badge badge-warning',
            self::STATUS_FAILED, self::STATUS_CANCELLED => 'badge badge-danger',
            default => 'badge badge-secondary',
        };
    }
}
