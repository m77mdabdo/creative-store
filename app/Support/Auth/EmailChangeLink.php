<?php

namespace App\Support\Auth;

use App\Models\User;
use Illuminate\Support\Facades\URL;

/**
 * Signed link that confirms a new sign-in email. It is bound to the user,
 * the new address and the address in use when it was sent, so it works
 * once and stops working if the email changes some other way.
 */
final class EmailChangeLink
{
    public const VALID_MINUTES = 60;

    public static function for(User $user, string $newEmail): string
    {
        return URL::temporarySignedRoute('myaccount.email.confirm', now()->addMinutes(self::VALID_MINUTES), [
            'user'  => $user->getKey(),
            'email' => $newEmail,
            'v'     => self::fingerprint($user),
        ]);
    }

    public static function isCurrent(User $user, ?string $fingerprint): bool
    {
        return is_string($fingerprint) && hash_equals(self::fingerprint($user), $fingerprint);
    }

    private static function fingerprint(User $user): string
    {
        return substr(hash_hmac('sha256', 'email-change|' . strtolower((string) $user->email), (string) config('app.key')), 0, 24);
    }
}
