<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingMethod extends Model
{

      use HasFactory;

    protected $fillable = [
    'name',
    'code',
    'description',
    'sort_order',
    'is_active',
];

protected $casts = [
    'is_active' => 'boolean',
    'sort_order' => 'integer',
];

    public function rates()
    {
        return $this->hasMany(ShippingRate::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
