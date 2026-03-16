<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attribute extends Model
{

      use HasFactory;

    protected $fillable = ['name','slug','is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attributes');
    }

    public function activeValues()
    {
        return $this->hasMany(AttributeValue::class)->where('is_active', true);
    }

}
