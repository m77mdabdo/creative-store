<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

/**
 * Tells owners and admins in their dashboard bell that someone joined the team.
 */
class StaffMemberAddedNotification extends Notification
{
    public function __construct(
        public readonly User $member,
        public readonly string $role,
        public readonly ?User $addedBy = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'     => 'staff_added',
            'user_id'  => $this->member->id,
            'name'     => $this->member->name,
            'role'     => $this->role,
            'added_by' => $this->addedBy?->name,
        ];
    }
}
