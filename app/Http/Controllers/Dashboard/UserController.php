<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\User\StoreUserRequest;
use App\Http\Requests\Dashboard\User\UpdateUserRequest;
use App\Http\Requests\Dashboard\User\UserBulkActionRequest;
use App\Http\Requests\Dashboard\User\UserIndexRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(UserIndexRequest $request): View
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $query = User::query()
            ->with([
                'roles:id,name',
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

        $query->when(!empty($filters['q']), function ($q) use ($filters) {
            $search = trim((string) $filters['q']);

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });

        $query->when(!empty($filters['role']), function ($q) use ($filters) {
            $role = $filters['role'];

            $q->whereHas('roles', function ($roleQuery) use ($role) {
                $roleQuery->where('name', $role);
            });
        });

        $query->when(isset($filters['email_verified']) && $filters['email_verified'] !== '', function ($q) use ($filters) {
            if ((string) $filters['email_verified'] === '1') {
                $q->whereNotNull('email_verified_at');
            } elseif ((string) $filters['email_verified'] === '0') {
                $q->whereNull('email_verified_at');
            }
        });

        $query->when(isset($filters['has_phone']) && $filters['has_phone'] !== '', function ($q) use ($filters) {
            if ((string) $filters['has_phone'] === '1') {
                $q->whereNotNull('phone')->where('phone', '!=', '');
            } elseif ((string) $filters['has_phone'] === '0') {
                $q->where(function ($sub) {
                    $sub->whereNull('phone')->orWhere('phone', '');
                });
            }
        });

        $sort = $filters['sort'] ?? 'latest';

        match ($sort) {
            'name_asc'       => $query->orderBy('name'),
            'name_desc'      => $query->orderByDesc('name'),
            'email_asc'      => $query->orderBy('email'),
            'email_desc'     => $query->orderByDesc('email'),
            'oldest'         => $query->oldest(),
            default          => $query->latest(),
        };

        $users = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total_users'         => User::count(),
            'verified_users'      => User::whereNotNull('email_verified_at')->count(),
            'unverified_users'    => User::whereNull('email_verified_at')->count(),
            'users_with_phone'    => User::whereNotNull('phone')->where('phone', '!=', '')->count(),
        ];

        $roles = DB::table('roles')
            ->select('name')
            ->orderBy('name')
            ->pluck('name');

        return view('dashboard.users.index', compact('users', 'filters', 'stats', 'roles'));
    }

    public function create(): View
    {
        $roles = DB::table('roles')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('dashboard.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'] ?? null,
                'password'          => Hash::make($data['password']),
                'email_verified_at' => !empty($data['email_verified']) ? now() : null,
            ]);

            if (!empty($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }
        });

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user): View
    {
        $user->load([
            'roles:id,name',
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
        $user->load('roles:id,name');

        $roles = DB::table('roles')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('dashboard.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $user) {
            $updateData = [
                'name'              => $data['name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'] ?? null,
                'email_verified_at' => !empty($data['email_verified']) ? ($user->email_verified_at ?? now()) : null,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            if (array_key_exists('roles', $data)) {
                $user->roles()->sync($data['roles'] ?? []);
            }
        });

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('dashboard.users.index')
            ->with('success', 'User moved to trash successfully.');
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
        $record->restore();

        return redirect()
            ->route('dashboard.users.trash')
            ->with('success', 'User restored successfully.');
    }

    public function forceDelete(int $user): RedirectResponse
    {
        $record = User::onlyTrashed()->findOrFail($user);

        if (auth()->id() === $record->id) {
            return back()->with('error', 'You cannot permanently delete your own account.');
        }

        $record->forceDelete();

        return redirect()
            ->route('dashboard.users.trash')
            ->with('success', 'User permanently deleted.');
    }

    public function bulkAction(UserBulkActionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $users = User::query()
            ->whereIn('id', $data['ids'])
            ->get();

        if ($users->isEmpty()) {
            return back()->with('error', 'No users selected.');
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
                    $ids = $users->pluck('id')->reject(fn ($id) => $id === auth()->id())->values();
                    if ($ids->isNotEmpty()) {
                        User::whereIn('id', $ids)->delete();
                    }
                    break;
            }
        });

        return back()->with('success', 'Bulk action applied successfully.');
    }
}
