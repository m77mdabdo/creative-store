<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;
class Product extends Model
{

      use HasFactory;
    use SoftDeletes;
    use Auditable;

    protected $fillable = [
        'category_id','user_id',
        'slug','sku','base_price','currency','status',
        'has_variants','tax_rate','weight','is_featured',
        'meta_title','meta_description'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'weight' => 'decimal:2',
        'has_variants' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }





    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function inventoryReservations()
    {
        return $this->hasMany(InventoryReservation::class);
    }


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function translation($locale = null)
{
    $locale = $locale ?? app()->getLocale();
    return $this->translations->firstWhere('locale', $locale)
        ?? $this->translations->firstWhere('locale', config('app.fallback_locale'))
        ?? $this->translations->first();
}

public function getNameAttribute()
{
    return optional($this->translation())->name;
}

public function coupons()
{
    return $this->hasMany(\App\Models\Coupon::class);
}

public function reviews()
{
    return $this->hasMany(Review::class)->approved()->latest();
}

public function allReviews()
{
    return $this->hasMany(Review::class)->latest();
}

public function averageRating(): float
{
    return (float) $this->reviews()->avg('rating');
}

public function reviewsCount(): int
{
    return $this->reviews()->count();
}







public function variants()
{
    return $this->hasMany(ProductVariant::class);
}

public function images()
{
    return $this->hasMany(ProductImage::class);
}

public function primaryImage()
{
    return $this->hasOne(ProductImage::class)
        ->whereNull('variant_id')
        ->where('is_primary', true);
}

public function generalImages()
{
    return $this->hasMany(ProductImage::class)
        ->whereNull('variant_id')
        ->orderBy('sort_order')
        ->orderByDesc('id');
}

public function attributes()
{
    return $this->belongsToMany(Attribute::class, 'product_attributes');
}

public function colorAttribute()
{
    return $this->attributes()
        ->where(function ($q) {
            $q->where('slug', 'color')
              ->orWhere('name', 'Color')
              ->orWhere('name', 'color')
              ->orWhere('name', 'اللون');
        })
        ->first();
}

public function variantsWithColor()
{
    return $this->variants()
        ->with([
            'values.attribute',
            'values.value',
            'images',
            'primaryImage',
        ]);
}

}
