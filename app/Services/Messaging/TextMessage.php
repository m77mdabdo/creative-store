<?php

namespace App\Services\Messaging;

final class TextMessage
{
    public function __construct(
        public readonly string $body,
        public readonly ?string $to = null,
    ) {
    }
}
