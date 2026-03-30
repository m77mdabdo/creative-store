<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;


class ProductVariant extends Model
{

      use HasFactory , Auditable ;

    protected $fillable = [
        'product_id','sku','price','stock','combination_key','weight',
        'version','is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock' => 'integer',
        'version' => 'integer',
        'is_active' => 'boolean',
    ];





    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'variant_id');
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class, 'variant_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'variant_id');
    }

    public function inventoryReservations()
    {
        return $this->hasMany(InventoryReservation::class, 'variant_id');
    }



//
public function product()
{
    return $this->belongsTo(Product::class);
}

public function values()
{
    return $this->hasMany(ProductVariantValue::class, 'product_variant_id');
}

public function images()
{
    return $this->hasMany(ProductImage::class, 'variant_id')
        ->orderBy('sort_order')
        ->orderByDesc('id');
}

public function primaryImage()
{
    return $this->hasOne(ProductImage::class, 'variant_id')
        ->where('is_primary', true);
}
}
