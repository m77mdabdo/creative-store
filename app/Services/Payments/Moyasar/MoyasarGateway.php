<?php

namespace App\Services\Payments\Moyasar;

use App\Exceptions\PaymentGatewayException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoyasarGateway
{
    private const BASE_URL = 'https://api.moyasar.com/v1';

    private const PAYMENT_ID_PATTERN = '/^[A-Za-z0-9\-]{8,64}$/';

    public function __construct(
        private readonly ?string $secretKey,
        private readonly ?string $webhookSecret,
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(
            config('services.moyasar.secret_key'),
            config('services.moyasar.webhook_secret'),
        );
    }

    public function isValidPaymentId(string $paymentId): bool
    {
        return (bool) preg_match(self::PAYMENT_ID_PATTERN, $paymentId);
    }

    /**
     * Fetch the authoritative payment record from Moyasar.
     */
    public function fetchPayment(string $paymentId): MoyasarPayment
    {
        if (! $this->isValidPaymentId($paymentId)) {
            throw new PaymentGatewayException('Invalid payment reference.');
        }

        if (blank($this->secretKey)) {
            throw new PaymentGatewayException('Payment gateway is not configured.');
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->timeout(15)
                ->retry(2, 300, throw: false)
                ->get(self::BASE_URL . '/payments/' . $paymentId);
        } catch (ConnectionException $e) {
            Log::warning('Moyasar connection failed', ['payment_id' => $paymentId]);

            throw new PaymentGatewayException('Payment gateway is unreachable.', previous: $e);
        }

        if (! $response->successful()) {
            Log::warning('Moyasar payment lookup failed', [
                'payment_id' => $paymentId,
                'status'     => $response->status(),
            ]);

            throw new PaymentGatewayException('Unable to verify payment.');
        }

        return MoyasarPayment::fromArray($response->json() ?? []);
    }

    public function hasValidWebhookSecret(?string $token): bool
    {
        if (blank($this->webhookSecret) || blank($token)) {
            return false;
        }

        return hash_equals($this->webhookSecret, $token);
    }
}
