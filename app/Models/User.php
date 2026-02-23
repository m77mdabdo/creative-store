<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditable;

// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasFactory, Notifiable , Auditable;
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

    // Permissions عبر الـ Roles (Collection من الأدوار مع permissions)
    public function rolesWithPermissions()
    {
        return $this->roles()->with('permissions');
    }

    // هل عنده Role؟
    public function hasRole(string $name): bool
    {
        return $this->roles()->where('name', $name)->exists();
    }

    // هل عنده Permission؟
    public function hasPermission(string $code): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn($q) => $q->where('code', $code))
            ->exists();
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

}
