<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;


class ShipmentTrackingLog extends Model
{
    use Auditable ;
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
