<?php

namespace App\Support\Zatca;

use Carbon\CarbonInterface;
use InvalidArgumentException;

/**
 * ZATCA (Fatoora) Phase 1 QR payload for simplified tax invoices.
 *
 * The QR code holds a Base64 string of five TLV fields (tag, length, value),
 * where the length is the value's size in bytes (UTF-8):
 *   1 seller name, 2 VAT registration number, 3 invoice timestamp (ISO 8601),
 *   4 invoice total including VAT, 5 VAT total.
 */
final class ZatcaQr
{
    public static function payload(
        string $sellerName,
        string $vatNumber,
        CarbonInterface $issuedAt,
        float $totalWithVat,
        float $vatTotal,
    ): string {
        if (! preg_match('/^3\d{13}3$/', $vatNumber)) {
            throw new InvalidArgumentException('A Saudi VAT number has 15 digits and starts and ends with 3.');
        }

        $fields = [
            1 => $sellerName,
            2 => $vatNumber,
            3 => $issuedAt->copy()->utc()->format('Y-m-d\TH:i:s\Z'),
            4 => number_format($totalWithVat, 2, '.', ''),
            5 => number_format($vatTotal, 2, '.', ''),
        ];

        $binary = '';

        foreach ($fields as $tag => $value) {
            $length = strlen($value);

            if ($length > 255) {
                throw new InvalidArgumentException("QR field {$tag} is longer than 255 bytes.");
            }

            $binary .= chr($tag) . chr($length) . $value;
        }

        return base64_encode($binary);
    }

    /**
     * Decode a payload back into its fields (used by tests and support tools).
     *
     * @return array<int, string>
     */
    public static function decode(string $payload): array
    {
        $binary = base64_decode($payload, true);

        if ($binary === false) {
            throw new InvalidArgumentException('The QR payload is not valid Base64.');
        }

        $fields = [];
        $offset = 0;

        while ($offset < strlen($binary)) {
            $tag = ord($binary[$offset]);
            $length = ord($binary[$offset + 1]);
            $fields[$tag] = substr($binary, $offset + 2, $length);
            $offset += 2 + $length;
        }

        return $fields;
    }
}
