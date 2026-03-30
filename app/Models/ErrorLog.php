<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;


class ErrorLog extends Model
{

      use HasFactory , Auditable ;

    protected $fillable = [
        'level','service','message','context','trace','resolved_at'
    ];

    protected $casts = [
        'context' => 'array',
        'resolved_at' => 'datetime',
    ];
}
