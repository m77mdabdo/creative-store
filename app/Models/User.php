<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditable;


// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasFactory, Notifiable , Auditable ;
    // use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
         'otp',
         'email_verified_at',
         'phone',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* =========================
     * ACL (Roles / Permissions)
     * ========================= */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function rolesWithPermissions()
    {
        return $this->roles()->with('permissions');
    }

    public function hasRole(string $name): bool
    {
        return $this->roles()->where('name', $name)->exists();
    }

    public function hasPermission(string $code): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn($q) => $q->where('code', $code))
            ->exists();
    }

    // ✅ بديل assignRole بتاع Spatie
    public function assignRole(string $roleName): void
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    // ✅ لو عايز تستبدل كل الرولات (مش إضافة)
    public function syncRoles(array $roleNames): void
    {
        $roleIds = collect($roleNames)->map(function ($name) {
            return Role::firstOrCreate(['name' => $name])->id;
        })->all();

        $this->roles()->sync($roleIds);
    }



    /* باقي العلاقات زي ما هي عندك */
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

    /* =========================
     * Orders / Payments
     * ========================= */

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Payments من خلال Orders
    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Order::class,
            'user_id',   // FK on orders table
            'order_id',  // FK on payments table
            'id',        // local key on users
            'id'         // local key on orders
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

    public function isDashboardUser(): bool
{
    return $this->hasRole('owner')
        || $this->hasRole('admin')
        || $this->hasRole('employee');
}

public function abandonedCartRecoveries()
{
    return $this->hasMany(AbandonedCartRecovery::class);
}

}
