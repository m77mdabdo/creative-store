<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;


class ProductTranslation extends Model
{

      use HasFactory , Auditable ;


    protected $fillable = [
        'product_id','locale','name','description'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
