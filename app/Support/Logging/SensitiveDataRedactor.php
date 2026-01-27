<?php

namespace App\Support\Logging;

use Illuminate\Database\QueryException;
use Throwable;

/**
 * Removes personal data and secrets from error text before it is stored
 * in the error_logs table, where every staff member with error-logs.view
 * can read it.
 */
final class SensitiveDataRedactor
{
    private const PATTERNS = [
        // name=value pairs for anything secret-looking
        '/\b(password|passwd|pwd|secret|token|api[_-]?key|authorization|otp|code)(["\']?\s*[:=]\s*["\']?)([^\s"\',&)]+)/i' => '$1$2[redacted]',
        // Bearer / Basic credentials
        '/\b(Bearer|Basic)\s+[A-Za-z0-9._~+\/=-]{8,}/i' => '$1 [redacted]',
        // e-mail addresses
        '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i' => '[email]',
        // card-like digit runs (13-19 digits, optional spaces/dashes)
        '/\b(?:\d[ -]?){12,18}\d\b/' => '[number]',
        // Saudi / international phone numbers
        '/(?<!\d)(?:\+?966|00966|0)?5\d{8}(?!\d)/' => '[phone]',
        '/\+\d{9,15}\b/' => '[phone]',
    ];

    public static function message(Throwable $e): string
    {
        // Query errors repeat the SQL with every bound value filled in
        // (emails, phone numbers, addresses). Keep the SQL with "?" instead.
        if ($e instanceof QueryException) {
            $reason = (string) ($e->errorInfo[2] ?? class_basename($e));

            return self::redact($reason . ' (SQL: ' . $e->getSql() . ')');
        }

        $message = trim($e->getMessage());

        return self::redact($message === '' ? class_basename($e) : $message);
    }

    public static function redact(string $text): string
    {
        return (string) preg_replace(array_keys(self::PATTERNS), array_values(self::PATTERNS), $text);
    }
}
