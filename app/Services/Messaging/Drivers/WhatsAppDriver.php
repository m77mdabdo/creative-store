<?php

namespace App\Services\Messaging\Drivers;

use App\Services\Messaging\MessagingDriver;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * WhatsApp Business Cloud API (Meta).
 */
class WhatsAppDriver implements MessagingDriver
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function send(string $to, string $body): void
    {
        if (empty($this->config['token']) || empty($this->config['phone_number_id'])) {
            throw new RuntimeException('WHATSAPP_TOKEN and WHATSAPP_PHONE_NUMBER_ID must be set.');
        }

        $payload = ['messaging_product' => 'whatsapp', 'to' => $to];

        $payload += $this->config['template']
            ? ['type' => 'template', 'template' => [
                'name'       => $this->config['template'],
                'language'   => ['code' => $this->config['template_lang'] ?? 'ar'],
                'components' => [['type' => 'body', 'parameters' => [['type' => 'text', 'text' => $body]]]],
            ]]
            : ['type' => 'text', 'text' => ['body' => $body]];

        $response = Http::withToken($this->config['token'])->timeout(15)
            ->post(rtrim($this->config['endpoint'], '/') . '/' . $this->config['phone_number_id'] . '/messages', $payload);

        if (! $response->successful()) {
            throw new RuntimeException('WhatsApp rejected the message: ' . $response->body());
        }
    }
}
