<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnRequest extends Model
{


      use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'order_id','user_id','status','reason',
        'refund_shipping','refund_tax',
        'shipping_refund_amount','tax_refund_amount',
        'requested_at','processed_at'
    ];

    protected $casts = [
        'refund_shipping' => 'boolean',
        'refund_tax' => 'boolean',
        'shipping_refund_amount' => 'decimal:2',
        'tax_refund_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

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
}
