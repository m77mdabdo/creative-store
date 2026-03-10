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

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes');
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function inventoryReservations()
    {
        return $this->hasMany(InventoryReservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
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

public function averageRating()
{
    return $this->reviews()->avg('rating') ?? 0;
}

public function reviewsCount()
{
    return $this->reviews()->count();
}

}
