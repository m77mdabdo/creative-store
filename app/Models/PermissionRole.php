<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionRole extends Pivot
{

      use HasFactory;


    protected $table = 'permission_role';
    public $timestamps = false;
    protected $fillable = ['role_id','permission_id'];
}
