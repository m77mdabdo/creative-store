<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailySalesAggregate extends Model
{

      use HasFactory;

    protected $fillable = [
        'day','orders_count','paid_orders_count',
        'gross_sales','discount_total','shipping_total','tax_total','net_sales'
    ];

    protected $casts = [
        'day' => 'date',
        'orders_count' => 'integer',
        'paid_orders_count' => 'integer',
        'gross_sales' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'net_sales' => 'decimal:2',
    ];
}
