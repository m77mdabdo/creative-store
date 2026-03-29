<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPermission extends Pivot
{
    use HasFactory;

    protected $table = 'user_permissions';

    protected $fillable = [
        'user_id',
        'permission_id',
        'is_denied',
    ];

    protected $casts = [
        'is_denied' => 'boolean',
    ];
}
