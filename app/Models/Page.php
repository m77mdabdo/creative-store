<?php

namespace App\Models;

use App\Traits\Auditable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory, Auditable, Translatable;

    public array $translatedAttributes = [
        'title',
        'content',
    ];

    protected $fillable = [
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getDecodedContentAttribute(): array
    {
        $content = $this->content;

        if (! is_string($content) || trim($content) === '') {
            return [];
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('slug', 'like', "%{$term}%")
              ->orWhereHas('translations', function (Builder $t) use ($term) {
                  $t->where('title', 'like', "%{$term}%")
                    ->orWhere('content', 'like', "%{$term}%");
              });
        });
    }

    public function cacheKey(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return "page.{$this->slug}.{$locale}";
    }

    public static function clearCacheBySlug(string $slug): void
    {
        foreach (['ar', 'en'] as $locale) {
            Cache::forget("page.{$slug}.{$locale}");
        }
    }
}
