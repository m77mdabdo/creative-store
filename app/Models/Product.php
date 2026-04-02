<?php

namespace App\Models;

use App\Traits\Auditable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes, Auditable, Translatable;

    public array $translatedAttributes = [
        'name',
        'description',
    ];

    protected $fillable = [
        'category_id',
        'user_id',
        'slug',
        'sku',
        'base_price',
        'currency',
        'status',
        'has_variants',
        'tax_rate',
        'weight',
        'is_featured',
        'meta_title',
        'meta_description',
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

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
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
        return $this->variants()->with([
            'values.attribute',
            'values.value',
            'images',
            'primaryImage',
        ]);
    }

    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('slug', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhereHas('translations', function ($t) use ($term) {
                    $t->where('name', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%");
                });
        });
    }
}
