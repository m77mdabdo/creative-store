<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->hasPermission('permissions.view'), 403);

        $search = trim((string) $request->input('q', ''));

        $query = Permission::query()
            ->withCount(['roles', 'users'])
            ->orderBy('group')
            ->orderBy('name');

        if ($search !== '') {
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%");
            });
        }

        $permissions = $query->get();
        $groupedPermissions = $permissions->groupBy(fn ($permission) => $permission->group ?: 'general');

        $stats = [
            'total_permissions' => $permissions->count(),
            'groups_count' => $groupedPermissions->count(),
        ];

        return view('dashboard.permissions.index', compact('groupedPermissions', 'stats', 'search'));
    }

    public function show(Request $request, Permission $permission): View
    {
        abort_unless($request->user()?->hasPermission('permissions.view'), 403);

        $permission->load([
            'roles:id,name',
            'users:id,name,email',
        ]);

        return view('dashboard.permissions.show', compact('permission'));
    }
}
