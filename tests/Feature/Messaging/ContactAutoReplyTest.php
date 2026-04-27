<?php

namespace Tests\Feature\Messaging;

use App\Mail\ContactAutoReplyMail;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactAutoReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_auto_reply_renders_without_repeating_the_message(): void
    {
        $message = ContactMessage::create([
            'name'    => 'Visitor',
            'email'   => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'Click http://spam.test now',
        ]);

        $mail = new ContactAutoReplyMail($message);
        $html = $mail->render();

        $mail->assertHasSubject(__('site.contact_reply_subject'));
        $this->assertStringContainsString('Visitor', $html);
        $this->assertStringNotContainsString('spam.test', $html);
    }
}
