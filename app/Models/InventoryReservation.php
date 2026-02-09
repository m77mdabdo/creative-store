<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryReservation extends Model
{

      use HasFactory;

    protected $fillable = [
        'cart_id','product_id','variant_id',
        'quantity','expires_at','released_at'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'expires_at' => 'datetime',
        'released_at' => 'datetime',
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
