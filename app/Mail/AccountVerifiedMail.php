<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AccountVerifiedMail extends Mailable
{
    public string $name;
    public string $loginUrl;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->loginUrl = route('login');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.account-verified');
    }
}

