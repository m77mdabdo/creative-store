<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingRate extends Model
{
      use HasFactory;

 protected $fillable = [
    'shipping_zone_id',
    'shipping_method_id',
    'city',
    'district',
    'min_weight',
    'max_weight',
    'price',
    'eta_min_days',
    'eta_max_days',
    'free_shipping_threshold',
    'cash_on_delivery_fee',
    'priority',
    'is_active',
];

protected $casts = [
    'min_weight' => 'decimal:2',
    'max_weight' => 'decimal:2',
    'price' => 'decimal:2',
    'free_shipping_threshold' => 'decimal:2',
    'cash_on_delivery_fee' => 'decimal:2',
    'eta_min_days' => 'integer',
    'eta_max_days' => 'integer',
    'priority' => 'integer',
    'is_active' => 'boolean',
];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function method()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }
}
