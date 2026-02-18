<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyAccountMail extends Mailable
{
    use SerializesModels;

    public int $otp;
    public string $verifyUrl;

    public function __construct(int $otp, string $email)
    {
        $this->otp = $otp;
        $this->verifyUrl = route('email.verify', ['email' => $email]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verify Account');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.verify-account');
        
    }
}



