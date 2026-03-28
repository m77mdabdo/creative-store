<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbandonedCartRecovery extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'user_id',
        'email',
        'session_id',
        'channel',
        'status',
        'recovery_token',
        'cart_abandoned_at',
        'sent_at',
        'recovered_at',
        'expires_at',
        'note',
        'meta',
    ];

    protected $casts = [
        'cart_abandoned_at' => 'datetime',
        'sent_at'           => 'datetime',
        'recovered_at'      => 'datetime',
        'expires_at'        => 'datetime',
        'meta'              => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRecoverable(): bool
    {
        return $this->status === 'sent'
            && is_null($this->recovered_at)
            && $this->expires_at
            && now()->lessThanOrEqualTo($this->expires_at);
    }
}
