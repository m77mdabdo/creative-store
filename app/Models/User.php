<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Auditable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'otp',
        'email_verified_at',
        'phone',
        'avatar_path',
        'is_active',
        'created_by',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'deleted_at'        => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
        ];
    }

    /**
     * The uploaded profile photo, or null to show initials instead.
     */
    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? media_url($this->avatar_path) : null;
    }

    /**
     * One or two letters for the avatar when there is no photo ("Mohamed Abdo" → "MA").
     */
    public function initials(): string
    {
        $words = preg_split('/\s+/u', trim((string) $this->name), -1, PREG_SPLIT_NO_EMPTY) ?: ['?'];
        $letters = mb_substr($words[0], 0, 1) . (count($words) > 1 ? mb_substr(end($words), 0, 1) : '');

        return mb_strtoupper($letters);
    }

    /* =========================
     * ACL (Roles / Permissions)
     * ========================= */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->using(RoleUser::class);
    }

    public function rolesWithPermissions()
    {
        return $this->roles()->with('permissions');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->using(UserPermission::class)
            ->withPivot('is_denied')
            ->withTimestamps();
    }

    public function directAllowedPermissions()
    {
        return $this->permissions()->wherePivot('is_denied', false);
    }

    public function directDeniedPermissions()
    {
        return $this->permissions()->wherePivot('is_denied', true);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function hasRole(string $name): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains(fn ($role) => $role->name === $name);
        }

        return $this->roles()->where('name', $name)->exists();
    }

    public function hasAnyRole(array $names): bool
    {
        $names = array_filter(array_map('trim', $names));

        if (empty($names)) {
            return false;
        }

        if ($this->relationLoaded('roles')) {
            return $this->roles->whereIn('name', $names)->isNotEmpty();
        }

        return $this->roles()->whereIn('name', $names)->exists();
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isEmployee(): bool
    {
        return $this->hasRole('employee');
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    public function hasDirectPermission(string $code): bool
    {
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains(function ($permission) use ($code) {
                return $permission->code === $code
                    && ! (bool) $permission->pivot->is_denied;
            });
        }

        return $this->permissions()
            ->where('code', $code)
            ->wherePivot('is_denied', false)
            ->exists();
    }

    public function hasDeniedPermission(string $code): bool
    {
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains(function ($permission) use ($code) {
                return $permission->code === $code
                    && (bool) $permission->pivot->is_denied;
            });
        }

        return $this->permissions()
            ->where('code', $code)
            ->wherePivot('is_denied', true)
            ->exists();
    }

    public function hasPermissionThroughRole(string $code): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn ($q) => $q->where('code', $code))
            ->exists();
    }

    public function hasPermission(string $code): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        if ($this->hasDeniedPermission($code)) {
            return false;
        }

        if ($this->hasDirectPermission($code)) {
            return true;
        }

        return $this->hasPermissionThroughRole($code);
    }

    public function hasAnyPermission(array $codes): bool
    {
        foreach ($codes as $code) {
            if ($this->hasPermission(trim((string) $code))) {
                return true;
            }
        }

        return false;
    }

    public function assignRole(string $roleName): void
    {
        $role = Role::firstOrCreate(['name' => trim($roleName)]);
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function syncRoles(array $roleNames): void
    {
        $roleIds = collect($roleNames)
            ->filter()
            ->map(fn ($name) => Role::firstOrCreate(['name' => trim($name)])->id)
            ->values()
            ->all();

        $this->roles()->sync($roleIds);
    }

    public function givePermission(string $permissionCode): void
    {
        $permission = Permission::where('code', $permissionCode)->firstOrFail();

        $this->permissions()->syncWithoutDetaching([
            $permission->id => ['is_denied' => false],
        ]);

        $this->permissions()->updateExistingPivot($permission->id, [
            'is_denied' => false,
        ]);
    }

    public function denyPermission(string $permissionCode): void
    {
        $permission = Permission::where('code', $permissionCode)->firstOrFail();

        $this->permissions()->syncWithoutDetaching([
            $permission->id => ['is_denied' => true],
        ]);

        $this->permissions()->updateExistingPivot($permission->id, [
            'is_denied' => true,
        ]);
    }

    public function revokeDirectPermission(string $permissionCode): void
    {
        $permission = Permission::where('code', $permissionCode)->first();

        if (! $permission) {
            return;
        }

        $this->permissions()->detach($permission->id);
    }

    public function scopeDashboardUsers($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->whereIn('name', ['owner', 'admin', 'employee']));
    }

    public function isDashboardUser(): bool
    {
        return $this->isOwner()
            || $this->isAdmin()
            || $this->isEmployee();
    }

    /* =========================
     * Catalog
     * ========================= */

    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    /* =========================
     * Customer
     * ========================= */

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function abandonedCartRecoveries()
    {
        return $this->hasMany(AbandonedCartRecovery::class);
    }

    /* =========================
     * Orders / Payments
     * ========================= */

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Order::class,
            'user_id',
            'order_id',
            'id',
            'id'
        );
    }

    /* =========================
     * Returns
     * ========================= */

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'user_id');
    }

    /* =========================
     * Logs
     * ========================= */

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
