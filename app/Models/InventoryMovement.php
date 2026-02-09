<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryMovement extends Model
{

      use HasFactory;


    protected $fillable = [
        'product_id','variant_id','type','quantity',
        'reference_type','reference_id','note','created_by'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reference_id' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
