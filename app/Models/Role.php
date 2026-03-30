<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Role extends Model
{
    use HasFactory , Auditable ;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->using(RoleUser::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->using(PermissionRole::class);
    }
}
