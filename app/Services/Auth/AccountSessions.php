<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Ends a user's other sign-ins after their password changes or their
 * account is disabled: "remember me" cookies stop working and stored
 * sessions are removed. Sessions on other drivers are also caught by the
 * AuthenticateSession middleware, which rejects a session whose password
 * hash no longer matches.
 */
class AccountSessions
{
    public function revokeOthers(User $user, ?string $keepSessionId = null): void
    {
        $user->setRememberToken(Str::random(60));
        $user->saveQuietly();

        if (config('session.driver') !== 'database') {
            return;
        }

        $table = (string) config('session.table', 'sessions');

        if (! Schema::hasTable($table)) {
            return;
        }

        DB::table($table)
            ->where('user_id', $user->getKey())
            ->when($keepSessionId, fn ($query) => $query->where('id', '!=', $keepSessionId))
            ->delete();
    }
}
