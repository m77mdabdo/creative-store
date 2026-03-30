<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\Auditable;
class Blog extends Model
{
    use Auditable ;
    protected $fillable = [
        'blog_category_id',
        'title',
        'slug',
        'image',
        'excerpt',
        'content',
        'author',
        'is_featured',
        'status',
        'views',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $blog) {
            if (blank($blog->slug) && filled($blog->title)) {
                $blog->slug = Str::slug($blog->title);
            }

            if (blank($blog->author)) {
                $blog->author = 'Admin';
            }
        });

        static::updating(function (self $blog) {
            if (blank($blog->slug) && filled($blog->title)) {
                $blog->slug = Str::slug($blog->title);
            }

            if (blank($blog->author)) {
                $blog->author = 'Admin';
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class)->latest();
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('author', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%");
        });
    }
}
