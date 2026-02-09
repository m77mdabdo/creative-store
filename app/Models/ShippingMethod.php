<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingMethod extends Model
{

      use HasFactory;

    protected $fillable = ['name','code','is_active'];

    protected $casts = [
        'is_active' => 'boolean',
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
