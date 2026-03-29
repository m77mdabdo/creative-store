<?php

namespace App\Http\Controllers\About;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\PageContent\AboutPageContent;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        $cacheKey = "page.about.{$locale}";

        $payload = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($locale, $fallback) {
            $page = Page::query()
                ->active()
                ->where('slug', 'about')
                ->with([
                    'translations' => function ($query) use ($locale, $fallback) {
                        $query->select('id', 'page_id', 'locale', 'title', 'content')
                              ->whereIn('locale', array_unique([$locale, $fallback]));
                    }
                ])
                ->firstOrFail();

            $translation = $page->translation($locale);
            $content = AboutPageContent::decode($translation?->content);

            return [
                'title' => $translation?->title,
                'content' => $content,
            ];
        });

        return view('site.about.about', [
            'pageTitle' => $payload['title'] ?? 'About',
            'content' => $payload['content'] ?? [],
        ]);
    }
}
