<?php

namespace App\Support\Auth;

use Illuminate\Validation\Rules\Password;

/**
 * Password rule for dashboard accounts. Staff can see customers' orders,
 * addresses and payments, so their passwords are held to a higher bar
 * than customers' (8 characters, letters and numbers).
 */
final class StaffPassword
{
    public const MIN_LENGTH = 10;

    public static function rule(): Password
    {
        return Password::min(self::MIN_LENGTH)->letters()->mixedCase()->numbers();
    }
}
