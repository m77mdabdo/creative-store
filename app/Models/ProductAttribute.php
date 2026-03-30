<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class ProductAttribute extends Pivot
{

      use HasFactory , Auditable ;

    protected $table = 'product_attributes';
    protected $fillable = ['product_id','attribute_id'];
}
