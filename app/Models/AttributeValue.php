<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute as CastAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class AttributeValue extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'attribute_id', 'value', 'slug', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function variantValues()
    {
        return $this->hasMany(ProductVariantValue::class, 'attribute_value_id');
    }

    /**
     * Display name in the current language (lang/{locale}/attributes.php),
     * falling back to the stored value for anything not translated.
     */
    protected function label(): CastAttribute
    {
        return CastAttribute::get(function () {
            $key = 'attributes.' . $this->slug;

            return Lang::has($key) ? __($key) : $this->value;
        });
    }
}
