<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{

      use HasFactory;


    protected $fillable = [
        'product_id','variant_id','path','alt_text','is_primary','sort_order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];



    public function product()
{
    return $this->belongsTo(\App\Models\Product::class);
}

public function variant()
{
    return $this->belongsTo(\App\Models\ProductVariant::class, 'variant_id');
}
}
