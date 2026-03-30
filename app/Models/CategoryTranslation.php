<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class CategoryTranslation extends Model
{
    use HasFactory , Auditable ;

    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
