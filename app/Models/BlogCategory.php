<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Traits\Auditable;

class BlogCategory extends Model
{

    use Auditable ;
    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $category) {
            if (blank($category->slug) && filled($category->name)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function (self $category) {
            if (blank($category->slug) && filled($category->name)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'blog_category_id');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('slug', 'like', "%{$term}%");
        });
    }
}
