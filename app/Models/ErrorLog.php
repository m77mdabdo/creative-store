<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ErrorLog extends Model
{

      use HasFactory, MassPrunable;

    protected $fillable = [
        'level','service','message','context','trace','resolved_at'
    ];

    protected $casts = [
        'context' => 'array',
        'resolved_at' => 'datetime',
    ];

    /**
     * Rows older than the retention window are deleted by `model:prune`.
     */
    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<', now()->subDays(180));
    }
}
