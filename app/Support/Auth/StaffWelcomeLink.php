<?php

namespace App\Support\Auth;

use App\Models\User;
use Illuminate\Support\Facades\URL;

/**
 * The "set your password and sign in" link sent to new team members.
 *
 * It is a signed URL valid for a few days that also carries a fingerprint
 * of the current password hash, so it stops working the moment the
 * password is set (single use) or changed by someone else.
 */
final class StaffWelcomeLink
{
    public const VALID_DAYS = 3;

    public static function for(User $user): string
    {
        return URL::temporarySignedRoute(
            'staff.welcome',
            now()->addDays(self::VALID_DAYS),
            ['user' => $user->getKey(), 'v' => self::fingerprint($user)],
        );
    }

    public static function isCurrent(User $user, ?string $fingerprint): bool
    {
        return is_string($fingerprint) && hash_equals(self::fingerprint($user), $fingerprint);
    }

    private static function fingerprint(User $user): string
    {
        return substr(hash_hmac('sha256', (string) $user->getAuthPassword(), (string) config('app.key')), 0, 24);
    }
}
