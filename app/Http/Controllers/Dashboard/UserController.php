<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\User\StoreUserRequest;
use App\Http\Requests\Dashboard\User\UpdateUserRequest;
use App\Http\Requests\Dashboard\User\UserBulkActionRequest;
use App\Http\Requests\Dashboard\User\UserIndexRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Security\ProtectOwnerService;
use App\Services\Staff\StaffInvitations;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected ProtectOwnerService $protectOwnerService,
        protected StaffInvitations $invitations,
    ) {}

    public function index(UserIndexRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $query = User::query()
            ->with([
                'roles:id,name',
                'permissions:id,code,name',
                'addresses:id,user_id,city,is_default',
                'wishlist:id,user_id',
                'carts:id,user_id,session_id,updated_at',
            ])
            ->withCount([
                'addresses',
                'orders',
                'reviews',
                'carts',
            ]);

        if (!empty($filters['q'])) {
            $search = trim((string) $filters['q']);

            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['role'])) {
            $role = $filters['role'];

            $query->whereHas('roles', function ($roleQuery) use ($role) {
                $roleQuery->where('name', $role);
            });
        }

        if (!empty($filters['type'])) {
            $type = $filters['type'];

            $query->whereHas('roles', function ($roleQuery) use ($type) {
                $roleQuery->where('name', $type);
            });
        }

        if (isset($filters['email_verified']) && $filters['email_verified'] !== '') {
            if ((string) $filters['email_verified'] === '1') {
                $query->whereNotNull('email_verified_at');
            } elseif ((string) $filters['email_verified'] === '0') {
                $query->whereNull('email_verified_at');
            }
        }

        if (isset($filters['has_phone']) && $filters['has_phone'] !== '') {
            if ((string) $filters['has_phone'] === '1') {
                $query->whereNotNull('phone')->where('phone', '!=', '');
            } elseif ((string) $filters['has_phone'] === '0') {
                $query->where(function ($sub) {
                    $sub->whereNull('phone')->orWhere('phone', '');
                });
            }
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (int) $filters['is_active'] === 1);
        }

        $sort = $filters['sort'] ?? 'latest';

        match ($sort) {
            'name_asc'   => $query->orderBy('name'),
            'name_desc'  => $query->orderByDesc('name'),
            'email_asc'  => $query->orderBy('email'),
            'email_desc' => $query->orderByDesc('email'),
            'oldest'     => $query->oldest(),
            default      => $query->latest(),
        };

        $users = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total_users'      => User::count(),
            'verified_users'   => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
            'users_with_phone' => User::whereNotNull('phone')->where('phone', '!=', '')->count(),
            'owners'           => User::whereHas('roles', fn ($q) => $q->where('name', 'owner'))->count(),
            'admins'           => User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count(),
            'employees'        => User::whereHas('roles', fn ($q) => $q->where('name', 'employee'))->count(),
            'customers'        => User::whereHas('roles', fn ($q) => $q->where('name', 'customer'))->count(),
        ];

        $roles = Role::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('dashboard.users.index', [
            'users' => $users,
            'filters' => $filters,
            'stats' => $stats,
            'roles' => $roles,
        ]);
    }

    public function create(): View
    {
        $roles = Role::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $permissionsGrouped = Permission::query()
            ->select('id', 'code', 'name', 'group')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permission) => $permission->group ?: 'general');

        return view('dashboard.users.create', compact('roles', 'permissionsGrouped'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data, $request) {
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'] ?? null,
                'password'          => Hash::make($data['password']),
                'email_verified_at' => !empty($data['email_verified']) ? now() : null,
                'is_active'         => (bool) ($data['is_active'] ?? true),
                'created_by'        => $request->user()->id,
            ]);

            $this->syncRoles($user, $data['roles'] ?? [], $request->user());
            $this->syncDirectPermissions(
                $user,
                $data['direct_permissions'] ?? [],
                $data['denied_permissions'] ?? []
            );

            return $user;
        });

        $redirect = redirect()
            ->route('dashboard.users.index')
            ->with('success', __('User created successfully.'));

        if ($this->invitations->roleOf($user) === null) {
            return $redirect;
        }

        return $this->invitations->invite($user, $request->user())
            ? $redirect->with('info', __('staff.invite.sent', ['email' => $user->email]))
            : $redirect->with('warning', __('staff.invite.send_failed'));
    }

    /**
     * Send a team member their sign-in invitation again (a fresh link).
     */
    public function resendInvitation(Request $request, User $user): RedirectResponse
    {
        if ($this->invitations->roleOf($user) === null) {
            return back()->with('error', __('staff.invite.not_staff'));
        }

        return $this->invitations->invite($user, $request->user(), announce: false)
            ? back()->with('success', __('staff.invite.sent', ['email' => $user->email]))
            : back()->with('error', __('staff.invite.send_failed'));
    }

    public function show(User $user): View
    {
        $user->load([
            'roles:id,name',
            'permissions:id,code,name',
            'addresses',
            'wishlist.items.product',
            'carts.items.product',
            'orders' => function ($q) {
                $q->latest()->take(10);
            },
            'reviews' => function ($q) {
                $q->latest()->take(10);
            },
        ])->loadCount([
            'addresses',
            'orders',
            'reviews',
            'carts',
        ]);

        return view('dashboard.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $user->load([
            'roles:id,name',
            'permissions:id,code,name',
        ]);

        $roles = Role::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $permissionsGrouped = Permission::query()
            ->select('id', 'code', 'name', 'group')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permission) => $permission->group ?: 'general');

        return view('dashboard.users.edit', compact('user', 'roles', 'permissionsGrouped'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $user, $request) {
            $this->protectOwnerService->ensureUserCanBeUpdated($request->user(), $user);

            $updateData = [
                'name'              => $data['name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'] ?? null,
                'is_active'         => (bool) ($data['is_active'] ?? true),
                'email_verified_at' => !empty($data['email_verified'])
                    ? ($user->email_verified_at ?? now())
                    : null,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            if ($user->isOwner() && isset($updateData['is_active']) && !$updateData['is_active']) {
                throw ValidationException::withMessages([
                    'is_active' => ['Owner accounts cannot be deactivated.'],
                ]);
            }

            $user->update($updateData);

            $this->syncRoles($user, $data['roles'] ?? [], $request->user());
            $this->syncDirectPermissions(
                $user,
                $data['direct_permissions'] ?? [],
                $data['denied_permissions'] ?? []
            );
        });

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', __('User updated successfully.'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', __('You cannot delete your own account.'));
        }

        try {
            $this->protectOwnerService->ensureUserCanBeUpdated(auth()->user(), $user);
            $this->protectOwnerService->ensureUserCanBeDeleted($user);
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }

        $user->delete();

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', __('User moved to trash successfully.'));
    }

    public function trash(UserIndexRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $users = User::onlyTrashed()
            ->with('roles:id,name')
            ->when(!empty($filters['q']), function ($q) use ($filters) {
                $search = trim((string) $filters['q']);

                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest('deleted_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('dashboard.users.trash', compact('users', 'filters'));
    }

    public function restore(int $user): RedirectResponse
    {
        $record = User::onlyTrashed()->findOrFail($user);

        try {
            $this->protectOwnerService->ensureUserCanBeUpdated(auth()->user(), $record);
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }

        $record->restore();

        return redirect()
            ->route('dashboard.users.trash')
            ->with('success', __('User restored successfully.'));
    }

    public function forceDelete(int $user): RedirectResponse
    {
        $record = User::onlyTrashed()->with(['roles', 'permissions'])->findOrFail($user);

        if (auth()->id() === $record->id) {
            return back()->with('error', __('You cannot permanently delete your own account.'));
        }

        try {
            $this->protectOwnerService->ensureUserCanBeUpdated(auth()->user(), $record);
            $this->protectOwnerService->ensureUserCanBeDeleted($record);
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }

        DB::transaction(function () use ($record) {
            $record->roles()->detach();
            $record->permissions()->detach();
            $record->forceDelete();
        });

        return redirect()
            ->route('dashboard.users.trash')
            ->with('success', __('User permanently deleted.'));
    }

    public function bulkAction(UserBulkActionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $users = User::query()
            ->whereIn('id', $data['ids'])
            ->get();

        if ($users->isEmpty()) {
            return back()->with('error', __('No users selected.'));
        }

        DB::transaction(function () use ($users, $data) {
            switch ($data['action']) {
                case 'verify':
                    User::whereIn('id', $users->pluck('id'))
                        ->update(['email_verified_at' => now()]);
                    break;

                case 'unverify':
                    User::whereIn('id', $users->pluck('id'))
                        ->update(['email_verified_at' => null]);
                    break;

                case 'delete':
                    foreach ($users as $user) {
                        if (auth()->id() === $user->id) {
                            continue;
                        }

                        $this->protectOwnerService->ensureUserCanBeUpdated(auth()->user(), $user);
                        $this->protectOwnerService->ensureUserCanBeDeleted($user);
                        $user->delete();
                    }
                    break;
            }
        });

        return back()->with('success', __('Bulk action applied successfully.'));
    }

    protected function syncRoles(User $user, array $roleIds, User $actor): void
    {
        $roleIds = collect($roleIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $roleNames = Role::query()
            ->whereIn('id', $roleIds)
            ->pluck('name')
            ->all();

        if (!$actor->hasRole('owner')) {
            $ownerRoleId = Role::query()->where('name', 'owner')->value('id');
            $roleIds = $roleIds->reject(fn ($id) => $id === (int) $ownerRoleId)->values();
            $roleNames = array_values(array_diff($roleNames, ['owner']));
        }

        if ($user->hasRole('owner')) {
            $ownersCount = User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'owner'))
                ->count();

            if (!in_array('owner', $roleNames, true) && $ownersCount <= 1) {
                throw ValidationException::withMessages([
                    'roles' => ['You cannot remove the last owner role.'],
                ]);
            }
        }

        $user->roles()->sync($roleIds->all());
    }

    protected function syncDirectPermissions(User $user, array $allowCodes, array $denyCodes): void
    {
        $allowCodes = collect($allowCodes)
            ->map(fn ($code) => trim((string) $code))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $denyCodes = collect($denyCodes)
            ->map(fn ($code) => trim((string) $code))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $conflicts = array_intersect($allowCodes, $denyCodes);

        if (!empty($conflicts)) {
            throw ValidationException::withMessages([
                'direct_permissions' => ['The same permission cannot be allowed and denied together.'],
            ]);
        }

        $permissions = Permission::query()
            ->whereIn('code', array_merge($allowCodes, $denyCodes))
            ->pluck('id', 'code');

        $syncData = [];

        foreach ($allowCodes as $code) {
            if (isset($permissions[$code])) {
                $syncData[$permissions[$code]] = ['is_denied' => false];
            }
        }

        foreach ($denyCodes as $code) {
            if (isset($permissions[$code])) {
                $syncData[$permissions[$code]] = ['is_denied' => true];
            }
        }

        $user->permissions()->sync($syncData);
    }
}
