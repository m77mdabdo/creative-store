<?php

namespace App\Models;

use App\Support\Html\HtmlSanitizer;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use Auditable;

    protected $fillable = [
        'blog_category_id',
        'title',
        'title_ar',
        'slug',
        'slug_ar',
        'image',
        'excerpt',
        'excerpt_ar',
        'content',
        'content_ar',
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
        static::saving(function (self $blog) {
            if (blank($blog->slug) && filled($blog->title)) {
                $blog->slug = static::uniqueSlug($blog->title, $blog->getKey());
            }

            if (blank($blog->author)) {
                $blog->author = 'Admin';
            }
        });
    }

    /**
     * Build a URL-safe slug that keeps Arabic letters and never collides.
     */
    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title, '-', null) ?: Str::lower(Str::random(8));
        $slug = $base;
        $suffix = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    protected function localizedTitle(): Attribute
    {
        return Attribute::get(fn () => $this->localized('title'));
    }

    protected function localizedExcerpt(): Attribute
    {
        return Attribute::get(fn () => $this->localized('excerpt'));
    }

    /**
     * Content cleaned of scripts and unsafe markup, ready for {!! !!}.
     */
    protected function safeContent(): Attribute
    {
        return Attribute::get(fn () => HtmlSanitizer::clean($this->localized('content')));
    }

    private function localized(string $field): ?string
    {
        if (app()->getLocale() === 'ar' && filled($this->{"{$field}_ar"})) {
            return $this->{"{$field}_ar"};
        }

        return $this->{$field};
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class)->latest();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', true)
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    /**
     * Posts readable in the current language: the Arabic site lists only
     * posts that have an Arabic title and body, so no English text shows up
     * under an Arabic layout.
     */
    public function scopeForCurrentLocale(Builder $query): Builder
    {
        if (app()->getLocale() !== 'ar') {
            return $query;
        }

        return $query->whereNotNull('title_ar')->where('title_ar', '!=', '')
            ->whereNotNull('content_ar')->where('content_ar', '!=', '');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('title_ar', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('author', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%");
        });
    }
}
