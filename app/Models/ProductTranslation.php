<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductTranslation extends Model
{

      use HasFactory;


    protected $fillable = [
        'product_id','locale','name','description'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
