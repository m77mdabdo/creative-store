<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Role\DeleteRoleRequest;
use App\Http\Requests\Dashboard\Role\RoleIndexRequest;
use App\Http\Requests\Dashboard\Role\StoreRoleRequest;
use App\Http\Requests\Dashboard\Role\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(RoleIndexRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $query = Role::query()
            ->withCount(['users', 'permissions']);

        if (!empty($filters['q'])) {
            $search = trim((string) $filters['q']);

            $query->where('name', 'like', "%{$search}%");
        }

        $sort = $filters['sort'] ?? 'name_asc';

        match ($sort) {
            'name_desc'         => $query->orderByDesc('name'),
            'users_count_desc'  => $query->orderByDesc('users_count')->orderBy('name'),
            'users_count_asc'   => $query->orderBy('users_count')->orderBy('name'),
            'latest'            => $query->latest(),
            'oldest'            => $query->oldest(),
            default             => $query->orderBy('name'),
        };

        $roles = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total_roles' => Role::count(),
            'system_roles' => Role::whereIn('name', ['owner', 'admin', 'employee', 'customer'])->count(),
            'custom_roles' => Role::whereNotIn('name', ['owner', 'admin', 'employee', 'customer'])->count(),
        ];

        return view('dashboard.roles.index', compact('roles', 'filters', 'stats'));
    }

    public function create(): View
    {
        $permissionsGrouped = Permission::query()
            ->select('id', 'code', 'name', 'group')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permission) => $permission->group ?: 'general');

        return view('dashboard.roles.create', compact('permissionsGrouped'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => Str::lower(trim($data['name'])),
            ]);

            $permissionIds = Permission::query()
                ->whereIn('code', $data['permissions'] ?? [])
                ->pluck('id')
                ->all();

            $role->permissions()->sync($permissionIds);
        });

        return redirect()
            ->route('dashboard.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role): View
    {
        $role->load([
            'permissions:id,code,name,group',
            'users:id,name,email',
        ])->loadCount(['users', 'permissions']);

        $permissionsGrouped = $role->permissions
            ->sortBy(['group', 'name'])
            ->groupBy(fn ($permission) => $permission->group ?: 'general');

        return view('dashboard.roles.show', compact('role', 'permissionsGrouped'));
    }

    public function edit(Role $role): View
    {
        $role->load('permissions:id,code,name,group');

        $permissionsGrouped = Permission::query()
            ->select('id', 'code', 'name', 'group')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permission) => $permission->group ?: 'general');

        return view('dashboard.roles.edit', compact('role', 'permissionsGrouped'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $data = $request->validated();

        $this->ensureSystemRoleCanBeManaged($role, $request->user());

        DB::transaction(function () use ($data, $role) {
            $newName = Str::lower(trim($data['name']));

            if ($this->isProtectedSystemRole($role->name) && $role->name !== $newName) {
                throw ValidationException::withMessages([
                    'name' => ['System role name cannot be changed.'],
                ]);
            }

            $role->update([
                'name' => $newName,
            ]);

            $permissionIds = Permission::query()
                ->whereIn('code', $data['permissions'] ?? [])
                ->pluck('id')
                ->all();

            $role->permissions()->sync($permissionIds);
        });

        return redirect()
            ->route('dashboard.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(DeleteRoleRequest $request, Role $role): RedirectResponse
    {
        $this->ensureRoleCanBeDeleted($role, $request->user());

        DB::transaction(function () use ($role) {
            $role->permissions()->detach();
            $role->users()->detach();
            $role->delete();
        });

        return redirect()
            ->route('dashboard.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    protected function isProtectedSystemRole(string $roleName): bool
    {
        return in_array($roleName, ['owner', 'admin', 'employee', 'customer'], true);
    }

    protected function ensureSystemRoleCanBeManaged(Role $role, $actor): void
    {
        if (!$this->isProtectedSystemRole($role->name)) {
            return;
        }

        if (!$actor->hasRole('owner')) {
            throw ValidationException::withMessages([
                'role' => ['Only owner can manage system roles.'],
            ]);
        }
    }

    protected function ensureRoleCanBeDeleted(Role $role, $actor): void
    {
        if ($this->isProtectedSystemRole($role->name)) {
            throw ValidationException::withMessages([
                'role' => ['System roles cannot be deleted.'],
            ]);
        }

        if (!$actor->hasRole('owner') && $role->users()->exists()) {
            throw ValidationException::withMessages([
                'role' => ['Only owner can delete a role that is assigned to users.'],
            ]);
        }
    }
}
