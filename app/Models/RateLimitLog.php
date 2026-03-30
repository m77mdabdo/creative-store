<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;


class RateLimitLog extends Model
{

      use HasFactory , Auditable ;

    protected $fillable = [
        'key','endpoint','hits','window_start','blocked_until'
    ];

    protected $casts = [
        'hits' => 'integer',
        'window_start' => 'datetime',
        'blocked_until' => 'datetime',
    ];
}
