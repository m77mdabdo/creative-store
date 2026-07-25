<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/**
 * XML sitemap with an entry per page per language, cached for an hour and
 * built with lightweight queries (ids, slugs and dates only).
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('seo.sitemap', now()->addHour(), fn () => $this->build());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function build(): string
    {
        $locales = array_keys(LaravelLocalization::getSupportedLocales());
        $entries = [];

        $add = function (string $path, $lastModified = null, string $priority = '0.5') use (&$entries, $locales) {
            foreach ($locales as $locale) {
                $entries[] = [
                    'loc'        => LaravelLocalization::getLocalizedURL($locale, url($path), [], true),
                    'lastmod'    => $lastModified?->toAtomString(),
                    'priority'   => $priority,
                    'alternates' => collect($locales)->mapWithKeys(fn ($alt) => [
                        $alt => LaravelLocalization::getLocalizedURL($alt, url($path), [], true),
                    ])->all(),
                ];
            }
        };

        foreach (['/' => '1.0', '/products' => '0.9', '/blog' => '0.6', '/about' => '0.4', '/contact' => '0.4'] as $path => $priority) {
            $add($path, null, $priority);
        }

        Category::query()->where('is_active', true)->select(['slug', 'updated_at'])
            ->each(fn ($category) => $add('/products?category=' . urlencode($category->slug), $category->updated_at, '0.7'));

        Product::query()->where('status', 'active')->select(['id', 'slug', 'updated_at'])
            ->lazyById(500)
            ->each(fn ($product) => $add('/products/' . $product->slug, $product->updated_at, '0.8'));

        Blog::query()->published()->select(['id', 'slug', 'updated_at'])
            ->lazyById(500)
            ->each(fn ($blog) => $add('/blog/' . $blog->slug, $blog->updated_at, '0.5'));

        Page::query()->where('is_active', true)->whereNotIn('slug', ['home', 'about', 'contact'])->select(['slug', 'updated_at'])
            ->each(fn ($page) => $add('/pages/' . $page->slug, $page->updated_at, '0.3'));

        return view('seo.sitemap', ['entries' => $entries])->render();
    }
}
