<?php

namespace App\Services\Dashboard;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Auth\AccountSessions;
use App\Services\Security\ProtectOwnerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserManagementService
{
    public function __construct(
        protected ProtectOwnerService $protectOwnerService
    ) {}

    public function create(array $data, User $actor): User
    {
        return DB::transaction(function () use ($data, $actor) {
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make($data['password']),
                'phone'             => $data['phone'] ?? null,
                'email_verified_at' => !empty($data['mark_email_verified']) ? now() : null,
                'is_active'         => (bool) ($data['is_active'] ?? true),
                'created_by'        => $actor->id,
            ]);

            $this->syncRoles($user, $data['roles'] ?? [], $actor);
            $this->syncDirectPermissions(
                $user,
                $data['direct_permissions'] ?? [],
                $data['denied_permissions'] ?? [],
                $actor
            );

            return $user->load(['roles', 'permissions']);
        });
    }

    public function update(User $target, array $data, User $actor): User
    {
        return DB::transaction(function () use ($target, $data, $actor) {
            $this->protectOwnerService->ensureUserCanBeUpdated($actor, $target);

            $target->update([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'] ?? null,
                'is_active'         => (bool) ($data['is_active'] ?? true),
                'email_verified_at' => !empty($data['mark_email_verified'])
                    ? ($target->email_verified_at ?? now())
                    : null,
            ]);

            if (!empty($data['password'])) {
                $target->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            $this->syncRoles($target, $data['roles'] ?? [], $actor);
            $this->syncDirectPermissions(
                $target,
                $data['direct_permissions'] ?? [],
                $data['denied_permissions'] ?? [],
                $actor
            );

            return $target->load(['roles', 'permissions']);
        });
    }

    public function delete(User $target, User $actor): void
    {
        DB::transaction(function () use ($target, $actor) {
            $this->protectOwnerService->ensureUserCanBeUpdated($actor, $target);
            $this->protectOwnerService->ensureUserCanBeDeleted($target);

            $target->delete();
        });
    }

    public function forceDelete(User $target, User $actor): void
    {
        DB::transaction(function () use ($target, $actor) {
            $this->protectOwnerService->ensureUserCanBeUpdated($actor, $target);
            $this->protectOwnerService->ensureUserCanBeDeleted($target);

            $target->roles()->detach();
            $target->permissions()->detach();
            $target->forceDelete();
        });
    }

    public function restore(User $target, User $actor): void
    {
        DB::transaction(function () use ($target, $actor) {
            $this->protectOwnerService->ensureUserCanBeUpdated($actor, $target);
            $target->restore();
        });
    }

    public function bulkAction(array $ids, string $action, User $actor): void
    {
        $users = User::withTrashed()->whereIn('id', $ids)->get();

        DB::transaction(function () use ($users, $action, $actor) {
            foreach ($users as $user) {
                match ($action) {
                    'activate'     => $this->setActive($user, true, $actor),
                    'deactivate'   => $this->setActive($user, false, $actor),
                    'delete'       => $this->delete($user, $actor),
                    'restore'      => $this->restore($user, $actor),
                    'force_delete' => $this->forceDelete($user, $actor),
                    default        => throw ValidationException::withMessages([
                        'action' => ['Invalid bulk action selected.'],
                    ]),
                };
            }
        });
    }

    public function setActive(User $target, bool $isActive, User $actor): void
    {
        $this->protectOwnerService->ensureUserCanBeUpdated($actor, $target);

        if ($target->isOwner() && !$isActive) {
            throw ValidationException::withMessages([
                'user' => ['Owner accounts cannot be deactivated.'],
            ]);
        }

        $target->update([
            'is_active' => $isActive,
        ]);

        if (! $isActive) {
            app(AccountSessions::class)->revokeOthers($target);
        }
    }

    protected function syncRoles(User $user, array $roleNames, User $actor): void
    {
        $roleNames = collect($roleNames)
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!$actor->isOwner()) {
            $roleNames = array_values(array_diff($roleNames, ['owner']));
        }

        if ($user->isOwner()) {
            $currentOwnerCount = User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'owner'))
                ->count();

            if (!in_array('owner', $roleNames, true) && $currentOwnerCount <= 1) {
                throw ValidationException::withMessages([
                    'roles' => ['You cannot remove the last owner role.'],
                ]);
            }
        }

        $roleIds = Role::query()
            ->whereIn('name', $roleNames)
            ->pluck('id')
            ->all();

        $user->roles()->sync($roleIds);
    }

    protected function syncDirectPermissions(
        User $user,
        array $allowedCodes,
        array $deniedCodes,
        User $actor
    ): void {
        $allowedCodes = collect($allowedCodes)
            ->map(fn ($code) => trim((string) $code))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $deniedCodes = collect($deniedCodes)
            ->map(fn ($code) => trim((string) $code))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $conflicts = array_intersect($allowedCodes, $deniedCodes);

        if (!empty($conflicts)) {
            throw ValidationException::withMessages([
                'permissions' => ['The same permission cannot be allowed and denied together.'],
            ]);
        }

        if (!$actor->isOwner()) {
            $deniedCodes = array_values(array_filter(
                $deniedCodes,
                fn ($code) => $code !== 'users.delete'
            ));
        }

        $permissionMap = Permission::query()
            ->whereIn('code', array_merge($allowedCodes, $deniedCodes))
            ->pluck('id', 'code');

        $syncData = [];

        foreach ($allowedCodes as $code) {
            if (isset($permissionMap[$code])) {
                $syncData[$permissionMap[$code]] = ['is_denied' => false];
            }
        }

        foreach ($deniedCodes as $code) {
            if (isset($permissionMap[$code])) {
                $syncData[$permissionMap[$code]] = ['is_denied' => true];
            }
        }

        $user->permissions()->sync($syncData);
    }
}
