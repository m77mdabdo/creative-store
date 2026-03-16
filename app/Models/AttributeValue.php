<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttributeValue extends Model
{

      use HasFactory;

    protected $fillable = [
        'attribute_id','value','slug','sort_order','is_active'
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

        public function variantValues()
    {
        return $this->hasMany(ProductVariantValue::class, 'attribute_value_id');
    }
}
