<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttribute extends Pivot
{

      use HasFactory;

    protected $table = 'product_attributes';
    protected $fillable = ['product_id','attribute_id'];
}
