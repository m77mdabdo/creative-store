<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use App\Traits\Auditable;


class Page extends Model
{
    use HasFactory , Auditable ;

    protected $fillable = [
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function translation(?string $locale = null): ?PageTranslation
    {
        $locale ??= app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        $translations = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->get();

        return $translations->firstWhere('locale', $locale)
            ?? $translations->firstWhere('locale', $fallback);
    }

    public function getTitleAttribute(): ?string
    {
        return $this->translation()?->title;
    }

    public function getContentAttribute(): ?string
    {
        return $this->translation()?->content;
    }

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
        $locales = array_values(array_unique([
            app()->getLocale(),
            config('app.fallback_locale', 'en'),
            'ar',
            'en',
        ]));

        foreach ($locales as $locale) {
            Cache::forget("page.{$slug}.{$locale}");
        }
    }
}
