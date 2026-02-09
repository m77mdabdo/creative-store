<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{


      use HasFactory;

    protected $fillable = [
        'order_id','gateway','transaction_id','idempotency_key',
        'amount','currency','exchange_rate','status',
        'raw_response','paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'raw_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
