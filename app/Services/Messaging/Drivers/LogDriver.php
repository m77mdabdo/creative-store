<?php

namespace App\Services\Messaging\Drivers;

use App\Services\Messaging\MessagingDriver;
use Illuminate\Support\Facades\Log;

/**
 * Used when no SMS provider is configured. It records that a message
 * would have been sent without writing the customer's number or the text
 * itself (verification codes, order details) into the log files.
 */
class LogDriver implements MessagingDriver
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function send(string $to, string $body): void
    {
        Log::info('Text message (not sent, log driver)', [
            'to'     => self::maskNumber($to),
            'length' => mb_strlen($body),
        ]);
    }

    /** "+966551234567" -> "+9665******67" */
    public static function maskNumber(string $number): string
    {
        $digits = preg_replace('/\s+/', '', $number);
        $length = strlen($digits);

        if ($length <= 6) {
            return str_repeat('*', $length);
        }

        return substr($digits, 0, 5) . str_repeat('*', $length - 7) . substr($digits, -2);
    }
}
