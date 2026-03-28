<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'shipping_address_id',
        'shipping_method_id',
        'coupon_id',
        'guest_email',
        'guest_phone',
        'currency',
        'exchange_rate',
        'subtotal',
        'discount',
        'shipping_fee',
        'tax_total',
        'total',
        'status',
        'shipping_status',
        'payment_status',
        'payment_method',
        'estimated_delivery_from',
        'estimated_delivery_to',
        'placed_at',
        'paid_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'placed_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'estimated_delivery_from' => 'datetime',
        'estimated_delivery_to' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addressSnapshot()
    {
        return $this->hasOne(OrderAddressSnapshot::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    public function couponUsage()
    {
        return $this->hasOne(CouponUsage::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'order_id');
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }
}
