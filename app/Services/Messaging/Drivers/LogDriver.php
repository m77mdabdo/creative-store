<?php

namespace App\Services\Messaging\Drivers;

use App\Services\Messaging\MessagingDriver;
use Illuminate\Support\Facades\Log;

class LogDriver implements MessagingDriver
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function send(string $to, string $body): void
    {
        Log::info('Text message (not sent, log driver)', ['to' => $to, 'body' => $body]);
    }
}
