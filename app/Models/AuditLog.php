<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{

      use HasFactory;

    protected $fillable = [
        'user_id','action','auditable_type','auditable_id',
        'old_values','new_values','ip_address'
    ];

    protected $casts = [
        'auditable_id' => 'integer',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
