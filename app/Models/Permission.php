<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'group',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role')
            ->using(PermissionRole::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions')
            ->using(UserPermission::class)
            ->withPivot('is_denied')
            ->withTimestamps();
    }
}
