<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Cart extends Model
{

      use HasFactory , Auditable ;

    protected $fillable = ['user_id','session_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function reservations()
    {
        return $this->hasMany(InventoryReservation::class);
    }

    public function recoveries()
{
    return $this->hasMany(AbandonedCartRecovery::class);
}

}
