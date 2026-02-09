<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{

      use HasFactory;

    protected $fillable = [
        'order_id','product_id','variant_id',
        'product_name_snapshot','sku_snapshot',
        'quantity','unit_price','tax_rate','tax_amount','line_total'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class, 'order_item_id');
    }
}
