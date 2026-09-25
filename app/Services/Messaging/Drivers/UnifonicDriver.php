<?php

namespace App\Services\Messaging\Drivers;

use App\Services\Messaging\MessagingDriver;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * SMS through Unifonic, a Saudi provider with registered sender IDs.
 */
class UnifonicDriver implements MessagingDriver
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function send(string $to, string $body): void
    {
        if (empty($this->config['app_sid'])) {
            throw new RuntimeException('UNIFONIC_APP_SID is not set.');
        }

        $response = Http::asForm()->timeout(15)->retry(2, 500, throw: false)->post($this->config['endpoint'], [
            'AppSid'    => $this->config['app_sid'],
            'SenderID'  => $this->config['sender_id'],
            'Recipient' => $to,
            'Body'      => $body,
        ]);

        if (! $response->successful() || $response->json('success') === false) {
            throw new RuntimeException('Unifonic rejected the message: ' . $response->body());
        }
    }
}
