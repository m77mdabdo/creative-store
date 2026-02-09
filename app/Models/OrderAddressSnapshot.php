<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderAddressSnapshot extends Model
{

      use HasFactory;

    protected $fillable = [
        'order_id','full_name','phone','country','city','district','street',
        'building_no','apartment_no','postal_code','lat','lng','notes'
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
