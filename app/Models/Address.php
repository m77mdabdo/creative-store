<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
      use HasFactory;
    
    protected $fillable = [
        'user_id','label','full_name','phone','country','city','district',
        'street','building_no','apartment_no','postal_code','lat','lng',
        'notes','is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }
}
