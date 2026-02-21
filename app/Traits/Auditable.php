<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::writeAudit($model, 'create', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            self::writeAudit($model, 'update', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            self::writeAudit($model, 'delete', $model->getOriginal(), null);
        });
    }

    protected static function writeAudit($model, string $action, $old, $new): void
    {
        if (!auth()->check()) return;

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => $action,
            'auditable_type' => get_class($model),
            'auditable_id'   => $model->getKey(),
            'old_values'     => $old,
            'new_values'     => $new,
            'ip_address'     => request()?->ip(),
        ]);
    }
}
