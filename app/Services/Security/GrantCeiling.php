<?php

namespace App\Services\Security;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Staff can only hand out authority they hold themselves.
 *
 * Owners are unrestricted. Anyone else managing users or roles:
 * - cannot change the roles or direct permissions on their own account;
 * - can give a role only if they hold every permission in it;
 * - can allow a permission, or lift a deny on one, only if they hold it.
 * Removing roles and adding denies is always allowed.
 */
class GrantCeiling
{
    /** @var array<string, bool> */
    private array $held = [];

    /**
     * @param  list<int>  $roleIds
     * @param  list<string>  $allowCodes
     * @param  list<string>  $denyCodes
     */
    public function assertUserAssignment(User $actor, ?User $target, array $roleIds, array $allowCodes, array $denyCodes): void
    {
        if ($actor->isOwner()) {
            return;
        }

        $roleIds = collect($roleIds)->map(fn ($id) => (int) $id)->filter()->unique()->sort()->values();
        $allow = collect($allowCodes)->map(fn ($c) => trim((string) $c))->filter()->unique()->sort()->values();
        $deny = collect($denyCodes)->map(fn ($c) => trim((string) $c))->filter()->unique()->sort()->values();

        $currentRoles = $target ? $target->roles()->pluck('roles.id')->map(fn ($id) => (int) $id)->sort()->values() : collect();
        $currentAllow = $target ? $target->directAllowedPermissions()->pluck('code')->sort()->values() : collect();
        $currentDeny = $target ? $target->directDeniedPermissions()->pluck('code')->sort()->values() : collect();

        if ($target && $target->is($actor)
            && ($roleIds->all() !== $currentRoles->all() || $allow->all() !== $currentAllow->all() || $deny->all() !== $currentDeny->all())) {
            throw ValidationException::withMessages([
                'roles' => [__('staff.ceiling.own_account')],
            ]);
        }

        $newRoles = $roleIds->diff($currentRoles);
        $tooBroad = Role::query()->with('permissions:id,code')->whereIn('id', $newRoles)->get()
            ->filter(fn (Role $role) => $this->missing($actor, $role->permissions->pluck('code'))->isNotEmpty());

        if ($tooBroad->isNotEmpty()) {
            throw ValidationException::withMessages([
                'roles' => [__('staff.ceiling.role', ['role' => $tooBroad->pluck('name')->implode(', ')])],
            ]);
        }

        $granted = $allow->diff($currentAllow)->merge($currentDeny->diff($deny));

        if (($missing = $this->missing($actor, $granted))->isNotEmpty()) {
            throw ValidationException::withMessages([
                'direct_permissions' => [__('staff.ceiling.permission', ['permissions' => $missing->implode(', ')])],
            ]);
        }
    }

    /**
     * @param  iterable<string>  $codes
     */
    public function assertRolePermissions(User $actor, iterable $codes): void
    {
        if ($actor->isOwner()) {
            return;
        }

        if (($missing = $this->missing($actor, collect($codes)))->isNotEmpty()) {
            throw ValidationException::withMessages([
                'permissions' => [__('staff.ceiling.permission', ['permissions' => $missing->implode(', ')])],
            ]);
        }
    }

    private function missing(User $actor, Collection $codes): Collection
    {
        return $codes->unique()->reject(function (string $code) use ($actor) {
            return $this->held[$actor->getKey() . '|' . $code] ??= $actor->hasPermission($code);
        })->values();
    }
}
