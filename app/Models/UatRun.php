<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;


class UatRun extends Model
{

      use HasFactory , Auditable ;

    protected $fillable = [
        'name','environment','status','started_at','finished_at','executed_by','notes'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function executor()
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function items()
    {
        return $this->hasMany(UatRunItem::class);
    }
}
