<?php

namespace App\Support\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Password rule for dashboard accounts. Staff can see customers' orders,
 * addresses and payments, so their passwords are held to a higher bar
 * than customers' (8 characters, letters and numbers).
 */
final class StaffPassword
{
    public const MIN_LENGTH = 10;

    /** Session flag set at sign-in when a staff password is below the bar. */
    public const UPGRADE_FLAG = 'auth.staff_password_upgrade';

    public static function rule(): Password
    {
        return Password::min(self::MIN_LENGTH)->letters()->mixedCase()->numbers();
    }

    /**
     * Whether a plain-text password (only known at sign-in) falls short of
     * the staff rule, e.g. one set before the rule existed.
     */
    public static function isWeak(string $plain): bool
    {
        return Validator::make(['password' => $plain], ['password' => [self::rule()]])->fails();
    }
}
