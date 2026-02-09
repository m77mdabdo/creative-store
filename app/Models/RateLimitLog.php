<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RateLimitLog extends Model
{

      use HasFactory;

    protected $fillable = [
        'key','endpoint','hits','window_start','blocked_until'
    ];

    protected $casts = [
        'hits' => 'integer',
        'window_start' => 'datetime',
        'blocked_until' => 'datetime',
    ];
}
