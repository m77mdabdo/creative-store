<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class BlogComment extends Model
{
    use Auditable ;
    protected $fillable = [
        'blog_id',
        'name',
        'email',
        'website',
        'comment',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('website', 'like', "%{$term}%")
                ->orWhere('comment', 'like', "%{$term}%");
        });
    }
}
