<?php

namespace App\Support\Export;

/**
 * Makes a value safe to open in a spreadsheet: text that starts with a
 * formula character (=, +, -, @, tab, carriage return) is prefixed with a
 * quote so Excel and Sheets show it as text instead of running it.
 */
final class CsvCell
{
    public static function safe(mixed $value): mixed
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        return in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true) ? "'" . $value : $value;
    }

    /**
     * @param  array<int, mixed>  $row
     * @return array<int, mixed>
     */
    public static function row(array $row): array
    {
        return array_map(self::safe(...), $row);
    }
}
