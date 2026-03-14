<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $contactMessage)
    {
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
            ->greeting('Hello Admin,')
            ->line('A new contact message has been received.')
            ->line('Name: ' . $this->contactMessage->name)
            ->line('Email: ' . $this->contactMessage->email)
            ->line('Phone: ' . ($this->contactMessage->phone ?: 'N/A'))
            ->line('Subject: ' . ($this->contactMessage->subject ?: 'No Subject'))
            ->line('Message:')
            ->line($this->contactMessage->message)
            ->action('View Message', route('dashboard.contact-messages.show', $this->contactMessage->id))
            ->line('Thank you.');
    }
}
