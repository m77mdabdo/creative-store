<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentTrackingLog extends Model
{
    protected $fillable = [
        'shipment_id',
        'status',
        'location',
        'carrier_status',
        'note',
        'tracked_at',
    ];

    protected $casts = [
        'tracked_at' => 'datetime',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
