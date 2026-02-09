<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrivacyRequest extends Model
{

      use HasFactory;


    protected $fillable = [
        'user_id','email','type','status','requested_at','processed_at','note'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
