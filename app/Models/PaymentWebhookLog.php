<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentWebhookLog extends Model
{

      use HasFactory;

    protected $fillable = [
        'gateway','event_id','event_type','payload',
        'processed','processed_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean',
        'processed_at' => 'datetime',
    ];
}
