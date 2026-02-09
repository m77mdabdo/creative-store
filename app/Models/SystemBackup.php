<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemBackup extends Model
{
      use HasFactory;

    protected $fillable = [
        'type','file_path','size_bytes','status','started_at','finished_at','note'
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
