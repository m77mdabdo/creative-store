<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory;

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

        return $this->translations
            ->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', $fallback);
    }

    public function getTitleAttribute(): ?string
    {
        return $this->translation()?->title;
    }

    public function getContentAttribute(): ?string
    {
        return $this->translation()?->content;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where('slug', 'like', "%{$term}%")
            ->orWhereHas('translations', function (Builder $q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('content', 'like', "%{$term}%");
            });
    }
}
