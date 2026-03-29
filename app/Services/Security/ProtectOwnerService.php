<?php

namespace App\Services\Security;

use App\Models\User;
use LogicException;

class ProtectOwnerService
{
    public function ensureUserCanBeDeleted(User $target): void
    {
        if ($target->hasRole('owner')) {
            throw new LogicException('Owner accounts cannot be deleted.');
        }
    }

    public function ensureRoleCanBeRemoved(User $target, string $roleName): void
    {
        if ($roleName !== 'owner') {
            return;
        }

        if (! $target->hasRole('owner')) {
            return;
        }

        $ownersCount = User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'owner'))
            ->count();

        if ($ownersCount <= 1) {
            throw new LogicException('You cannot remove the last owner role.');
        }
    }

    public function ensureUserCanBeUpdated(User $actor, User $target): void
    {
        if (! $target->hasRole('owner')) {
            return;
        }

        if ($actor->id === $target->id && $actor->hasRole('owner')) {
            return;
        }

        if (! $actor->hasRole('owner')) {
            throw new LogicException('Only an owner can manage another owner account.');
        }
    }
}
