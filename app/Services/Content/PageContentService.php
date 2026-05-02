<?php

namespace App\Services\Content;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;

/**
 * Loads translated CMS page payloads (title + structured content) with caching.
 */
class PageContentService
{
    private const TTL_MINUTES = 30;

    /**
     * @return array{title: string, content: array}
     */
    public function forSlug(string $slug, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $fallback = (string) config('app.fallback_locale');

        return Cache::remember(
            "page.{$slug}.{$locale}",
            now()->addMinutes(self::TTL_MINUTES),
            function () use ($slug, $locale, $fallback) {
                $page = Page::query()
                    ->where('slug', $slug)
                    ->where('is_active', true)
                    ->with('translations')
                    ->first();

                $translation = $page?->translate($locale, true)
                    ?? ($fallback !== $locale ? $page?->translate($fallback, true) : null);

                $content = $translation?->content;

                if (! is_array($content)) {
                    $content = json_decode((string) ($content ?? '{}'), true) ?: [];
                }

                return [
                    'title'   => $translation?->title ?? ucfirst($slug),
                    'content' => $content,
                ];
            }
        );
    }

}
