<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\Catalog\ProductCatalogService;
use App\Services\Content\PageContentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const PRODUCT_LIMIT = 16;

    private const BLOG_LIMIT = 6;

    public function __construct(
        private readonly ProductCatalogService $catalog,
        private readonly PageContentService $pages,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'category' => ['nullable', 'string', 'max:120'],
            'q'        => ['nullable', 'string', 'max:200'],
            'min'      => ['nullable', 'numeric', 'min:0'],
            'max'      => ['nullable', 'numeric', 'min:0', 'gte:min'],
            'color'    => ['nullable', 'integer', 'min:1'],
            'tag'      => ['nullable', 'integer', 'min:1'],
            'sort'     => ['nullable', 'in:new,price_asc,price_desc'],
        ]);

        $attributes = $this->catalog->filterAttributes();
        $home = $this->pages->forSlug('home');
        $about = $this->pages->forSlug('about');

        return view('site.home', [
            'categories'         => $this->catalog->topCategories(),
            'products'           => $this->catalog->listing($filters, $attributes, self::PRODUCT_LIMIT),
            'wishlistedProducts' => $this->catalog->wishlistedProductIds($request->user()),
            'colorValues'        => $this->catalog->attributeValues($attributes['color']?->id),
            'tagValues'          => $this->catalog->attributeValues($attributes['tag']?->id),
            'priceRanges'        => $this->priceRanges(),
            'blogs'              => $this->latestBlogs(),
            'homePageTitle'      => $home['title'],
            'homeContent'        => $home['content'],
            'pageTitle'          => $about['title'],
            'aboutContent'       => $about['content'],
        ]);
    }

    private function latestBlogs()
    {
        $fallback = asset('images/default-blog.jpg');

        return Blog::query()
            ->published()
            ->with('category')
            ->orderByDesc('published_at')
            ->latest('id')
            ->take(self::BLOG_LIMIT)
            ->get()
            ->each(fn (Blog $blog) => $blog->image_url = $this->imageUrl($blog->image, $fallback));
    }

    private function imageUrl(?string $path, string $fallback): string
    {
        $path = trim((string) $path);

        return match (true) {
            $path === ''                       => $fallback,
            str_starts_with($path, 'http')     => $path,
            str_starts_with($path, 'storage/') => asset($path),
            default                            => media_url($path),
        };
    }

    /**
     * @return list<array{label: string, min: ?int, max: ?int}>
     */
    private function priceRanges(): array
    {
        $currency = __('SAR');

        return [
            ['label' => __('All'), 'min' => null, 'max' => null],
            ['label' => "0 - 100 {$currency}", 'min' => 0, 'max' => 100],
            ['label' => "100 - 250 {$currency}", 'min' => 100, 'max' => 250],
            ['label' => "250 - 500 {$currency}", 'min' => 250, 'max' => 500],
            ['label' => "500 - 1000 {$currency}", 'min' => 500, 'max' => 1000],
            ['label' => "1000+ {$currency}", 'min' => 1000, 'max' => null],
        ];
    }
}
