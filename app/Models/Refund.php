<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refund extends Model
{

      use HasFactory;


    protected $fillable = [
        'payment_id','return_id','amount','status','transaction_id','processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }
}
