<?php

namespace App\Services\Messaging;

use InvalidArgumentException;

class Messenger
{
    public function driver(?string $key = null): MessagingDriver
    {
        $key ??= config('messaging.driver');
        $config = config("messaging.drivers.{$key}");

        if (! $config || ! class_exists($config['class'] ?? '')) {
            throw new InvalidArgumentException("Unknown messaging driver [{$key}].");
        }

        return app()->make($config['class'], ['config' => $config]);
    }

    public function send(string $phone, string $body): bool
    {
        $to = self::normalize($phone);

        if (! $to) {
            return false;
        }

        $this->driver()->send($to, $body);

        return true;
    }

    /**
     * 05XXXXXXXX, +9665XXXXXXXX, 009665XXXXXXXX → 9665XXXXXXXX.
     */
    public static function normalize(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '' || $digits === null) {
            return null;
        }

        $digits = preg_replace('/^00/', '', $digits);

        if (str_starts_with($digits, '0')) {
            $digits = config('messaging.default_country_code', '966') . substr($digits, 1);
        }

        return strlen($digits) >= 10 && strlen($digits) <= 15 ? $digits : null;
    }
}
