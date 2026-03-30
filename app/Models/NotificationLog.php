<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;


class NotificationLog extends Model
{
    use HasFactory , Auditable ;

    protected $fillable = [
        'user_id',
        'order_id',
        'channel',
        'type',
        'status',
        'recipient',
        'subject',
        'message',
        'meta',
        'sent_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
