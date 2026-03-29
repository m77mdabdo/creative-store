<?php

namespace App\Jobs;

use App\Mail\ContactAutoReplyMail;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendContactNotificationsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $contactMessageId)
    {
        $this->onQueue('contact');
    }

    public function handle(): void
    {
        $contactMessage = ContactMessage::query()->find($this->contactMessageId);

        if (! $contactMessage) {
            return;
        }

        $admins = User::query()
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['owner', 'admin']);
            })
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewContactMessageNotification($contactMessage));
        }

        Mail::to($contactMessage->email)->queue(new ContactAutoReplyMail($contactMessage));
    }
}
