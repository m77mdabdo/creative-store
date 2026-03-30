<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;


class OrderStatusLog extends Model
{


      use HasFactory , Auditable ;

    protected $fillable = [
        'order_id','from_status','to_status','changed_by','note'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
