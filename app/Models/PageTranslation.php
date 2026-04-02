<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageTranslation extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'page_id',
        'locale',
        'title',
        'content',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
