<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;


class RoleUser extends Pivot
{

      use HasFactory , Auditable;

    protected $table = 'role_user';
    public $timestamps = false;
    protected $fillable = ['user_id','role_id'];
}
