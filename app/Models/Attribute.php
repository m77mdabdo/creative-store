<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute as CastAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class Attribute extends Model
{
    use HasFactory, Auditable;

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

    /**
     * Option name in the current language, e.g. "Color" → "اللون".
     */
    protected function label(): CastAttribute
    {
        return CastAttribute::get(function () {
            $key = 'attributes.names.' . $this->slug;

            return Lang::has($key) ? __($key) : $this->name;
        });
    }
}
