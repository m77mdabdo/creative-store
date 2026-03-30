<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Coupon extends Model
{
    use SoftDeletes, HasFactory , Auditable ;

    protected $fillable = [
        'product_id',
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'per_user_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'per_user_limit' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isProductCoupon(): bool
    {
        return !is_null($this->product_id);
    }

    public function isCartCoupon(): bool
    {
        return is_null($this->product_id);
    }
}
