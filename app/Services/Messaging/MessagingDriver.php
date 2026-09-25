<?php

namespace App\Services\Messaging;

interface MessagingDriver
{
    /**
     * Send a message to an international number without "+" (9665XXXXXXXX).
     */
    public function send(string $to, string $body): void;
}
