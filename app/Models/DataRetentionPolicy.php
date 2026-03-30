<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class DataRetentionPolicy extends Model
{

      use HasFactory , Auditable ;

    protected $fillable = [
        'target_table','retain_days','is_anonymize','is_active'
    ];

    protected $casts = [
        'retain_days' => 'integer',
        'is_anonymize' => 'boolean',
        'is_active' => 'boolean',
    ];
}
