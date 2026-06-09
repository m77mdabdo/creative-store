<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class PaymentWebhookLog extends Model
{

    use HasFactory, MassPrunable;

    protected $fillable = [
        'gateway','event_id','event_type','payload',
        'processed','processed_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    /**
     * Rows older than the retention window are deleted by `model:prune`.
     */
    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<', now()->subDays(180))->where('processed', true);
    }
}
