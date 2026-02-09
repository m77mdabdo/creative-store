<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleUser extends Pivot
{

      use HasFactory;

    protected $table = 'role_user';
    public $timestamps = false;
    protected $fillable = ['user_id','role_id'];
}
