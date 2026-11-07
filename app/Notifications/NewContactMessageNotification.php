<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use App\Notifications\Concerns\WritesBellImmediately;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use WritesBellImmediately;

    public function __construct(public ContactMessage $contactMessage)
    {
        $this->onQueue('notifications');
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message_id' => $this->contactMessage->id,
            'name' => $this->contactMessage->name,
            'email' => $this->contactMessage->email,
            'phone' => $this->contactMessage->phone,
            'subject' => $this->contactMessage->subject,
            'message' => $this->contactMessage->message,
            'text' => 'New contact message from ' . $this->contactMessage->name,
            'url' => route('dashboard.contact-messages.show', $this->contactMessage->id),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Contact Message Received')
            ->greeting('Hello,')
            ->line('A new contact message has been received from your website.')
            ->line('Name: ' . $this->contactMessage->name)
            ->line('Email: ' . $this->contactMessage->email)
            ->line('Phone: ' . ($this->contactMessage->phone ?: 'N/A'))
            ->line('Subject: ' . ($this->contactMessage->subject ?: 'No Subject'))
            ->line('Message:')
            ->line($this->contactMessage->message)
            ->action('View Message', route('dashboard.contact-messages.show', $this->contactMessage->id));
    }
}
