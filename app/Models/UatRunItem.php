<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;


class UatRunItem extends Model
{
      use HasFactory , Auditable ;

    protected $fillable = [
        'uat_run_id','scenario_code','title','status','evidence'
    ];

    public function run()
    {
        return $this->belongsTo(UatRun::class, 'uat_run_id');
    }
}
