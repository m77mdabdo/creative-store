<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmEmailChangeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $name,
        public readonly string $confirmUrl,
        public readonly int $validMinutes,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('account.email_change.subject', ['store' => config('app.name')]));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.confirm-email-change');
    }
}
