<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Review extends Model
{
      use HasFactory , Auditable ;

    protected $fillable = [
        'product_id','user_id','rating','title','comment','is_approved'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
