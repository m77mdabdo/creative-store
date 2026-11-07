<?php

namespace App\Services\Staff;

use App\Models\User;
use App\Notifications\StaffInvitationNotification;
use App\Notifications\StaffMemberAddedNotification;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Welcomes new team members and lets the rest of the management team know.
 */
class StaffInvitations
{
    private const ROLES = ['owner', 'admin', 'employee'];

    /**
     * The dashboard role a user holds, highest first, or null for customers.
     */
    public function roleOf(User $user): ?string
    {
        $user->load('roles:id,name');

        foreach (self::ROLES as $role) {
            if ($user->roles->contains('name', $role)) {
                return $role;
            }
        }

        return null;
    }

    /**
     * Send the invitation. Returns false when the user is not staff or the
     * message could not be delivered (the account itself is unaffected).
     */
    public function invite(User $member, ?User $invitedBy = null, bool $announce = true): bool
    {
        $role = $this->roleOf($member);

        if (! $role) {
            return false;
        }

        try {
            $member->notify(new StaffInvitationNotification($role, $invitedBy));
        } catch (Throwable $e) {
            report($e);

            return false;
        } finally {
            if ($announce) {
                $this->announce($member, $role, $invitedBy);
            }
        }

        return true;
    }

    private function announce(User $member, string $role, ?User $addedBy): void
    {
        $managers = User::query()
            ->whereKeyNot($member->getKey())
            ->where('is_active', true)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['owner', 'admin']))
            ->get(['id', 'name', 'email']);

        try {
            Notification::send($managers, new StaffMemberAddedNotification($member, $role, $addedBy));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
