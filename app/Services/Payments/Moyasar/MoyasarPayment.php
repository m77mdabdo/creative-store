<?php

namespace App\Services\Payments\Moyasar;

use App\Models\Payment;

/**
 * Immutable snapshot of a payment as reported by Moyasar.
 */
final class MoyasarPayment
{
    private const SUCCESS_STATUSES = ['paid', 'captured'];

    private const FAILURE_STATUSES = ['failed', 'voided'];

    public function __construct(
        public readonly ?string $id,
        public readonly string $status,
        public readonly int $amount,
        public readonly ?string $currency,
        public readonly ?int $orderId,
        public readonly array $raw,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $orderId = data_get($data, 'metadata.order_id');

        return new self(
            id: data_get($data, 'id'),
            status: strtolower((string) data_get($data, 'status', '')),
            amount: (int) data_get($data, 'amount', 0),
            currency: data_get($data, 'currency') ? strtoupper((string) data_get($data, 'currency')) : null,
            orderId: is_numeric($orderId) ? (int) $orderId : null,
            raw: $data,
        );
    }

    public function isSuccessful(): bool
    {
        return in_array($this->status, self::SUCCESS_STATUSES, true);
    }

    public function isFailed(): bool
    {
        return in_array($this->status, self::FAILURE_STATUSES, true);
    }

    /**
     * Translate the gateway status into a value allowed by the payments table.
     */
    public function localStatus(): string
    {
        return match (true) {
            $this->isSuccessful()          => Payment::STATUS_CAPTURED,
            $this->status === 'authorized' => Payment::STATUS_AUTHORIZED,
            $this->status === 'refunded'   => Payment::STATUS_REFUNDED,
            $this->isFailed()              => Payment::STATUS_FAILED,
            default                        => Payment::STATUS_PENDING,
        };
    }
}
