<?php

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Channels\TextMessageChannel;
use App\Services\Messaging\TextMessage;
use App\Support\Auth\StaffWelcomeLink;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Welcome message for a new team member: their role, the dashboard sign-in
 * address and a single-use link to choose their own password. Sent by email
 * and SMS/WhatsApp, and kept in their dashboard notifications.
 */
class StaffInvitationNotification extends Notification
{
    public function __construct(
        public readonly string $role,
        public readonly ?User $invitedBy = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return array_values(array_filter([
            'mail',
            'database',
            $notifiable->phone ? TextMessageChannel::class : null,
        ]));
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('staff.invite.subject', ['store' => config('app.name')]))
            ->view('emails.staff-invitation', [
                'user'       => $notifiable,
                'role'       => __('staff.roles.' . $this->role),
                'invitedBy'  => $this->invitedBy?->name,
                'welcomeUrl' => StaffWelcomeLink::for($notifiable),
                'loginUrl'   => route('login'),
                'validDays'  => StaffWelcomeLink::VALID_DAYS,
            ]);
    }

    public function toText(object $notifiable): TextMessage
    {
        return new TextMessage(__('staff.invite.text', [
            'name' => $notifiable->name,
            'role' => __('staff.roles.' . $this->role),
            'link' => StaffWelcomeLink::for($notifiable),
        ]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'staff_invited',
            'role'       => $this->role,
            'invited_by' => $this->invitedBy?->name,
        ];
    }
}
