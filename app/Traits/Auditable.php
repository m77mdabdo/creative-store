<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Records who changed what on a model.
 *
 * Only the changed columns are stored on update, and secrets (hidden
 * attributes such as password hashes, tokens and OTPs) never reach the log.
 */
trait Auditable
{
    private static array $auditRedacted = [
        'password',
        'remember_token',
        'otp',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'raw_response',
        'payload',
    ];

    public static function bootAuditable(): void
    {
        static::created(fn (Model $model) => self::writeAudit($model, 'create', null, $model->getAttributes()));

        static::updated(function (Model $model) {
            $changes = Arr::except($model->getChanges(), ['updated_at']);

            if ($changes === []) {
                return;
            }

            self::writeAudit($model, 'update', Arr::only($model->getOriginal(), array_keys($changes)), $changes);
        });

        static::deleted(fn (Model $model) => self::writeAudit($model, 'delete', $model->getOriginal(), null));
    }

    protected static function writeAudit(Model $model, string $action, ?array $old, ?array $new): void
    {
        if (! auth()->check()) {
            return;
        }

        $redact = array_merge(self::$auditRedacted, $model->getHidden());

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => $action,
            'auditable_type' => $model::class,
            'auditable_id'   => $model->getKey(),
            'old_values'     => $old === null ? null : Arr::except($old, $redact),
            'new_values'     => $new === null ? null : Arr::except($new, $redact),
            'ip_address'     => request()?->ip(),
        ]);
    }
}
