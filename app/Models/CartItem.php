<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{

      use HasFactory;


    protected $fillable = [
        'cart_id','product_id','variant_id',
        'quantity','price_snapshot','tax_rate_snapshot'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_snapshot' => 'decimal:2',
        'tax_rate_snapshot' => 'decimal:2',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
