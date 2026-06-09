<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{

      use HasFactory, MassPrunable;

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

    /**
     * Rows older than the retention window are deleted by `model:prune`.
     */
    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<', now()->subDays(365));
    }
}
